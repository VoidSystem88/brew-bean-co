<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Item;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isStaff = $user->isStaff();
        
        // Get all branches
        $branches = Branch::all();
        
        if ($branches->isEmpty()) {
            return redirect()->route('branches.index')
                ->with('error', '⚠️ No branches found! Please create a branch first.');
        }
        
        // For staff: force their assigned branch
        if ($isStaff) {
            if (!$user->branch_id) {
                return redirect()->route('staff.dashboard')
                    ->with('error', '⚠️ You are not assigned to any branch.');
            }
            
            $branchId = $user->branch_id;
            $branch = Branch::findOrFail($branchId);
            
            // Staff only sees branch stock, NOT warehouse stock
            $items = Item::with(['branches' => function($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            }])->get(); // Removed 'warehouseStock' from eager loading
            
            $lowStockItems = $this->getLowStockItems($branchId);
            $suppliers = Supplier::all();
            $products = Product::all();
            
            $canManage = false;
            
            $criticalCount = DB::table('branch_item')
                ->where('branch_id', $branchId)
                ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                ->count();
            
            return view('inventory.index', compact('branch', 'branches', 'items', 'lowStockItems', 'canManage', 'isStaff', 'suppliers', 'products', 'criticalCount'));
        }
        
        // For admin/manager: show branch selector and warehouse stock
        $branchId = $request->branch_id ?? $branches->first()->id;
        $branch = Branch::findOrFail($branchId);
        
        // Admin/Manager sees both branch stock and warehouse stock
        $items = Item::with(['branches' => function($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        }, 'warehouseStock'])->get();
        
        $lowStockItems = $this->getLowStockItems($branchId);
        $suppliers = Supplier::all();
        $products = Product::all();
        
        $canManage = $user->isAdmin() || $user->isManager();
        
        $criticalCount = DB::table('branch_item')
            ->where('branch_id', $branchId)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();
        
        return view('inventory.index', compact('branch', 'branches', 'items', 'lowStockItems', 'canManage', 'isStaff', 'suppliers', 'products', 'criticalCount'));
    }

    private function getLowStockItems($branchId)
    {
        return DB::table('branch_item')
            ->join('items', 'branch_item.item_id', '=', 'items.id')
            ->where('branch_item.branch_id', $branchId)
            ->whereColumn('branch_item.stock_quantity', '<=', 'branch_item.low_stock_threshold')
            ->select('items.*', 'branch_item.stock_quantity as stock', 'branch_item.low_stock_threshold as threshold')
            ->get();
    }

    public function quickTransfer(Request $request)
    {
        try {
            $request->validate([
                'item_id' => 'required|exists:items,id',
                'branch_id' => 'required|exists:branches,id',
                'quantity' => 'required|numeric|min:1',
            ]);

            DB::beginTransaction();

            $item = Item::find($request->item_id);
            $branch = Branch::find($request->branch_id);
            
            $warehouseStock = WarehouseStock::where('item_id', $item->id)->first();
            
            if (!$warehouseStock || $warehouseStock->stock_quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient warehouse stock. Available: ' . ($warehouseStock->stock_quantity ?? 0)
                ], 400);
            }

            $warehouseStock->stock_quantity -= $request->quantity;
            $warehouseStock->save();

            $transfer = Transfer::create([
                'item_id' => $request->item_id,
                'to_branch_id' => $request->branch_id,
                'quantity' => $request->quantity,
                'type' => 'warehouse_to_branch',
                'status' => 'pending',
                'requested_by' => Auth::id(),
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'notes' => 'Quick transfer from inventory page',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Transfer created! Waiting for branch staff to confirm receipt.',
                'transfer_id' => $transfer->id,
                'warehouse_remaining' => $warehouseStock->stock_quantity
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quick transfer error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'initial_stock' => 'required|numeric|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $item = Item::create([
                'name' => $request->name,
                'category' => $request->category,
                'unit' => $request->unit,
                'supplier_id' => $request->supplier_id,
                'min_stock_alert' => $request->low_stock_threshold,
            ]);

            WarehouseStock::create([
                'item_id' => $item->id,
                'stock_quantity' => $request->initial_stock,
                'low_stock_threshold' => $request->low_stock_threshold,
                'reorder_point' => $request->low_stock_threshold * 2,
                'reorder_quantity' => $request->low_stock_threshold * 5,
            ]);

            $branches = Branch::all();
            foreach ($branches as $branch) {
                $branch->items()->attach($item->id, [
                    'stock_quantity' => 0,
                    'low_stock_threshold' => $request->low_stock_threshold,
                ]);
            }

            DB::commit();

            return redirect()->route('inventory.index')
                ->with('success', '✅ Item "' . $item->name . '" added to inventory with ' . $request->initial_stock . ' units in warehouse!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Inventory store error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $item = Item::findOrFail($id);
            $itemName = $item->name;

            $recipeCount = DB::table('recipe_ingredients')
                ->where('item_id', $id)
                ->count();

            if ($recipeCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete "' . $itemName . '" because it is used in ' . $recipeCount . ' recipe(s).'
                ], 400);
            }

            WarehouseStock::where('item_id', $id)->delete();
            $item->branches()->detach();
            $item->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Item "' . $itemName . '" deleted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Inventory delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function deleteMultiple(Request $request)
    {
        try {
            $request->validate([
                'item_ids' => 'required|array',
                'item_ids.*' => 'exists:items,id'
            ]);

            $itemIds = $request->item_ids;
            $deletedCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($itemIds as $itemId) {
                try {
                    $item = Item::find($itemId);
                    if ($item) {
                        $recipeCount = DB::table('recipe_ingredients')
                            ->where('item_id', $itemId)
                            ->count();

                        if ($recipeCount > 0) {
                            $errors[] = 'Cannot delete "' . $item->name . '" because it is used in ' . $recipeCount . ' recipe(s).';
                            continue;
                        }

                        WarehouseStock::where('item_id', $itemId)->delete();
                        $item->branches()->detach();
                        $item->delete();
                        $deletedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = 'Failed to delete item ID ' . $itemId . ': ' . $e->getMessage();
                }
            }

            DB::commit();

            $message = '✅ ' . $deletedCount . ' item(s) deleted successfully.';
            if (!empty($errors)) {
                $message .= ' Errors: ' . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete multiple error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function getLowStock()
    {
        $user = Auth::user();
        $alerts = [];
        
        if ($user->isAdmin()) {
            $alerts = DB::table('branch_item')
                ->join('items', 'branch_item.item_id', '=', 'items.id')
                ->join('branches', 'branch_item.branch_id', '=', 'branches.id')
                ->whereColumn('branch_item.stock_quantity', '<=', 'branch_item.low_stock_threshold')
                ->select('items.name as item', 'branches.name as branch', 'branch_item.stock_quantity as stock')
                ->limit(20)
                ->get();
        } elseif ($user->isStaff()) {
            $alerts = DB::table('branch_item')
                ->join('items', 'branch_item.item_id', '=', 'items.id')
                ->join('branches', 'branch_item.branch_id', '=', 'branches.id')
                ->whereColumn('branch_item.stock_quantity', '<=', 'branch_item.low_stock_threshold')
                ->where('branch_item.branch_id', $user->branch_id)
                ->select('items.name as item', 'branches.name as branch', 'branch_item.stock_quantity as stock')
                ->limit(20)
                ->get();
        } else {
            $alerts = DB::table('branch_item')
                ->join('items', 'branch_item.item_id', '=', 'items.id')
                ->join('branches', 'branch_item.branch_id', '=', 'branches.id')
                ->whereColumn('branch_item.stock_quantity', '<=', 'branch_item.low_stock_threshold')
                ->select('items.name as item', 'branches.name as branch', 'branch_item.stock_quantity as stock')
                ->limit(20)
                ->get();
        }
        
        return response()->json([
            'success' => true,
            'data' => $alerts
        ]);
    }
}