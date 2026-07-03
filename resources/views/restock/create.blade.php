@extends('layouts.app')

@section('page-title', 'Create Restock Order')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Create Restock Order</h2>
        <a href="{{ route('restock.orders') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('restock.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Branch *</label>
                        <select name="branch_id" class="form-select" required>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ $branch && $branch->id == $b->id ? 'selected' : '' }}>
                                    {{ $b->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Supplier *</label>
                        <select name="supplier_id" class="form-select" required>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Expected Delivery Date</label>
                        <input type="date" name="expected_delivery_date" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="Special instructions...">
                    </div>
                </div>

                <h5>Items to Restock</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Current Stock</th>
                                <th>Alert Level</th>
                                <th>Quantity to Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockItems as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td class="text-danger">{{ number_format($item->stock, 2) }}</td>
                                <td>{{ $item->threshold }}</td>
                                <td>
                                    <input type="hidden" name="items[{{ $loop->index }}][item_id]" value="{{ $item->id }}">
                                    <input type="number" name="items[{{ $loop->index }}][quantity]" class="form-control" value="{{ $item->threshold * 2 }}" min="1" required>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> Send Order to Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
