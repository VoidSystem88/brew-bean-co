@extends('layouts.app')

@section('page-title', 'Suppliers')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Suppliers</h2>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Supplier
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact Person</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->id }}</td>
                            <td><strong>{{ $supplier->name }}</strong></td>
                            <td>{{ $supplier->email }}</td>
                            <td>{{ $supplier->contact_person ?? 'N/A' }}</td>
                            <td>{{ $supplier->phone }}</td>
                            <td>
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this supplier?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-truck fa-2x d-block mb-2"></i>
                                No suppliers found. Click "Add Supplier" to create one.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-2 text-muted small">
                Total: <strong>{{ $suppliers->count() }}</strong> suppliers
            </div>
        </div>
    </div>
</div>
@endsection