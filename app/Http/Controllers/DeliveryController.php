<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
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
        
        $pendingCount = $pendingTransfers->count();
        
        return view('delivery.index', compact('pendingTransfers', 'completedTransfers', 'pendingCount'));
    }

    public function receive($id)
    {
        try {
            DB::beginTransaction();

            $transfer = Transfer::findOrFail($id);
            
            // Check if user is staff of the receiving branch
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

            // Add to branch stock
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

            // Update transfer status
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

                    // Add to branch stock
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