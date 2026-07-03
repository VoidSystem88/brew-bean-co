@extends('layouts.app')

@section('page-title', 'Customers')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users me-2"></i>Customers</h2>
        <span class="badge bg-primary">{{ $customers->count() }} total</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Member Code</th>
                            <th class="text-center">Orders</th>
                            <th class="text-center">Total Spent</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2" style="width: 32px; height: 32px; background: #6F4E37; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px;">
                                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $customer->name }}</strong>
                                            @if($customer->is_active == 0)
                                                <span class="badge bg-danger ms-1">Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone ?? 'N/A' }}</td>
                                <td><span class="badge bg-primary">{{ $customer->customer_code }}</span></td>
                                <td class="text-center">{{ $customer->sales->count() }}</td>
                                <td class="text-center">₱{{ number_format($customer->sales->sum('total_amount'), 2) }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('customers.qr', $customer) }}" class="btn btn-sm btn-secondary" title="QR Code">
                                            <i class="fas fa-qrcode"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="deleteCustomer({{ $customer->id }}, '{{ $customer->name }}')" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x d-block mb-2"></i>
                                    No customers registered yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-2 text-muted small">
        <i class="fas fa-info-circle me-1"></i>Total: <strong>{{ $customers->count() }}</strong> customers
    </div>
</div>

@push('scripts')
<script>
    function deleteCustomer(id, name) {
        if (!confirm(`Are you sure you want to delete customer "${name}"?\n\nThis action cannot be undone.`)) {
            return;
        }

        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

        fetch(`/customers/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            alert('Error: ' + error);
        });
    }
</script>
@endpush
@endsection