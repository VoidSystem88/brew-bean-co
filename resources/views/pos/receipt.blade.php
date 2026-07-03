@extends('layouts.app')

@section('page-title', 'Receipt')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h4>☕ Brew & Bean Co. Pro</h4>
                    <p class="text-muted mb-0">Receipt #{{ $sale->id }}</p>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Date:</strong> {{ $sale->sale_date->format('M d, Y H:i') }}
                        </div>
                        <div class="col-6 text-end">
                            <strong>Branch:</strong> {{ $sale->branch->name }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Cashier:</strong> {{ $sale->user->name }}
                        </div>
                        <div class="col-6 text-end">
                            <strong>Customer:</strong> {{ $sale->customer->name ?? 'Walk-in' }}
                        </div>
                    </div>
                    
                    <hr>
                    
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th class="text-end">${{ number_format($sale->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <hr>
                    
                    <div class="text-center">
                        <p class="text-muted">Thank you for your purchase!</p>
                        <p class="text-muted small">Status: {{ ucfirst($sale->sync_status) }}</p>
                        <a href="{{ route('pos.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i> New Sale
                        </a>
                        <button class="btn btn-secondary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
