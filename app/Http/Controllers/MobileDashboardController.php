<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Item;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MobileDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $branchId = $user->branch_id ?? null;
        
        // Today's sales
        $todaySales = Sale::whereDate('sale_date', today())->sum('total_amount');
        $todayCount = Sale::whereDate('sale_date', today())->count();
        
        // This week sales
        $weekSales = Sale::whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount');
        
        // This month sales
        $monthSales = Sale::whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->sum('total_amount');
        
        // Sales by branch
        $branchSales = Sale::whereDate('sale_date', today())
            ->select('branch_id', DB::raw('SUM(total_amount) as total, COUNT(*) as count'))
            ->groupBy('branch_id')
            ->with('branch')
            ->get();
        
        // Critical items (stock <= 2)
        $criticalItems = $this->getCriticalItems($branchId);
        
        // Low stock count
        $lowStockCount = $this->getLowStockCount($branchId);
        
        // Recent orders
        $recentOrders = Sale::with(['branch', 'customer'])
            ->when($branchId, function($q) use ($branchId) {
                return $q->where('branch_id', $branchId);
            })
            ->latest()
            ->limit(10)
            ->get();
        
        // Top products today
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereDate('sales.sale_date', today())
            ->when($branchId, function($q) use ($branchId) {
                return $q->where('sales.branch_id', $branchId);
            })
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();
        
        // Pending orders count
        $pendingOrders = Order::where('status', 'pending')
            ->when($branchId, function($q) use ($branchId) {
                return $q->whereHas('sale', function($sq) use ($branchId) {
                    $sq->where('branch_id', $branchId);
                });
            })
            ->count();
        
        // Customer count
        $customerCount = DB::table('customers')->count();
        
        // New customers this month
        $newCustomers = DB::table('customers')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Average order value
        $avgOrderValue = Sale::whereDate('sale_date', today())
            ->when($branchId, function($q) use ($branchId) {
                return $q->where('branch_id', $branchId);
            })
            ->avg('total_amount') ?? 0;
        
        // Get all alerts
        $alerts = $this->getAlerts($branchId);
        
        return view('mobile.dashboard', compact(
            'todaySales',
            'todayCount',
            'weekSales',
            'monthSales',
            'branchSales',
            'criticalItems',
            'lowStockCount',
            'recentOrders',
            'topProducts',
            'pendingOrders',
            'customerCount',
            'newCustomers',
            'avgOrderValue',
            'alerts'
        ));
    }
    
    private function getCriticalItems($branchId = null)
    {
        $query = DB::table('branch_item')
            ->join('items', 'branch_item.item_id', '=', 'items.id')
            ->join('branches', 'branch_item.branch_id', '=', 'branches.id')
            ->whereColumn('branch_item.stock_quantity', '<=', 'branch_item.low_stock_threshold')
            ->where('branch_item.stock_quantity', '<=', 2)
            ->select(
                'items.name as item_name',
                'items.category',
                'items.unit',
                'branch_item.stock_quantity as stock',
                'branch_item.low_stock_threshold as threshold',
                'branches.name as branch_name',
                'branches.id as branch_id'
            )
            ->orderBy('branch_item.stock_quantity', 'asc')
            ->limit(10);
            
        if ($branchId) {
            $query->where('branch_item.branch_id', $branchId);
        }
        
        return $query->get();
    }
    
    private function getLowStockCount($branchId = null)
    {
        $query = DB::table('branch_item')
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
            
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        return $query->count();
    }
    
    private function getAlerts($branchId = null)
    {
        $alerts = [];
        
        // Low stock alerts
        $lowStockItems = $this->getCriticalItems($branchId);
        foreach ($lowStockItems->take(3) as $item) {
            $alerts[] = [
                'type' => 'low_stock',
                'icon' => 'fa-exclamation-triangle',
                'color' => '#dc3545',
                'title' => 'Low Stock',
                'message' => "{$item->item_name} is critically low ({$item->stock} {$item->unit} left)",
                'branch' => $item->branch_name,
                'time' => now()->diffForHumans()
            ];
        }
        
        // Pending orders alerts
        $pendingCount = Order::where('status', 'pending')
            ->when($branchId, function($q) use ($branchId) {
                return $q->whereHas('sale', function($sq) use ($branchId) {
                    $sq->where('branch_id', $branchId);
                });
            })
            ->count();
            
        if ($pendingCount > 0) {
            $alerts[] = [
                'type' => 'pending_orders',
                'icon' => 'fa-clock',
                'color' => '#ffc107',
                'title' => 'Pending Orders',
                'message' => "{$pendingCount} order(s) waiting for preparation",
                'branch' => 'All branches',
                'time' => now()->diffForHumans()
            ];
        }
        
        // No sales today alert
        $todayCount = Sale::whereDate('sale_date', today())
            ->when($branchId, function($q) use ($branchId) {
                return $q->where('branch_id', $branchId);
            })
            ->count();
            
        if ($todayCount == 0) {
            $alerts[] = [
                'type' => 'no_sales',
                'icon' => 'fa-info-circle',
                'color' => '#17a2b8',
                'title' => 'No Sales Today',
                'message' => 'No transactions recorded today',
                'branch' => 'All branches',
                'time' => 'Today'
            ];
        }
        
        return $alerts;
    }
}