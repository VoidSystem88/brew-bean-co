@extends('layouts.app')

@section('page-title', 'Deliveries')

@section('content')
<style>
    .delivery-card {
        background: white;
        border-radius: 12px;
        padding: 15px 20px;
        border: 1px solid #e8e8e8;
        margin-bottom: 12px;
        transition: 0.2s;
    }
    .delivery-card:hover {
        border-color: #6F4E37;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .status-badge {
        padding: 2px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-badge.pending {
        background: #fff3cd;
        color: #856404;
    }
    .status-badge.received {
        background: #d4edda;
        color: #155724;
    }
    .btn-receive {
        background: #28a745;
        color: white;
        border: none;
        padding: 6px 20px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-receive:hover {
        background: #1e7e34;
        color: white;
    }
    .btn-receive:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .pending-count {
        background: #ffc107;
        color: #000;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .delivery-checkbox {
        transform: scale(1.2);
        margin-right: 10px;
    }
    .bulk-actions {
        background: #f8f9fa;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
    }
    .bulk-actions .selected-count {
        font-weight: 600;
        color: #6F4E37;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-truck me-2"></i>Deliveries</h2>
        <div>
            @if($pendingCount > 0)
                <span class="pending-count me-2">
                    <i class="fas fa-clock me-1"></i> {{ $pendingCount }} pending
                </span>
            @endif
            <span class="view-only-badge me-2"><i class="fas fa-eye me-1"></i> Staff View</span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Pending Deliveries -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Pending Deliveries</h6>
        </div>
        <div class="card-body">
            @if($pendingTransfers->count() > 0)
                <div class="bulk-actions">
                    <div>
                        <input type="checkbox" id="selectAll" onchange="toggleAll()">
                        <label for="selectAll" class="ms-1">Select All</label>
                        <span class="selected-count ms-3" id="selectedCount">0 selected</span>
                    </div>
                    <button class="btn-receive" onclick="receiveSelected()">
                        <i class="fas fa-check-double me-1"></i> Receive Selected
                    </button>
                </div>

                @foreach($pendingTransfers as $transfer)
                    <div class="delivery-card">
                        <div class="row align-items-center">
                            <div class="col-md-1">
                                <input type="checkbox" class="delivery-checkbox" data-transfer-id="{{ $transfer->id }}" onchange="updateSelected()">
                            </div>
                            <div class="col-md-3">
                                <div class="fw-bold">{{ $transfer->item->name ?? 'Unknown Item' }}</div>
                                <div class="text-muted" style="font-size: 13px;">
                                    <i class="fas fa-box me-1"></i>
                                    Qty: {{ number_format($transfer->quantity, 2) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted" style="font-size: 13px;">
                                    <i class="fas fa-warehouse me-1"></i>
                                    From: {{ str_replace('☕ Brew & Bean Co. - ', '', $transfer->fromBranch->name ?? 'Warehouse') }}
                                </div>
                                <div class="text-muted" style="font-size: 12px;">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $transfer->created_at->format('M d, Y h:i A') }}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <span class="status-badge pending">⏳ Pending</span>
                            </div>
                            <div class="col-md-3 text-end">
                                <button class="btn-receive" onclick="receiveSingle({{ $transfer->id }})">
                                    <i class="fas fa-check-circle me-1"></i> Receive
                                </button>
                                @if($transfer->notes)
                                    <div class="text-muted" style="font-size: 11px; margin-top: 4px;">
                                        <i class="fas fa-comment me-1"></i> {{ $transfer->notes }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-2 d-block"></i>
                    <p class="text-muted">No pending deliveries</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Completed Deliveries -->
    @if($completedTransfers->count() > 0)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Completed Deliveries</h6>
                <small class="text-muted">Last 20 deliveries</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 14px;">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>From</th>
                                <th>Received</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completedTransfers as $transfer)
                                <tr>
                                    <td>{{ $transfer->item->name ?? 'Unknown' }}</td>
                                    <td>{{ number_format($transfer->quantity, 2) }}</td>
                                    <td>{{ str_replace('☕ Brew & Bean Co. - ', '', $transfer->fromBranch->name ?? 'Warehouse') }}</td>
                                    <td>
                                        {{ $transfer->received_at ? $transfer->received_at->format('M d, Y h:i A') : 'N/A' }}
                                        <br>
                                        <small class="text-muted">by {{ $transfer->receivedBy->name ?? 'Staff' }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    let selectedTransfers = [];

    function toggleAll() {
        const selectAll = document.getElementById('selectAll');
        document.querySelectorAll('.delivery-checkbox').forEach(cb => {
            cb.checked = selectAll.checked;
        });
        updateSelected();
    }

    function updateSelected() {
        selectedTransfers = [];
        document.querySelectorAll('.delivery-checkbox:checked').forEach(cb => {
            selectedTransfers.push(cb.getAttribute('data-transfer-id'));
        });
        document.getElementById('selectedCount').textContent = selectedTransfers.length + ' selected';
    }

    function receiveSingle(id) {
        if (!confirm('Confirm that you have received this delivery?')) {
            return;
        }

        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

        fetch(`/delivery/receive/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            alert('Error: ' + error);
        });
    }

    function receiveSelected() {
        if (selectedTransfers.length === 0) {
            alert('Please select at least one delivery to receive.');
            return;
        }

        if (!confirm(`Receive ${selectedTransfers.length} delivery(s)?`)) {
            return;
        }

        const btn = document.querySelector('.bulk-actions .btn-receive');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';

        fetch('{{ route("delivery.bulk-receive") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                transfer_ids: selectedTransfers.map(id => parseInt(id))
            })
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            alert('Error: ' + error);
        });
    }
</script>
@endpush
@endsection