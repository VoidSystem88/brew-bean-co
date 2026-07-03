<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MobileController extends Controller
{
    public function test()
    {
        return view('mobile.test');
    }

    public function dashboard()
    {
        $todaySales = Sale::whereDate('sale_date', today())->sum('total_amount');
        $todayCount = Sale::whereDate('sale_date', today())->count();
        $lowStockCount = $this->getLowStockCount();
        $recentSales = Sale::with(['branch', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('mobile.dashboard', compact('todaySales', 'todayCount', 'lowStockCount', 'recentSales'));
    }

    public function lowStock()
    {
        $user = Auth::user();
        $branchId = $user->branch_id ?? 1;
        
        $lowStockItems = DB::table('branch_item')
            ->join('items', 'branch_item.item_id', '=', 'items.id')
            ->join('branches', 'branch_item.branch_id', '=', 'branches.id')
            ->where('branch_item.branch_id', $branchId)
            ->whereColumn('branch_item.stock_quantity', '<=', 'branch_item.low_stock_threshold')
            ->select('items.name as item_name', 'items.category', 'items.unit', 
                    'branch_item.stock_quantity as stock', 
                    'branch_item.low_stock_threshold as threshold',
                    'branches.name as branch_name')
            ->orderBy('branch_item.stock_quantity', 'asc')
            ->get();
        
        return view('mobile.low-stock', compact('lowStockItems'));
    }

    public function salesSummary()
    {
        $user = Auth::user();
        $branchId = $user->branch_id ?? 1;
        
        $todaySales = Sale::whereDate('sale_date', today())->sum('total_amount');
        $todayCount = Sale::whereDate('sale_date', today())->count();
        $weekSales = Sale::whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount');
        $monthSales = Sale::whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->sum('total_amount');
        
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.branch_id', $branchId)
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();
        
        return view('mobile.sales-summary', compact(
            'todaySales', 'todayCount', 'weekSales', 'monthSales', 'topProducts'
        ));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('mobile.profile', compact('user'));
    }

    private function getLowStockCount()
    {
        $user = Auth::user();
        $branchId = $user->branch_id ?? 1;
        return DB::table('branch_item')
            ->where('branch_id', $branchId)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();
    }
}
