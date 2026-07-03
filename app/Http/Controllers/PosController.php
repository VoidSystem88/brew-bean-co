<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\Item;
use App\Helpers\UnitConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosController extends Controller
{
    public function index()
{
    $products = Product::with('recipes.item')->orderBy('name')->get();
    $customers = Customer::orderBy('name')->get();
    $branches = Branch::where('is_active', true)->get();

    // Gamitin ang assigned branch ng naka-login na staff, hindi basta ang unang branch sa listahan.
    // Mag-fallback lang sa first branch kung walang branch_id ang user (hal. admin).
    $currentBranchId = Auth::user()->branch_id ?? $branches->first()->id ?? null;

    return view('pos.index', compact('products', 'customers', 'branches', 'currentBranchId'));
}

    public function processSale(Request $request)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'customer_id' => 'nullable|exists:customers,id',
                'walkin_name' => 'nullable|string|max:255',
                'branch_id' => 'required|exists:branches,id',
                'payment_method' => 'required|in:cash,card,gcash',
                'amount_paid' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $branchId = $request->branch_id;
            $totalAmount = 0;
            $orderItems = [];
            $ingredientsUsed = [];

            foreach ($request->items as $item) {
                $product = Product::with('recipes.item')->find($item['product_id']);
                
                if (!$product) {
                    throw new \Exception('Product not found: ' . $item['product_id']);
                }

                if ($product->recipes->isEmpty()) {
                    throw new \Exception('Product "' . $product->name . '" has no recipe defined.');
                }

                foreach ($product->recipes as $recipe) {
                    $itemId = $recipe->item_id;
                    $recipeQty = $recipe->quantity;
                    $recipeUnit = $recipe->unit;
                    $quantityOrdered = $item['quantity'];
                    
                    $itemModel = Item::find($itemId);
                    $stockUnit = $itemModel->unit ?? 'g';
                    $itemName = $itemModel->name ?? 'Unknown';
                    
                    $neededInRecipeUnit = $recipeQty * $quantityOrdered;
                    
                    $branchStock = DB::table('branch_item')
                        ->where('branch_id', $branchId)
                        ->where('item_id', $itemId)
                        ->first();

                    $availableStock = $branchStock->stock_quantity ?? 0;
                    
                    $availableInRecipeUnit = UnitConverter::convert(
                        $availableStock,
                        $stockUnit,
                        $recipeUnit,
                        $itemName,
                        $itemId
                    );

                    if ($availableInRecipeUnit < $neededInRecipeUnit) {
                        throw new \Exception(
                            'Insufficient stock for "' . $itemName . '". ' .
                            'Need: ' . number_format($neededInRecipeUnit, 2) . ' ' . $recipeUnit . 
                            ', Available: ' . number_format($availableInRecipeUnit, 2) . ' ' . $recipeUnit
                        );
                    }

                    $neededInStockUnit = UnitConverter::convert(
                        $neededInRecipeUnit,
                        $recipeUnit,
                        $stockUnit,
                        $itemName,
                        $itemId
                    );

                    if (!isset($ingredientsUsed[$itemId])) {
                        $ingredientsUsed[$itemId] = 0;
                    }
                    $ingredientsUsed[$itemId] += $neededInStockUnit;
                }

                $subtotal = $item['price'] * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $subtotal,
                ];
            }

            $amountPaid = $request->amount_paid;
            $change = $amountPaid - $totalAmount;

            // Create Sale with status 'pending'
            $sale = Sale::create([
                'branch_id' => $branchId,
                'user_id' => Auth::id(),
                'customer_id' => $request->customer_id,
                'walkin_name' => $request->walkin_name,
                'total_amount' => $totalAmount,
                'original_amount' => $totalAmount,
                'discount_amount' => 0,
                'discount_rate' => 0,
                'sale_date' => now(),
                'sync_status' => 'synced',
                'payment_method' => $request->payment_method,
                'amount_paid' => $amountPaid,
                'change_amount' => $change,
                'order_status' => 'pending',
                'delivery_status' => 'pending',
            ]);

            // Deduct ingredients
            foreach ($ingredientsUsed as $itemId => $totalQty) {
                DB::table('branch_item')
                    ->where('branch_id', $branchId)
                    ->where('item_id', $itemId)
                    ->decrement('stock_quantity', $totalQty);
            }

            // Create Sale Items and Orders with status 'pending'
            foreach ($orderItems as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);

                Order::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'status' => 'pending',
                    'notes' => null,
                ]);
            }

            // Add loyalty points if customer
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
                $pointsEarned = floor($totalAmount / 100);
                $customer->loyalty_points += $pointsEarned;
                $customer->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully! Order is now in queue.',
                'sale_id' => $sale->id,
                'total' => $totalAmount,
                'change' => $change,
                'queue_url' => route('barista.queue')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('POS sale error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function receipt($saleId)
    {
        $sale = Sale::with(['items.product', 'customer', 'branch', 'user'])
            ->where('order_status', 'completed')
            ->findOrFail($saleId);
            
        return view('pos.receipt', compact('sale'));
    }

    public function regenerateReceipt($saleId)
    {
        $sale = Sale::with(['items.product', 'customer', 'branch', 'user'])
            ->where('order_status', 'completed')
            ->findOrFail($saleId);
            
        Log::info('Receipt regenerated', [
            'sale_id' => $saleId,
            'regenerated_by' => Auth::id(),
            'regenerated_at' => now()
        ]);
            
        return view('pos.receipt', compact('sale'));
    }

    public function getProductStock(Request $request)
    {
        $productId = $request->product_id;
        $branchId = $request->branch_id;
        
        $product = Product::with('recipes.item')->find($productId);
        
        if (!$product || $product->recipes->isEmpty()) {
            return response()->json([
                'available' => false,
                'message' => 'Product not available'
            ]);
        }

        $minStock = PHP_INT_MAX;
        $stockDetails = [];
        
        foreach ($product->recipes as $recipe) {
            $branchStock = DB::table('branch_item')
                ->where('branch_id', $branchId)
                ->where('item_id', $recipe->item_id)
                ->first();

            $stockQty = $branchStock->stock_quantity ?? 0;
            $needed = $recipe->quantity;
            $itemName = $recipe->item->name ?? 'Unknown';
            
            $availableInRecipeUnit = UnitConverter::convert(
                $stockQty,
                $recipe->item->unit ?? 'g',
                $recipe->unit,
                $itemName,
                $recipe->item_id
            );
            
            $servingsAvailable = floor($availableInRecipeUnit / $needed);
            
            if ($servingsAvailable < $minStock) {
                $minStock = $servingsAvailable;
            }
            
            $stockDetails[] = [
                'item' => $itemName,
                'available' => $stockQty,
                'needed' => $needed,
                'servings_available' => $servingsAvailable,
                'sufficient' => $availableInRecipeUnit >= $needed
            ];
        }

        return response()->json([
            'available' => $minStock > 0,
            'max_quantity' => $minStock > 0 ? $minStock : 0,
            'details' => $stockDetails
        ]);
    }

    public function searchCustomer(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }
        
        $customers = Customer::where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->orWhere('customer_code', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'email', 'customer_code', 'loyalty_points']);
        
        return response()->json($customers);
    }
}