@extends('layouts.app')

@section('page-title', 'Supplier Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ $supplier->name }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>Email:</strong> {{ $supplier->email }}</p>
                    <p><strong>Phone:</strong> {{ $supplier->phone }}</p>
                    <p><strong>Contact Person:</strong> {{ $supplier->contact_person ?? 'N/A' }}</p>
                    <p><strong>Status:</strong> 
                        @if($supplier->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </p>
                    @if($supplier->website)
                        <p><strong>Website:</strong> <a href="{{ $supplier->website }}" target="_blank">{{ $supplier->website }}</a></p>
                    @endif
                    @if($supplier->notes)
                        <p><strong>Notes:</strong> {{ $supplier->notes }}</p>
                    @endif
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary btn-sm">Edit</a>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Restock Orders</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Branch</th>
                                    <th>Items</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->branch->name }}</td>
                                    <td>{{ $order->items->count() }}</td>
                                    <td>
                                        @if($order->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($order->status == 'sent')
                                            <span class="badge bg-info">Sent</span>
                                        @elseif($order->status == 'approved')
                                            <span class="badge bg-success">Approved ✅</span>
                                        @elseif($order->status == 'delivered')
                                            <span class="badge bg-primary">Delivered</span>
                                        @elseif($order->status == 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No orders from this supplier</td>
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