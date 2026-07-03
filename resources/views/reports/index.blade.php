@extends('layouts.app')

@section('page-title', 'Sales Reports')

@section('content')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 18px 22px;
        border: 1px solid #e8e8e8;
        transition: all 0.2s;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        border-color: #6F4E37;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    }
    .stat-card .label {
        font-size: 13px;
        color: #999;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-card .number {
        font-size: 26px;
        font-weight: 700;
        color: #2d3436;
        margin-top: 4px;
    }
    .stat-card .icon {
        font-size: 28px;
        color: #6F4E37;
        opacity: 0.3;
    }
    .filter-card {
        background: white;
        border-radius: 12px;
        padding: 18px 22px;
        border: 1px solid #e8e8e8;
        margin-bottom: 20px;
    }
    .filter-card .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #555;
        margin-bottom: 4px;
    }
    .filter-card .form-control,
    .filter-card .form-select {
        border-radius: 8px;
        border: 1px solid #e8e8e8;
        font-size: 14px;
        padding: 8px 12px;
    }
    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        border-color: #6F4E37;
        box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.1);
    }
    .table thead th {
        background: #f8f9fa;
        font-weight: 600;
        font-size: 13px;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
    }
    .table tbody td {
        vertical-align: middle;
        font-size: 14px;
        padding: 10px 12px;
    }
    .table tbody tr:hover {
        background-color: #f8f6f4;
    }
    .badge-synced {
        background: #d4edda;
        color: #155724;
        padding: 3px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-pending {
        background: #fff3cd;
        color: #856404;
        padding: 3px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    .btn-export {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        transition: 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-export:hover {
        background: #5a3d2b;
        color: white;
    }
    .btn-filter {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        transition: 0.2s;
        width: 100%;
        cursor: pointer;
    }
    .btn-filter:hover {
        background: #5a3d2b;
        color: white;
    }
    .btn-reset {
        background: #e9ecef;
        color: #495057;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        transition: 0.2s;
        width: 100%;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }
    .btn-reset:hover {
        background: #dee2e6;
        color: #333;
    }
    .pagination-wrapper {
        padding: 12px 16px;
        border-top: 1px solid #e8e8e8;
    }
    .pagination-wrapper .pagination {
        margin: 0;
        justify-content: center;
    }
    .pagination-wrapper .pagination .page-link {
        border-radius: 6px;
        color: #6F4E37;
        border: 1px solid #e8e8e8;
        padding: 6px 12px;
        font-size: 14px;
        min-width: 36px;
        text-align: center;
    }
    .pagination-wrapper .pagination .page-link:hover {
        background: #f8f6f4;
        border-color: #6F4E37;
    }
    .pagination-wrapper .pagination .active .page-link {
        background: #6F4E37;
        border-color: #6F4E37;
        color: white;
    }
    .pagination-wrapper .pagination .disabled .page-link {
        color: #ccc;
        cursor: not-allowed;
        background: #f8f9fa;
    }
    .pagination-wrapper .pagination .page-item:first-child .page-link,
    .pagination-wrapper .pagination .page-item:last-child .page-link {
        border-radius: 6px;
    }
    .table-responsive {
        overflow-x: auto;
    }
    @media (max-width: 768px) {
        .stat-card .number {
            font-size: 20px;
        }
        .stat-card .icon {
            font-size: 20px;
        }
        .filter-card .row > div {
            margin-bottom: 10px;
        }
        .pagination-wrapper .pagination .page-link {
            padding: 4px 8px;
            font-size: 12px;
            min-width: 30px;
        }
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Sales Reports</h2>
        <a href="{{ route('reports.export-pdf') }}?{{ http_build_query(request()->query()) }}" class="btn-export">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="branch_id" class="form-label">Branch</label>
                <select name="branch_id" id="branch_id" class="form-select">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ str_replace('☕ Brew & Bean Co. - ', '', $branch->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="product_id" class="form-label">Product</label>
                <select name="product_id" id="product_id" class="form-select">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">Date From</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">Date To</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn-reset">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="label">Total Sales</div>
                        <div class="number">₱{{ number_format($totals['total_sales'] ?? 0, 2) }}</div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="label">Total Items Sold</div>
                        <div class="number">{{ number_format($totals['total_items'] ?? 0) }}</div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-box"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="label">Average Sale</div>
                        <div class="number">₱{{ number_format($totals['average_sale'] ?? 0, 2) }}</div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card border-0 shadow-sm" style="border-radius:12px; overflow:hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:60px;">#</th>
                            <th>Date</th>
                            <th>Branch</th>
                            <th>Cashier</th>
                            <th>Customer</th>
                            <th class="text-center">Items</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr>
                            <td>#{{ $sale->id }}</td>
                            <td>{{ $sale->sale_date->format('M d, Y h:i A') }}</td>
                            <td>{{ str_replace('☕ Brew & Bean Co. - ', '', $sale->branch->name ?? 'N/A') }}</td>
                            <td>{{ $sale->user->name ?? 'Unknown' }}</td>
                            <td>{{ $sale->customer->name ?? $sale->walkin_name ?? 'Walk-in' }}</td>
                            <td class="text-center">{{ $sale->items->sum('quantity') }}</td>
                            <td class="text-end fw-bold" style="color:#6F4E37;">₱{{ number_format($sale->total_amount, 2) }}</td>
                            <td class="text-center">
                                @if($sale->sync_status == 'synced')
                                    <span class="badge-synced">✅ Synced</span>
                                @else
                                    <span class="badge-pending">📡 Pending</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                No sales found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($sales->hasPages())
            <div class="pagination-wrapper">
                {{ $sales->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection