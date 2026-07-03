<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Item;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MobileDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get today's total sales
        $todaySales = Sale::whereDate('sale_date', today())->sum('total_amount');
        $todayCount = Sale::whereDate('sale_date', today())->count();
        
        // Get sales by branch (for breakdown)
        $branchSales = Sale::whereDate('sale_date', today())
            ->select('branch_id', DB::raw('SUM(total_amount) as total, COUNT(*) as count'))
            ->groupBy('branch_id')
            ->with('branch')
            ->get();
        
        // Get low stock items (critical only - stock <= 2)
        $criticalItems = $this->getCriticalItems();
        
        // Get low stock count for badge
        $lowStockCount = $this->getLowStockCount();
        
        // Get recent alerts
        $recentAlerts = $this->getRecentAlerts();
        
        return view('mobile.dashboard', compact(
            'todaySales', 
            'todayCount', 
            'branchSales', 
            'criticalItems', 
            'lowStockCount',
            'recentAlerts'
        ));
    }
    
    private function getCriticalItems()
    {
        $user = Auth::user();
        $branchId = $user->branch_id ?? null;
        
        $query = DB::table('branch_item')
            ->join('items', 'branch_item.item_id', '=', 'items.id')
            ->join('branches', 'branch_item.branch_id', '=', 'branches.id')
            ->whereColumn('branch_item.stock_quantity', '<=', 'branch_item.low_stock_threshold')
            ->where('branch_item.stock_quantity', '<=', 2) // Critical: 2 or less
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
    
    private function getLowStockCount()
    {
        $user = Auth::user();
        $branchId = $user->branch_id ?? null;
        
        $query = DB::table('branch_item')
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
            
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        return $query->count();
    }
    
    private function getRecentAlerts()
    {
        // Simulate recent alerts (you can replace with actual data later)
        $alerts = [];
        
        $criticalItems = $this->getCriticalItems();
        foreach ($criticalItems->take(3) as $item) {
            $alerts[] = [
                'type' => 'low_stock',
                'message' => "⚠️ {$item->item_name} is critically low ({$item->stock} {$item->unit} left)",
                'branch' => $item->branch_name,
                'time' => now()->subMinutes(rand(5, 60))->diffForHumans()
            ];
        }
        
        return $alerts;
    }
}