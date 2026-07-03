<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin();
        $isManager = $user->isManager();
        $isStaff = $user->isStaff();
        
        $branchId = null;
        if ($isStaff) {
            $branchId = $user->branch_id;
        }
        
        // Today's sales
        $todaySales = Sale::when($branchId, function($query) use ($branchId) {
            return $query->where('branch_id', $branchId);
        })->whereDate('sale_date', today())->sum('total_amount') ?? 0;
        
        $todayOrders = Sale::when($branchId, function($query) use ($branchId) {
            return $query->where('branch_id', $branchId);
        })->whereDate('sale_date', today())->count();
        
        $monthSales = Sale::when($branchId, function($query) use ($branchId) {
            return $query->where('branch_id', $branchId);
        })->whereMonth('sale_date', now()->month)->sum('total_amount') ?? 0;
        
        $totalCustomers = Customer::count();
        $totalProducts = Product::count();
        $totalBranches = Branch::count();
        
        // Pending orders (for barista)
        $pendingOrders = Order::where('status', 'pending')->count();
        
        // Offline pending orders (sync_status = 'pending')
        $offlinePendingOrders = Sale::when($branchId, function($query) use ($branchId) {
            return $query->where('branch_id', $branchId);
        })->where('sync_status', 'pending')->count();
        
        // Low stock items
        $lowStockItems = DB::table('items')
            ->join('warehouse_stock', 'items.id', '=', 'warehouse_stock.item_id')
            ->whereColumn('warehouse_stock.stock_quantity', '<=', 'warehouse_stock.low_stock_threshold')
            ->select('items.name', 'warehouse_stock.stock_quantity', 'warehouse_stock.low_stock_threshold')
            ->limit(5)
            ->get();
        
        $recentSales = Sale::with(['user', 'branch'])
            ->when($branchId, function($query) use ($branchId) {
                return $query->where('branch_id', $branchId);
            })
            ->latest()
            ->limit(5)
            ->get();
        
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->when($branchId, function($query) use ($branchId) {
                return $query->where('sales.branch_id', $branchId);
            })
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_sold'), DB::raw('SUM(sale_items.subtotal) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();
        
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $daySales = Sale::when($branchId, function($query) use ($branchId) {
                return $query->where('branch_id', $branchId);
            })->whereDate('sale_date', $date)->sum('total_amount') ?? 0;
            
            $chartData[] = [
                'day' => $date->format('D'),
                'sales' => $daySales
            ];
        }
        
        // Offline sync status
        $isOnline = true; // Check connection status
        $offlineOrders = Sale::when($branchId, function($query) use ($branchId) {
            return $query->where('branch_id', $branchId);
        })->where('sync_status', 'pending')->get();
        
        return view('dashboard.index', compact(
            'todaySales', 'todayOrders', 'monthSales', 
            'totalCustomers', 'totalProducts', 'totalBranches',
            'pendingOrders', 'lowStockItems', 'recentSales', 
            'topProducts', 'chartData', 'isAdmin', 'isManager', 'isStaff',
            'offlinePendingOrders', 'isOnline', 'offlineOrders'
        ));
    }
}