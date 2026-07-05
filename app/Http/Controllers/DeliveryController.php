<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Transfer;
use App\Models\Branch;
use App\Models\User;
use App\Models\DeliveryTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    public function getRiders()
    {
        try {
            $riders = User::where('role', 'delivery')
                ->where('is_active', true)
                ->select('id', 'name', 'email')
                ->get();
            
            return response()->json([
                'success' => true,
                'riders' => $riders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function index()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;
        
        // Get pending transfers for staff's branch
        $pendingTransfers = Transfer::with(['item', 'fromBranch', 'toBranch', 'requestedBy'])
            ->where('to_branch_id', $branchId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get completed transfers for history
        $completedTransfers = Transfer::with(['item', 'fromBranch', 'toBranch', 'requestedBy', 'receivedBy'])
            ->where('to_branch_id', $branchId)
            ->where('status', 'received')
            ->orderBy('received_at', 'desc')
            ->limit(20)
            ->get();
        
        // Get delivery orders
        $deliveryOrders = Sale::with(['customer', 'branch', 'deliveryPerson'])
            ->where('delivery_address', '!=', null)
            ->where('delivery_status', '!=', 'completed')
            ->where('delivery_status', '!=', 'cancelled')
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Get completed deliveries
        $completedDeliveries = Sale::with(['customer', 'branch', 'deliveryPerson'])
            ->where('delivery_address', '!=', null)
            ->where('delivery_status', 'completed')
            ->orderBy('delivery_completed_at', 'desc')
            ->limit(20)
            ->get();
        
        $pendingCount = $pendingTransfers->count();
        $deliveryCount = $deliveryOrders->count();
        
        // Get available delivery staff - ONLY delivery role
        $deliveryStaff = User::where('role', 'delivery')
            ->where('is_active', true)
            ->get();
        
        return view('delivery.index', compact(
            'pendingTransfers', 
            'completedTransfers', 
            'pendingCount',
            'deliveryOrders',
            'completedDeliveries',
            'deliveryCount',
            'deliveryStaff'
        ));
    }

    public function assignDeliveryPerson(Request $request)
    {
        try {
            $request->validate([
                'sale_id' => 'required|exists:sales,id',
                'delivery_person_id' => 'required|exists:users,id'
            ]);

            // Verify that the selected user is a delivery rider
            $deliveryPerson = User::where('id', $request->delivery_person_id)
                ->where('role', 'delivery')
                ->first();

            if (!$deliveryPerson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected user is not a delivery rider.'
                ], 400);
            }

            $sale = Sale::findOrFail($request->sale_id);
            $sale->delivery_person_id = $request->delivery_person_id;
            $sale->delivery_assigned_at = now();
            $sale->delivery_status = 'assigned';
            $sale->save();

            // Create tracking record
            DeliveryTracking::create([
                'sale_id' => $sale->id,
                'user_id' => $request->delivery_person_id,
                'status' => 'assigned',
                'notes' => 'Delivery assigned to ' . $deliveryPerson->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Delivery assigned to ' . $deliveryPerson->name . '!'
            ]);

        } catch (\Exception $e) {
            Log::error('Assign delivery error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function markPickedUp($saleId)
    {
        try {
            $sale = Sale::findOrFail($saleId);
            $sale->delivery_picked_up_at = now();
            $sale->delivery_status = 'picked_up';
            $sale->save();

            DeliveryTracking::create([
                'sale_id' => $sale->id,
                'user_id' => Auth::id(),
                'status' => 'picked_up',
                'notes' => 'Order picked up for delivery'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order marked as picked up!'
            ]);

        } catch (\Exception $e) {
            Log::error('Pick up error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function markInTransit($saleId)
    {
        try {
            $sale = Sale::findOrFail($saleId);
            $sale->delivery_status = 'in_transit';
            $sale->save();

            DeliveryTracking::create([
                'sale_id' => $sale->id,
                'user_id' => Auth::id(),
                'status' => 'in_transit',
                'notes' => 'Order is on the way'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order marked as in transit!'
            ]);

        } catch (\Exception $e) {
            Log::error('In transit error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function confirmDelivery($saleId)
    {
        try {
            DB::beginTransaction();

            $sale = Sale::findOrFail($saleId);
            $sale->delivery_status = 'completed';
            $sale->delivery_completed_at = now();
            $sale->save();

            foreach ($sale->orders as $order) {
                if ($order->status !== 'completed') {
                    $order->status = 'completed';
                    $order->save();
                }
            }
            $sale->order_status = 'completed';

            DeliveryTracking::create([
                'sale_id' => $sale->id,
                'user_id' => Auth::id(),
                'status' => 'delivered',
                'notes' => 'Order successfully delivered'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Delivery confirmed! Order completed.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delivery confirmation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function markDeliveryFailed($saleId, Request $request)
    {
        try {
            $sale = Sale::findOrFail($saleId);
            $sale->delivery_status = 'failed';
            $sale->delivery_notes = $request->reason ?? 'Delivery failed';
            $sale->save();

            DeliveryTracking::create([
                'sale_id' => $sale->id,
                'user_id' => Auth::id(),
                'status' => 'failed',
                'notes' => $request->reason ?? 'Delivery failed'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Delivery marked as failed'
            ]);

        } catch (\Exception $e) {
            Log::error('Delivery failed error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function getTracking($saleId)
    {
        $tracking = DeliveryTracking::where('sale_id', $saleId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'tracking' => $tracking
        ]);
    }

    public function receive($id)
    {
        try {
            DB::beginTransaction();

            $transfer = Transfer::findOrFail($id);
            
            $user = Auth::user();
            if ($user->isStaff() && $transfer->to_branch_id != $user->branch_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to receive this transfer.'
                ], 403);
            }

            if ($transfer->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transfer is already ' . $transfer->status
                ], 400);
            }

            $branchItem = DB::table('branch_item')
                ->where('branch_id', $transfer->to_branch_id)
                ->where('item_id', $transfer->item_id)
                ->first();

            if ($branchItem) {
                $newStock = $branchItem->stock_quantity + $transfer->quantity;
                DB::table('branch_item')
                    ->where('id', $branchItem->id)
                    ->update([
                        'stock_quantity' => $newStock,
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

            $transfer->status = 'received';
            $transfer->received_at = now();
            $transfer->received_by = Auth::id();
            $transfer->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Delivery received successfully! Stock added to branch inventory.',
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

    public function bulkReceive(Request $request)
    {
        try {
            $request->validate([
                'transfer_ids' => 'required|array',
                'transfer_ids.*' => 'exists:transfers,id'
            ]);

            DB::beginTransaction();

            $user = Auth::user();
            $receivedCount = 0;
            $errors = [];

            foreach ($request->transfer_ids as $transferId) {
                try {
                    $transfer = Transfer::findOrFail($transferId);
                    
                    if ($user->isStaff() && $transfer->to_branch_id != $user->branch_id) {
                        continue;
                    }

                    if ($transfer->status !== 'pending') {
                        continue;
                    }

                    $branchItem = DB::table('branch_item')
                        ->where('branch_id', $transfer->to_branch_id)
                        ->where('item_id', $transfer->item_id)
                        ->first();

                    if ($branchItem) {
                        $newStock = $branchItem->stock_quantity + $transfer->quantity;
                        DB::table('branch_item')
                            ->where('id', $branchItem->id)
                            ->update([
                                'stock_quantity' => $newStock,
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

                    $transfer->status = 'received';
                    $transfer->received_at = now();
                    $transfer->received_by = Auth::id();
                    $transfer->save();

                    $receivedCount++;

                } catch (\Exception $e) {
                    $errors[] = 'Transfer #' . $transferId . ': ' . $e->getMessage();
                }
            }

            DB::commit();

            $message = '✅ ' . $receivedCount . ' delivery(s) received successfully!';
            if (!empty($errors)) {
                $message .= ' Errors: ' . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'received_count' => $receivedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk receive error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }
}