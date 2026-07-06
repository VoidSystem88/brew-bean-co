<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Sale;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderQueueController extends Controller
{
    public function index()
    {
        return view('barista.queue');
    }

    public function getQueueData()
    {
        $user = Auth::user();
        $branchId = $user->branch_id ?? null;
        
        // Get delivery riders for assignment
        $deliveryRiders = User::where('role', 'delivery')
            ->where('is_active', true)
            ->get();
        
        // Get all sales with orders for this branch - EXCLUDE completed, cancelled, AND assigned delivery
        $sales = Sale::with(['customer', 'orders.product', 'branch'])
            ->whereHas('orders', function($query) {
                $query->whereNotIn('status', ['completed', 'cancelled']);
            })
            ->where('order_status', '!=', 'completed')
            // EXCLUDE orders that are already assigned to a delivery person
            ->where(function($query) {
                $query->whereNull('delivery_person_id')
                      ->orWhere('delivery_person_id', 0);
            })
            ->orderBy('created_at', 'asc');
            
        if ($branchId) {
            $sales->where('branch_id', $branchId);
        }
        
        $sales = $sales->get();
        
        // Separate in-store and online orders
        $inStore = [
            'pending' => [],
            'preparing' => [],
            'serve' => []
        ];
        
        $online = [
            'pending' => [],
            'preparing' => [],
            'deliver' => []
        ];
        
        foreach ($sales as $sale) {
            // CHECK: Online order if may delivery_address
            $isOnline = false;
            
            if ($sale->customer_id && $sale->user_id === null) {
                $isOnline = true;
            }
            
            if (!empty($sale->delivery_address)) {
                $isOnline = true;
            }
            
            if ($sale->delivery_status && $sale->delivery_status !== 'completed') {
                $isOnline = true;
            }
            
            if ($sale->walkin_name) {
                $isOnline = false;
            }
            
            if ($sale->user_id !== null && $sale->customer_id !== null) {
                $isOnline = false;
            }
            
            foreach ($sale->orders as $order) {
                $status = $order->status;
                
                if ($status === 'completed' || $status === 'cancelled') {
                    continue;
                }
                
                // SKIP if delivery is already assigned
                if ($sale->delivery_person_id && $sale->delivery_person_id > 0) {
                    continue;
                }
                
                $category = 'pending';
                if ($status === 'preparing') $category = 'preparing';
                if ($status === 'ready') {
                    $category = $isOnline ? 'deliver' : 'serve';
                }
                
                $orderData = [
                    'sale_id' => $sale->id,
                    'order_id' => $order->id,
                    'customer_name' => $sale->customer->name ?? $sale->walkin_name ?? 'Walk-in',
                    'total' => $sale->total_amount,
                    'status' => $status,
                    'item_count' => $sale->orders->count(),
                    'time_ago' => $sale->created_at->diffForHumans(),
                    'type' => $isOnline ? 'online' : 'in-store',
                    'order_type' => $isOnline ? 'Online' : 'In-Store',
                    'is_online' => $isOnline,
                    'has_delivery' => !empty($sale->delivery_address),
                    'customer_type' => $sale->walkin_name ? 'Walk-in' : ($sale->customer_id ? 'Member' : 'Guest'),
                    'delivery_status' => $sale->delivery_status ?? 'pending',
                    'delivery_person_id' => $sale->delivery_person_id,
                    'delivery_address' => $sale->delivery_address,
                    'delivery_riders' => $deliveryRiders,
                    'is_assigned' => !empty($sale->delivery_person_id)
                ];
                
                if ($isOnline) {
                    $online[$category][] = $orderData;
                } else {
                    $inStore[$category][] = $orderData;
                }
            }
        }
        
        return response()->json([
            'in_store' => $inStore,
            'online' => $online
        ]);
    }

    public function show($saleId)
    {
        $sale = Sale::with(['customer', 'branch', 'orders.product'])
            ->findOrFail($saleId);
            
        return view('barista.order-details', compact('sale'));
    }

    public function acceptOrder($saleId)
    {
        try {
            $sale = Sale::findOrFail($saleId);
            
            foreach ($sale->orders as $order) {
                if ($order->status === 'pending') {
                    $order->status = 'preparing';
                    $order->save();
                }
            }
            
            $sale->order_status = 'preparing';
            $sale->save();
            
            Log::info('Order accepted', ['sale_id' => $saleId, 'user_id' => Auth::id()]);
            
            return response()->json([
                'success' => true,
                'message' => 'Order accepted and is now preparing'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Order acceptance error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error accepting order: ' . $e->getMessage()
            ], 400);
        }
    }

    public function markReady($saleId)
    {
        try {
            $sale = Sale::findOrFail($saleId);
            
            foreach ($sale->orders as $order) {
                if ($order->status === 'preparing') {
                    $order->status = 'ready';
                    $order->save();
                }
            }
            
            $sale->order_status = 'ready';
            $sale->save();
            
            Log::info('Order marked ready', ['sale_id' => $saleId, 'user_id' => Auth::id()]);
            
            return response()->json([
                'success' => true,
                'message' => 'Order is ready'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Order ready error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error marking order ready: ' . $e->getMessage()
            ], 400);
        }
    }

    public function completeOrder($saleId)
    {
        try {
            $sale = Sale::findOrFail($saleId);
            
            foreach ($sale->orders as $order) {
                if ($order->status !== 'completed') {
                    $order->status = 'completed';
                    $order->save();
                }
            }
            
            $sale->order_status = 'completed';
            $sale->delivery_status = 'completed';
            $sale->save();
            
            Log::info('Order completed', ['sale_id' => $saleId, 'user_id' => Auth::id()]);
            
            return response()->json([
                'success' => true,
                'message' => 'Order completed successfully',
                'receipt_url' => route('pos.receipt', $saleId)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Order completion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error completing order: ' . $e->getMessage()
            ], 400);
        }
    }

    public function cancelOrder($saleId)
    {
        try {
            $sale = Sale::findOrFail($saleId);
            
            foreach ($sale->orders as $order) {
                if ($order->status !== 'completed' && $order->status !== 'cancelled') {
                    $order->status = 'cancelled';
                    $order->save();
                }
            }
            
            $sale->order_status = 'cancelled';
            $sale->save();
            
            Log::info('Order cancelled', ['sale_id' => $saleId, 'user_id' => Auth::id()]);
            
            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Order cancellation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling order: ' . $e->getMessage()
            ], 400);
        }
    }

    public function getNewOrders()
    {
        $user = Auth::user();
        $branchId = $user->branch_id ?? null;
        
        $newOrders = Sale::with(['customer', 'orders'])
            ->whereHas('orders', function($query) {
                $query->where('status', 'pending');
            })
            // EXCLUDE orders already assigned to delivery
            ->where(function($query) {
                $query->whereNull('delivery_person_id')
                      ->orWhere('delivery_person_id', 0);
            })
            ->orderBy('created_at', 'desc');
            
        if ($branchId) {
            $newOrders->where('branch_id', $branchId);
        }
        
        $newOrders = $newOrders->get();
        
        return response()->json([
            'count' => $newOrders->count(),
            'orders' => $newOrders->map(function($sale) {
                $isOnline = $sale->customer_id && !$sale->walkin_name;
                return [
                    'id' => $sale->id,
                    'customer_name' => $sale->customer->name ?? $sale->walkin_name ?? 'Walk-in',
                    'total' => $sale->total_amount,
                    'created_at' => $sale->created_at->diffForHumans(),
                    'items' => $sale->orders->count(),
                    'type' => $isOnline ? 'Online' : 'In-Store'
                ];
            })
        ]);
    }
}