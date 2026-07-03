<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OrderQueueController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isStaff()) {
            $orders = Order::with(['sale.branch', 'product'])
                ->whereHas('sale', function($query) use ($user) {
                    $query->where('branch_id', $user->branch_id);
                })
                ->whereNotIn('status', ['completed', 'cancelled', 'served'])
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            $orders = Order::with(['sale.branch', 'product'])
                ->whereNotIn('status', ['completed', 'cancelled', 'served'])
                ->orderBy('created_at', 'asc')
                ->get();
        }
        
        foreach ($orders as $order) {
            $sale = $order->sale;
            if ($sale) {
                if ($sale->customer_id) {
                    $order->customer_name = $sale->customer->name ?? 'Unknown Member';
                    $order->customer_type = 'member';
                } elseif ($sale->walkin_name) {
                    $order->customer_name = $sale->walkin_name;
                    $order->customer_type = 'walkin';
                } else {
                    $order->customer_name = 'Walk-in';
                    $order->customer_type = 'walkin';
                }
                
                $order->branch_name = $sale->branch ? str_replace('☕ Brew & Bean Co. - ', '', $sale->branch->name) : 'Unknown';
            }
        }
        
        return view('barista.queue', compact('orders'));
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $status = $request->status;
            
            // Allowed statuses - use 'served' instead of 'completed'
            $allowed = ['pending', 'preparing', 'ready', 'served', 'cancelled'];
            if (!in_array($status, $allowed)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status: ' . $status
                ], 400);
            }

            $order = Order::find($id);
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $oldStatus = $order->status;
            $order->status = $status;
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated from ' . $oldStatus . ' to ' . $status,
                'status' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Order status update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}