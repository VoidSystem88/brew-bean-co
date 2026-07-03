@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Today's Sales by Branch -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-store me-2"></i> Today''s Sales by Branch
                    <span class="float-end">Total: <strong>₱{{ number_format($todayTotal ?? 0, 2) }}</strong></span>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($branchSales ?? [] as $branchSale)
                            <div class="col-md-3 col-6 mb-3">
                                <div class="stat-card text-center">
                                    <div class="label"><i class="fas fa-store me-1"></i>{{ $branchSale->branch->name ?? 'Unknown' }}</div>
                                    <div class="number">₱{{ number_format($branchSale->total, 2) }}</div>
                                    <small class="text-muted"><i class="fas fa-receipt me-1"></i>{{ $branchSale->count }} transactions</small>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted text-center"><i class="fas fa-info-circle me-1"></i>No sales yet today</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->isAdmin())
        <!-- Super Admin Dashboard -->
        <div class="row mb-4">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="label"><i class="fas fa-coins me-1"></i>Today''s Sales</div>
                            <div class="number">₱{{ number_format($totalSalesToday ?? 0, 2) }}</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="label"><i class="fas fa-store me-1"></i>Total Branches</div>
                            <div class="number">{{ $totalBranches ?? 0 }}</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-store-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="label"><i class="fas fa-cube me-1"></i>Total Products</div>
                            <div class="number">{{ $totalProducts ?? 0 }}</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="label"><i class="fas fa-exclamation-triangle me-1"></i>Low Stock Alerts</div>
                            <div class="number text-danger">{{ $lowStockAlerts->count() ?? 0 }}</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-bell text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-line me-2"></i> Sales Trend (Last 7 Days)
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-fire me-2"></i> Top Selling Products Today
                    </div>
                    <div class="card-body">
                        @if(isset($topProducts) && $topProducts->count())
                            <div class="list-group list-group-flush">
                                @foreach($topProducts as $product)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-cube me-1"></i>{{ $product->product->name }}</span>
                                        <span class="badge bg-primary rounded-pill">{{ $product->total_sold }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center"><i class="fas fa-info-circle me-1"></i>No sales today</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        @if(isset($lowStockAlerts) && $lowStockAlerts->count())
        <div class="row">
            <div class="col-12">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <i class="fas fa-exclamation-triangle me-2"></i> Low Stock Alerts
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-cube me-1"></i>Product</th>
                                        <th><i class="fas fa-store me-1"></i>Branch</th>
                                        <th><i class="fas fa-box me-1"></i>Current Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockAlerts as $alert)
                                    <tr>
                                        <td><i class="fas fa-cube me-1"></i>{{ $alert->product }}</td>
                                        <td><i class="fas fa-store me-1"></i>{{ $alert->branch }}</td>
                                        <td class="text-danger font-weight-bold">{{ $alert->stock }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    @elseif(auth()->user()->isManager())
        <!-- Branch Manager Dashboard -->
        <div class="row mb-4">
            <div class="col-md-4 col-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="label"><i class="fas fa-coins me-1"></i>Today''s Sales</div>
                            <div class="number">₱{{ number_format($totalSalesToday ?? 0, 2) }}</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="label"><i class="fas fa-users me-1"></i>Staff Count</div>
                            <div class="number">{{ $totalStaff ?? 0 }}</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-friends"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="label"><i class="fas fa-exclamation-triangle me-1"></i>Low Stock Alerts</div>
                            <div class="number text-danger">{{ $lowStockAlerts->count() ?? 0 }}</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-bell text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-line me-2"></i> Branch Sales Trend (Last 7 Days)
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Staff Dashboard -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-clock me-2"></i> Recent Sales
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-hashtag me-1"></i>Sale ID</th>
                                        <th><i class="fas fa-user me-1"></i>Customer</th>
                                        <th><i class="fas fa-money-bill-wave me-1"></i>Total</th>
                                        <th><i class="fas fa-calendar me-1"></i>Date</th>
                                        <th><i class="fas fa-sync me-1"></i>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentSales ?? [] as $sale)
                                    <tr>
                                        <td>#{{ $sale->id }}</td>
                                        <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                                        <td>₱{{ number_format($sale->total_amount, 2) }}</td>
                                        <td><i class="far fa-clock me-1"></i>{{ $sale->sale_date->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($sale->sync_status == 'synced')
                                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Synced</span>
                                            @else
                                                <span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    @if(isset($chartDates) && isset($chartTotals))
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartDates) !!},
            datasets: [{
                label: 'Daily Sales',
                data: {!! json_encode($chartTotals) !!},
                borderColor: '#6F4E37',
                backgroundColor: 'rgba(111, 78, 55, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: function(value) { return '₱' + value.toFixed(2); } }
                }
            }
        }
    });
    @endif
</script>
@endpush
@endsection
