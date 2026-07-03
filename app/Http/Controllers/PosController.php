<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Order;
use App\Models\Item;
use App\Helpers\UnitConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin() || $user->isManager()) {
            $branches = Branch::where('is_active', true)->get();
            $branchId = request('branch_id', $branches->first()->id ?? null);
        } else {
            $branches = collect([$user->branch]);
            $branchId = $user->branch_id;
        }

        $products = Product::with(['recipes.item'])->get();
        
        $products = $products->map(function($product) use ($branchId) {
            $canMake = true;
            $stockInfo = [];
            
            if ($product->recipes->isEmpty()) {
                $canMake = false;
            } else {
                foreach ($product->recipes as $recipe) {
                    $stock = DB::table('branch_item')
                        ->where('branch_id', $branchId)
                        ->where('item_id', $recipe->item_id)
                        ->value('stock_quantity') ?? 0;
                    
                    $needed = $recipe->quantity;
                    $stockUnit = $recipe->item->unit ?? 'pcs';
                    $recipeUnit = $recipe->unit;
                    $itemName = $recipe->item->name ?? 'Unknown';
                    $itemId = $recipe->item_id;
                    
                    $stockInRecipeUnit = UnitConverter::convert(
                        $stock,
                        $stockUnit,
                        $recipeUnit,
                        $itemName,
                        $itemId
                    );
                    
                    $available = $stockInRecipeUnit >= $needed;
                    
                    $stockInfo[] = [
                        'item_id' => $recipe->item_id,
                        'item_name' => $itemName,
                        'stock' => $stock,
                        'stock_unit' => $stockUnit,
                        'stock_in_recipe_unit' => $stockInRecipeUnit,
                        'needed' => $needed,
                        'needed_unit' => $recipeUnit,
                        'available' => $available
                    ];
                    
                    if (!$available) {
                        $canMake = false;
                    }
                }
            }
            
            $product->can_make = $canMake;
            $product->stock_info = $stockInfo;
            return $product;
        });
        
        $customers = Customer::all();
        $offlineMode = session('offline_mode', false);

        return view('pos.index', compact('branches', 'products', 'customers', 'offlineMode', 'branchId'));
    }

    public function processSale(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'customer_id' => 'nullable|exists:customers,id',
            'walkin_name' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $branchId = $request->branch_id;

        if (!$user->isAdmin() && $user->branch_id != $branchId) {
            return response()->json(['error' => 'Unauthorized branch access.'], 403);
        }

        $offlineMode = session('offline_mode', false);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $orderItems = [];
            $ingredientsToDeduct = [];

            // FIRST PASS: Check all stocks first (no deductions yet)
            foreach ($request->items as $item) {
                $product = Product::with('recipes.item')->find($item['product_id']);
                
                if ($product->recipes->isEmpty()) {
                    throw new \Exception('Product "' . $product->name . '" has no recipe.');
                }

                // Check each ingredient
                foreach ($product->recipes as $recipe) {
                    $itemId = $recipe->item_id;
                    $recipeQty = $recipe->quantity;
                    $recipeUnit = $recipe->unit;
                    $quantityOrdered = $item['quantity'];
                    
                    $itemModel = Item::find($itemId);
                    $stockUnit = $itemModel->unit ?? 'g';
                    $itemName = $itemModel->name ?? 'Unknown';
                    $weightPerUnit = $itemModel->weight_per_unit ?? null;
                    
                    $neededInRecipeUnit = $recipeQty * $quantityOrdered;
                    
                    // Get current branch stock BEFORE deduction
                    $branchStock = DB::table('branch_item')
                        ->where('branch_id', $branchId)
                        ->where('item_id', $itemId)
                        ->lockForUpdate()
                        ->first();

                    $availableStock = $branchStock->stock_quantity ?? 0;
                    
                    // Convert available stock to recipe unit for comparison
                    $availableInRecipeUnit = UnitConverter::convert(
                        $availableStock,
                        $stockUnit,
                        $recipeUnit,
                        $itemName,
                        $itemId
                    );

                    // Check if enough stock (in recipe unit)
                    if ($availableInRecipeUnit < $neededInRecipeUnit) {
                        $errorMsg = 'Insufficient stock for "' . $itemName . '". ';
                        $errorMsg .= 'Need: ' . number_format($neededInRecipeUnit, 2) . ' ' . $recipeUnit;
                        $errorMsg .= ', Available: ' . number_format($availableInRecipeUnit, 2) . ' ' . $recipeUnit;
                        
                        if ($weightPerUnit && $stockUnit !== $recipeUnit) {
                            $errorMsg .= ' (1 ' . $stockUnit . ' = ' . number_format($weightPerUnit) . 'g)';
                        }
                        
                        throw new \Exception($errorMsg);
                    }

                    // Calculate needed in stock unit for deduction
                    $neededInStockUnit = UnitConverter::convert(
                        $neededInRecipeUnit,
                        $recipeUnit,
                        $stockUnit,
                        $itemName,
                        $itemId
                    );

                    // Track ingredients to deduct
                    if (!isset($ingredientsToDeduct[$itemId])) {
                        $ingredientsToDeduct[$itemId] = 0;
                    }
                    $ingredientsToDeduct[$itemId] += $neededInStockUnit;
                }

                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            // SECOND PASS: Now deduct all ingredients
            foreach ($ingredientsToDeduct as $itemId => $totalQty) {
                // Check again before deducting (double check)
                $currentStock = DB::table('branch_item')
                    ->where('branch_id', $branchId)
                    ->where('item_id', $itemId)
                    ->lockForUpdate()
                    ->first();

                if (!$currentStock || $currentStock->stock_quantity < $totalQty) {
                    $itemName = Item::find($itemId)->name ?? 'Unknown';
                    throw new \Exception('Stock changed for "' . $itemName . '". Please try again.');
                }

                // Deduct
                $newStock = $currentStock->stock_quantity - $totalQty;
                
                // Ensure we never go negative
                if ($newStock < 0) {
                    $itemName = Item::find($itemId)->name ?? 'Unknown';
                    throw new \Exception('Cannot deduct more than available stock for "' . $itemName . '".');
                }

                DB::table('branch_item')
                    ->where('branch_id', $branchId)
                    ->where('item_id', $itemId)
                    ->update(['stock_quantity' => $newStock]);
            }

            // Create Sale with proper sync_status based on offline mode
            $sale = Sale::create([
                'branch_id' => $branchId,
                'user_id' => $user->id,
                'customer_id' => $request->customer_id,
                'walkin_name' => $request->walkin_name ?? null,
                'total_amount' => $totalAmount,
                'sale_date' => now(),
                'sync_status' => $offlineMode ? 'pending' : 'synced',
            ]);

            // Create Sale Items and Orders
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
                    'notes' => $request->notes ?? ($offlineMode ? '📡 OFFLINE ORDER' : null),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'total' => $totalAmount,
                'offline' => $offlineMode,
                'message' => $offlineMode 
                    ? '✅ Order saved offline! Will sync when online.' 
                    : '✅ Order placed successfully!',
                'sync_status' => $sale->sync_status,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getProductStock(Request $request)
    {
        $productId = $request->product_id;
        $branchId = $request->branch_id;
        
        if (!$branchId) {
            $user = Auth::user();
            $branchId = $user->branch_id;
        }
        
        $product = Product::with('recipes.item')->find($productId);
        
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        
        $stockInfo = [];
        $canMake = true;
        
        foreach ($product->recipes as $recipe) {
            $itemModel = Item::find($recipe->item_id);
            $stockUnit = $itemModel->unit ?? 'g';
            $itemName = $itemModel->name ?? 'Unknown';
            $itemId = $recipe->item_id;
            $weightPerUnit = $itemModel->weight_per_unit ?? null;
            
            $stock = DB::table('branch_item')
                ->where('branch_id', $branchId)
                ->where('item_id', $itemId)
                ->value('stock_quantity') ?? 0;
            
            $needed = $recipe->quantity;
            $neededUnit = $recipe->unit;
            
            $stockInRecipeUnit = UnitConverter::convert(
                $stock,
                $stockUnit,
                $neededUnit,
                $itemName,
                $itemId
            );
            
            $available = $stockInRecipeUnit >= $needed;
            
            $stockInfo[] = [
                'item_name' => $itemName,
                'stock' => $stock,
                'stock_unit' => $stockUnit,
                'stock_in_recipe_unit' => $stockInRecipeUnit,
                'needed' => $needed,
                'needed_unit' => $neededUnit,
                'weight_per_unit' => $weightPerUnit,
                'available' => $available
            ];
            
            if (!$available) {
                $canMake = false;
            }
        }
        
        return response()->json([
            'product' => $product,
            'can_make' => $canMake,
            'stock_info' => $stockInfo
        ]);
    }

    public function receipt($saleId)
    {
        $sale = Sale::with(['items.product', 'branch', 'user', 'customer'])
            ->findOrFail($saleId);
        
        return view('pos.receipt', compact('sale'));
    }
}