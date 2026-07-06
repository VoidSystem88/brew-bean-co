<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffNotificationController extends Controller
{
    public function getNotifications()
    {
        try {
            $user = Auth::user();
            $branchId = $user->branch_id;
            
            $notifications = [];
            
            // Get pending deliveries (transfers) for this branch
            $pendingTransfers = Transfer::with(['item', 'fromBranch', 'toBranch'])
                ->where('to_branch_id', $branchId)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($pendingTransfers as $transfer) {
                $notifications[] = [
                    'id' => 'transfer_' . $transfer->id,
                    'type' => 'transfer',
                    'title' => '📦 New Delivery',
                    'message' => $transfer->quantity . ' x ' . ($transfer->item->name ?? 'Unknown') . ' from ' . ($transfer->fromBranch->name ?? 'Warehouse'),
                    'time' => $transfer->created_at->diffForHumans(),
                    'read' => false,
                    'data' => [
                        'transfer_id' => $transfer->id,
                        'item_id' => $transfer->item_id,
                        'quantity' => $transfer->quantity
                    ]
                ];
            }
            
            // Get completed deliveries (transfers received)
            $receivedTransfers = Transfer::with(['item', 'fromBranch', 'toBranch'])
                ->where('to_branch_id', $branchId)
                ->where('status', 'received')
                ->whereDate('received_at', '>=', now()->subDay())
                ->orderBy('received_at', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($receivedTransfers as $transfer) {
                $notifications[] = [
                    'id' => 'received_' . $transfer->id,
                    'type' => 'delivered',
                    'title' => '✅ Delivery Received',
                    'message' => $transfer->quantity . ' x ' . ($transfer->item->name ?? 'Unknown') . ' added to stock',
                    'time' => $transfer->received_at->diffForHumans(),
                    'read' => false,
                    'data' => [
                        'transfer_id' => $transfer->id,
                        'item_id' => $transfer->item_id,
                        'quantity' => $transfer->quantity
                    ]
                ];
            }
            
            // Sort by time (newest first)
            usort($notifications, function($a, $b) {
                return strtotime($b['time']) - strtotime($a['time']);
            });
            
            // Limit to 20
            $notifications = array_slice($notifications, 0, 20);
            
            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'count' => count($notifications)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    public function markAsRead($id)
    {
        try {
            // We don't actually store read status in DB for now
            // Just return success
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    public function markAllRead()
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}