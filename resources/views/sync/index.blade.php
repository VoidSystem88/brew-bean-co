@extends('layouts.app')

@section('page-title', 'Sync Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Sync Management</h2>
    </div>

    <!-- Status Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="label">Offline Mode</div>
                        <div class="number">
                            @if($offlineMode)
                                <span class="text-warning">ON</span>
                            @else
                                <span class="text-success">OFF</span>
                            @endif
                        </div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-wifi"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="label">Pending Sales</div>
                        <div class="number">{{ $pendingSales->count() }}</div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="label">Actions</div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-warning" onclick="toggleOffline(true)">
                                <i class="fas fa-plug me-2"></i> Go Offline
                            </button>
                            <button class="btn btn-success" onclick="toggleOffline(false)">
                                <i class="fas fa-wifi me-2"></i> Go Online
                            </button>
                            <button class="btn btn-primary" onclick="syncNow()">
                                <i class="fas fa-sync me-2"></i> Sync Now
                            </button>
                        </div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Sales Table -->
    @if($pendingSales->count() > 0)
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Pending Sales</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Branch</th>
                            <th>Cashier</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingSales as $sale)
                        <tr>
                            <td>{{ $sale->id }}</td>
                            <td>{{ $sale->sale_date->format('M d, Y H:i') }}</td>
                            <td>{{ $sale->branch->name }}</td>
                            <td>{{ $sale->user->name }}</td>
                            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                            <td>${{ number_format($sale->total_amount, 2) }}</td>
                            <td><span class="badge bg-warning">Pending</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body text-center text-muted">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <p>All sales are synced. No pending sales.</p>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    function toggleOffline(mode) {
        const url = mode ? '{{ route("sync.offline") }}' : '{{ route("sync.online") }}';
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            location.reload();
        })
        .catch(error => {
            alert('Error: ' + error);
        });
    }

    function syncNow() {
        if (!confirm('Are you sure you want to sync all pending sales?')) {
            return;
        }
        
        fetch('{{ route("sync.now") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error);
        });
    }

    // Check sync status periodically
    function checkStatus() {
        fetch('{{ route("sync.status") }}')
            .then(response => response.json())
            .then(data => {
                // Update UI if needed
            })
            .catch(error => console.error('Status check error:', error));
    }
    
    // Check status every 30 seconds
    setInterval(checkStatus, 30000);
</script>
@endpush
@endsection