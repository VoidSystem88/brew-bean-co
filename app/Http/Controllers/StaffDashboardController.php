<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Order;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;
        
        // Pending transfers for this branch
        $pendingTransfers = Transfer::with(['item', 'fromBranch'])
            ->where('to_branch_id', $branchId)
            ->where('status', 'pending')
            ->get();
        
        $pendingTransfersCount = $pendingTransfers->count();
        
        // Pending orders for this branch
        $pendingOrders = Order::with(['sale', 'product'])
            ->whereHas('sale', function($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->where('status', 'pending')
            ->count();
        
        // Today's sales
        $todaySales = Sale::where('branch_id', $branchId)
            ->whereDate('sale_date', today())
            ->sum('total_amount') ?? 0;
        
        $todayOrders = Sale::where('branch_id', $branchId)
            ->whereDate('sale_date', today())
            ->count();
        
        // Recent orders with items (for staff dashboard)
        $recentOrders = Sale::with(['items.product', 'branch', 'user'])
            ->where('branch_id', $branchId)
            ->latest()
            ->limit(10)
            ->get();
        
        // Add sync status to each order
        foreach ($recentOrders as $order) {
            // Check if order is offline (sync_status = 'pending')
            $order->is_offline = $order->sync_status === 'pending';
        }
        
        return view('staff.dashboard', compact(
            'pendingTransfers', 
            'pendingTransfersCount', 
            'pendingOrders', 
            'todaySales', 
            'todayOrders',
            'recentOrders'
        ));
    }
}