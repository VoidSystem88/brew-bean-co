<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get branches based on user role
        if ($user->isAdmin()) {
            $branches = Branch::where('is_active', true)->get();
        } else {
            $branches = Branch::where('id', $user->branch_id)->get();
        }

        // Build query
        $query = Sale::with(['branch', 'user', 'items.product'])
            ->when($request->branch_id, function($q) use ($request) {
                return $q->where('branch_id', $request->branch_id);
            })
            ->when($request->date_from, function($q) use ($request) {
                return $q->whereDate('sale_date', '>=', $request->date_from);
            })
            ->when($request->date_to, function($q) use ($request) {
                return $q->whereDate('sale_date', '<=', $request->date_to);
            })
            ->when($request->product_id, function($q) use ($request) {
                return $q->whereHas('items', function($sub) use ($request) {
                    $sub->where('product_id', $request->product_id);
                });
            });

        // Apply branch restriction for non-admin users
        if (!$user->isAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        $sales = $query->orderBy('sale_date', 'desc')->paginate(20);

        // Calculate totals
        $totals = [
            'total_sales' => $query->sum('total_amount'),
            'total_items' => SaleItem::whereIn('sale_id', $query->pluck('id'))->sum('quantity'),
            'average_sale' => $query->avg('total_amount') ?? 0,
        ];

        $products = Product::all();

        return view('reports.index', compact('sales', 'branches', 'totals', 'request', 'products'));
    }

    public function exportPdf(Request $request)
    {
        // Similar query to index but get all results
        $user = Auth::user();
        
        $query = Sale::with(['branch', 'user', 'items.product'])
            ->when($request->branch_id, function($q) use ($request) {
                return $q->where('branch_id', $request->branch_id);
            })
            ->when($request->date_from, function($q) use ($request) {
                return $q->whereDate('sale_date', '>=', $request->date_from);
            })
            ->when($request->date_to, function($q) use ($request) {
                return $q->whereDate('sale_date', '<=', $request->date_to);
            });

        if (!$user->isAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();

        // Simple PDF generation (you can install barryvdh/laravel-dompdf for better PDFs)
        $html = view('reports.pdf', compact('sales'))->render();
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="sales-report-' . now()->format('Y-m-d') . '.html"');
    }
}