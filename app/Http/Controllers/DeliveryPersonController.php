<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\DeliveryTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryPersonController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');
        $thisWeek = now()->startOfWeek()->format('Y-m-d');
        $thisMonth = now()->startOfMonth()->format('Y-m-d');
        
        // Get assigned orders
        $assignedOrders = Sale::with(['customer', 'branch'])
            ->where('delivery_person_id', $user->id)
            ->where('delivery_status', '!=', 'completed')
            ->where('delivery_status', '!=', 'failed')
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Completed deliveries today
        $completedToday = Sale::where('delivery_person_id', $user->id)
            ->where('delivery_status', 'completed')
            ->whereDate('delivery_completed_at', today())
            ->get();
        
        $completedTodayCount = $completedToday->count();
        $completedTodayEarnings = $completedToday->sum('total_amount') * 0.10;
        
        // Completed this week
        $completedWeek = Sale::where('delivery_person_id', $user->id)
            ->where('delivery_status', 'completed')
            ->whereDate('delivery_completed_at', '>=', $thisWeek)
            ->get();
        
        $completedWeekCount = $completedWeek->count();
        $completedWeekEarnings = $completedWeek->sum('total_amount') * 0.10;
        
        // Completed this month
        $completedMonth = Sale::where('delivery_person_id', $user->id)
            ->where('delivery_status', 'completed')
            ->whereDate('delivery_completed_at', '>=', $thisMonth)
            ->get();
        
        $completedMonthCount = $completedMonth->count();
        $completedMonthEarnings = $completedMonth->sum('total_amount') * 0.10;
        
        // Total deliveries all time
        $totalDeliveries = Sale::where('delivery_person_id', $user->id)
            ->where('delivery_status', 'completed')
            ->count();
        
        $totalEarnings = Sale::where('delivery_person_id', $user->id)
            ->where('delivery_status', 'completed')
            ->sum('total_amount') * 0.10;
        
        // Recent completed
        $recentCompleted = Sale::with(['customer'])
            ->where('delivery_person_id', $user->id)
            ->where('delivery_status', 'completed')
            ->orderBy('delivery_completed_at', 'desc')
            ->limit(10)
            ->get();
        
        $commissionRate = 10;
        
        return view('delivery.dashboard', compact(
            'assignedOrders',
            'completedTodayCount',
            'completedTodayEarnings',
            'completedWeekCount',
            'completedWeekEarnings',
            'completedMonthCount',
            'completedMonthEarnings',
            'totalDeliveries',
            'totalEarnings',
            'recentCompleted',
            'commissionRate'
        ));
    }

    public function updateStatus(Request $request, $saleId)
    {
        try {
            $request->validate([
                'status' => 'required|in:picked_up,in_transit,completed,failed',
                'notes' => 'nullable|string'
            ]);

            $sale = Sale::where('delivery_person_id', Auth::id())
                ->findOrFail($saleId);

            $status = $request->status;
            
            switch ($status) {
                case 'picked_up':
                    $sale->delivery_picked_up_at = now();
                    break;
                case 'completed':
                    $sale->delivery_completed_at = now();
                    $sale->order_status = 'completed';
                    foreach ($sale->orders as $order) {
                        $order->status = 'completed';
                        $order->save();
                    }
                    break;
                case 'failed':
                    $sale->delivery_notes = $request->notes ?? 'Delivery failed';
                    break;
            }
            
            $sale->delivery_status = $status;
            $sale->save();

            // Create tracking record
            DeliveryTracking::create([
                'sale_id' => $sale->id,
                'user_id' => Auth::id(),
                'status' => $status,
                'notes' => $request->notes ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated to ' . str_replace('_', ' ', $status)
            ]);

        } catch (\Exception $e) {
            \Log::error('Update status error: ' . $e->getMessage());
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

    public function getOrderDetails($saleId)
    {
        $sale = Sale::with(['customer', 'branch', 'orders.product'])
            ->where('delivery_person_id', Auth::id())
            ->findOrFail($saleId);
        
        return view('delivery.order-details', compact('sale'));
    }
}