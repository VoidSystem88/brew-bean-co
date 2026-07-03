@extends('layouts.app')

@section('page-title', 'Customer Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer Profile</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-circle" style="width: 80px; height: 80px; background: #6F4E37; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto;">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                        <h5 class="mt-2">{{ $customer->name }}</h5>
                        <p class="text-muted">{{ $customer->customer_code }}</p>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <strong>Email:</strong> {{ $customer->email }}
                    </div>
                    <div class="mb-2">
                        <strong>Phone:</strong> {{ $customer->phone ?? 'N/A' }}
                    </div>
                    <div class="mb-2">
                        <strong>Total Orders:</strong> {{ $totalOrders }}
                    </div>
                    <div class="mb-2">
                        <strong>Total Spent:</strong> ₱{{ number_format($totalSpent, 2) }}
                    </div>
                    <div class="mb-2">
                        <strong>Loyalty Points:</strong> {{ $customer->loyalty_points ?? 0 }}
                    </div>
                    <div class="mb-2">
                        <strong>Member Since:</strong> {{ $customer->created_at->format('M d, Y') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Purchase History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Branch</th>
                                    <th>Total</th>
                                    <th>Items</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPurchases as $sale)
                                    <tr>
                                        <td>#{{ $sale->id }}</td>
                                        <td>{{ $sale->sale_date->format('M d, Y h:i A') }}</td>
                                        <td>{{ $sale->branch->name ?? 'Unknown' }}</td>
                                        <td>₱{{ number_format($sale->total_amount, 2) }}</td>
                                        <td>{{ $sale->items->count() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No purchases yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection