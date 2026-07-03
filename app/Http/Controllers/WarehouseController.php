<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Branch;
use App\Models\Transfer;
use App\Models\WarehouseStock;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WarehouseController extends Controller
{
    public function index()
    {
        $items = Item::with('warehouseStock')->get();
        $branches = Branch::all();
        $suppliers = Supplier::all();
        return view('warehouse.index', compact('items', 'branches', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'weight_per_unit' => 'nullable|numeric|min:0',
            'weight_unit' => 'nullable|string|max:50',
            'min_stock_alert' => 'required|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'initial_stock' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $weightPerUnit = null;
            if ($request->weight_per_unit && $request->weight_unit) {
                if ($request->weight_unit === 'kg') {
                    $weightPerUnit = $request->weight_per_unit * 1000;
                } elseif ($request->weight_unit === 'g') {
                    $weightPerUnit = $request->weight_per_unit;
                } elseif ($request->weight_unit === 'liters') {
                    $weightPerUnit = $request->weight_per_unit * 1000;
                } elseif ($request->weight_unit === 'ml') {
                    $weightPerUnit = $request->weight_per_unit;
                } else {
                    $weightPerUnit = $request->weight_per_unit;
                }
            }

            $item = Item::create([
                'name' => $request->name,
                'category' => $request->category,
                'unit' => $request->unit,
                'weight_per_unit' => $weightPerUnit,
                'supplier_id' => $request->supplier_id,
            ]);

            WarehouseStock::create([
                'item_id' => $item->id,
                'stock_quantity' => $request->initial_stock,
                'low_stock_threshold' => $request->min_stock_alert,
                'reorder_point' => $request->min_stock_alert * 2,
                'reorder_quantity' => $request->min_stock_alert * 5,
            ]);

            $branches = Branch::all();
            foreach ($branches as $branch) {
                $branch->items()->attach($item->id, [
                    'stock_quantity' => 0,
                    'low_stock_threshold' => $request->min_stock_alert,
                ]);
            }

            DB::commit();

            return redirect()->route('warehouse.index')
                ->with('success', '✅ Item "' . $item->name . '" added to warehouse with ' . $request->initial_stock . ' units!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Warehouse store error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function createTransfer(Request $request)
    {
        try {
            $request->validate([
                'item_id' => 'required|exists:items,id',
                'to_branch_id' => 'required|exists:branches,id',
                'quantity' => 'required|numeric|min:1',
                'notes' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $item = Item::find($request->item_id);
            $branch = Branch::find($request->to_branch_id);
            
            $warehouseStock = WarehouseStock::where('item_id', $item->id)->first();
            
            if (!$warehouseStock || $warehouseStock->stock_quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient warehouse stock. Available: ' . ($warehouseStock->stock_quantity ?? 0)
                ], 400);
            }

            // Deduct from warehouse immediately
            $warehouseStock->stock_quantity -= $request->quantity;
            $warehouseStock->save();

            // Create transfer record with 'pending' status
            $transfer = Transfer::create([
                'item_id' => $request->item_id,
                'to_branch_id' => $request->to_branch_id,
                'quantity' => $request->quantity,
                'type' => 'warehouse_to_branch',
                'status' => 'pending', // Not yet received by branch
                'requested_by' => Auth::id(),
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'notes' => $request->notes,
            ]);

            // DO NOT add to branch stock yet - waiting for staff confirmation

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Transfer created! Waiting for branch staff to confirm receipt.',
                'transfer_id' => $transfer->id,
                'status' => 'pending',
                'warehouse_remaining' => $warehouseStock->stock_quantity
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transfer error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function receiveTransfer($id)
    {
        try {
            DB::beginTransaction();

            $transfer = Transfer::findOrFail($id);
            
            if ($transfer->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transfer is already ' . $transfer->status
                ], 400);
            }

            // Add to branch stock
            $branchItem = DB::table('branch_item')
                ->where('branch_id', $transfer->to_branch_id)
                ->where('item_id', $transfer->item_id)
                ->first();

            if ($branchItem) {
                DB::table('branch_item')
                    ->where('id', $branchItem->id)
                    ->update([
                        'stock_quantity' => $branchItem->stock_quantity + $transfer->quantity,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('branch_item')->insert([
                    'branch_id' => $transfer->to_branch_id,
                    'item_id' => $transfer->item_id,
                    'stock_quantity' => $transfer->quantity,
                    'low_stock_threshold' => 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update transfer status
            $transfer->status = 'received';
            $transfer->received_at = now();
            $transfer->received_by = Auth::id();
            $transfer->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Transfer received successfully! Stock added to branch inventory.',
                'transfer_id' => $transfer->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Receive transfer error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function transfers()
    {
        $user = Auth::user();
        
        if ($user->isStaff()) {
            // Staff sees transfers to their branch
            $transfers = Transfer::with(['item', 'fromBranch', 'toBranch', 'requestedBy', 'approvedBy'])
                ->where('to_branch_id', $user->branch_id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Admin sees all transfers
            $transfers = Transfer::with(['item', 'fromBranch', 'toBranch', 'requestedBy', 'approvedBy'])
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('warehouse.transfers', compact('transfers'));
    }

    public function updateStock(Request $request)
    {
        try {
            Log::info('Update stock request received', [
                'item_id' => $request->item_id,
                'stock_quantity' => $request->stock_quantity
            ]);

            $request->validate([
                'item_id' => 'required|exists:items,id',
                'stock_quantity' => 'required|numeric|min:0',
            ]);

            $warehouseStock = WarehouseStock::where('item_id', $request->item_id)->first();
            
            if (!$warehouseStock) {
                Log::warning('Warehouse stock not found, creating new record for item: ' . $request->item_id);
                
                $warehouseStock = WarehouseStock::create([
                    'item_id' => $request->item_id,
                    'stock_quantity' => $request->stock_quantity,
                    'low_stock_threshold' => 5,
                    'reorder_point' => 10,
                    'reorder_quantity' => 50,
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => '✅ Warehouse stock created with ' . $request->stock_quantity . ' units',
                    'new_stock' => $request->stock_quantity
                ]);
            }

            $oldStock = $warehouseStock->stock_quantity;
            $warehouseStock->stock_quantity = $request->stock_quantity;
            $warehouseStock->save();

            Log::info('Stock updated successfully', [
                'item_id' => $request->item_id,
                'old_stock' => $oldStock,
                'new_stock' => $request->stock_quantity
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Stock updated from ' . $oldStock . ' to ' . $request->stock_quantity,
                'new_stock' => $request->stock_quantity
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Update stock error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateThreshold(Request $request)
    {
        try {
            $request->validate([
                'item_id' => 'required|exists:items,id',
                'threshold' => 'required|integer|min:0',
            ]);

            $warehouseStock = WarehouseStock::where('item_id', $request->item_id)->first();
            
            if (!$warehouseStock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found in warehouse.'
                ], 404);
            }

            $warehouseStock->low_stock_threshold = $request->threshold;
            $warehouseStock->save();

            return response()->json([
                'success' => true,
                'message' => '✅ Threshold updated to ' . $request->threshold,
                'new_threshold' => $request->threshold
            ]);

        } catch (\Exception $e) {
            Log::error('Update threshold error: ' . $e->getMessage());
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
                'deleted_count' => $deletedCount
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

    public function approveTransfer($id)
    {
        // Keep for backward compatibility
        return $this->receiveTransfer($id);
    }
}
