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
        display: inline-block;
    }
    .status-badge.pending { background: #fff3cd; color: #856404; }
    .status-badge.assigned { background: #cce5ff; color: #004085; }
    .status-badge.picked_up { background: #d1ecf1; color: #0c5460; }
    .status-badge.in_transit { background: #d4edda; color: #155724; }
    .status-badge.completed { background: #d4edda; color: #155724; }
    .status-badge.failed { background: #f8d7da; color: #721c24; }
    
    .btn-assign {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-assign:hover {
        background: #5a3d2b;
        color: white;
    }
    .btn-deliver {
        background: #28a745;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-deliver:hover {
        background: #218838;
        color: white;
    }
    .btn-fail {
        background: #dc3545;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-fail:hover {
        background: #c82333;
        color: white;
    }
    .tracking-timeline {
        border-left: 2px solid #6F4E37;
        padding-left: 15px;
        margin: 10px 0;
    }
    .tracking-timeline .track-item {
        padding: 4px 0;
        font-size: 12px;
        color: #666;
        position: relative;
    }
    .tracking-timeline .track-item::before {
        content: "";
        position: absolute;
        left: -20px;
        top: 8px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #6F4E37;
        border: 2px solid white;
    }
    .tracking-timeline .track-item.completed::before {
        background: #28a745;
    }
    .tracking-timeline .track-item .time {
        font-size: 10px;
        color: #999;
        margin-left: 8px;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-truck me-2"></i>Deliveries</h2>
        <div>
            @if($deliveryCount > 0)
                <span class="badge bg-warning me-2">
                    <i class="fas fa-clock me-1"></i> {{ $deliveryCount }} pending
                </span>
            @endif
            <span class="badge bg-secondary">Delivery Management</span>
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

    <!-- Pending Transfers -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Pending Transfers</h6>
        </div>
        <div class="card-body">
            @if($pendingTransfers->count() > 0)
                @foreach($pendingTransfers as $transfer)
                    <div class="delivery-card">
                        <div class="row align-items-center">
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
                            <div class="col-md-4 text-end">
                                <button class="btn-deliver" onclick="receiveTransfer({{ $transfer->id }})">
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
                    <p class="text-muted">No pending transfers</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Pending Deliveries -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Pending Deliveries</h6>
        </div>
        <div class="card-body">
            @if($deliveryOrders->count() > 0)
                @foreach($deliveryOrders as $order)
                    <div class="delivery-card">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="fw-bold">Order #{{ $order->id }}</div>
                                <div class="text-muted" style="font-size: 12px;">
                                    {{ $order->customer->name ?? 'Unknown' }}
                                </div>
                                <div style="font-size: 11px; color: #999;">
                                    <i class="fas fa-map-pin me-1"></i> {{ $order->delivery_address }}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <span class="status-badge {{ $order->delivery_status }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->delivery_status)) }}
                                </span>
                            </div>
                            <div class="col-md-2">
                                <div style="font-size: 13px; font-weight: 600; color: #6F4E37;">
                                    ₱{{ number_format($order->total_amount, 2) }}
                                </div>
                                <div style="font-size: 11px; color: #999;">
                                    {{ $order->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                @if($order->delivery_person_id)
                                    <div style="font-size: 12px; color: #666;">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $order->deliveryPerson->name ?? 'Unknown' }}
                                    </div>
                                    <div style="font-size: 11px; color: #999;">
                                        @php
                                            $assignedAt = $order->delivery_assigned_at;
                                            $assignedText = $assignedAt ? (is_string($assignedAt) ? \Carbon\Carbon::parse($assignedAt)->diffForHumans() : $assignedAt->diffForHumans()) : 'N/A';
                                        @endphp
                                        Assigned: {{ $assignedText }}
                                    </div>
                                @else
                                    <form onsubmit="assignDelivery(event, {{ $order->id }})">
                                        <select name="delivery_person_id" class="form-select form-select-sm" required>
                                            <option value="">Assign to...</option>
                                            @foreach($deliveryStaff as $staff)
                                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn-assign btn-sm mt-1 w-100">
                                            <i class="fas fa-user-plus me-1"></i> Assign
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="col-md-2 text-end">
                                @if($order->delivery_status == 'assigned')
                                    <button class="btn-deliver btn-sm" onclick="updateDeliveryStatus({{ $order->id }}, 'picked_up')">
                                        <i class="fas fa-box"></i> Pick Up
                                    </button>
                                @endif
                                @if($order->delivery_status == 'picked_up')
                                    <button class="btn-deliver btn-sm" onclick="updateDeliveryStatus({{ $order->id }}, 'in_transit')">
                                        <i class="fas fa-truck"></i> In Transit
                                    </button>
                                @endif
                                @if($order->delivery_status == 'in_transit')
                                    <button class="btn-deliver btn-sm" onclick="updateDeliveryStatus({{ $order->id }}, 'completed')">
                                        <i class="fas fa-check-circle"></i> Confirm Delivery
                                    </button>
                                    <button class="btn-fail btn-sm mt-1" onclick="failDelivery({{ $order->id }})">
                                        <i class="fas fa-times-circle"></i> Failed
                                    </button>
                                @endif
                                <button class="btn btn-sm btn-outline-secondary mt-1" onclick="viewTracking({{ $order->id }})">
                                    <i class="fas fa-clock"></i> Track
                                </button>
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
    @if($completedDeliveries->count() > 0)
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
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Address</th>
                                <th>Delivery Person</th>
                                <th>Completed At</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completedDeliveries as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->customer->name ?? 'Unknown' }}</td>
                                    <td style="font-size: 12px;">{{ Str::limit($order->delivery_address, 30) }}</td>
                                    <td>{{ $order->deliveryPerson->name ?? 'Unknown' }}</td>
                                    <td>
                                        @php
                                            $completedAt = $order->delivery_completed_at;
                                            $completedText = $completedAt ? (is_string($completedAt) ? \Carbon\Carbon::parse($completedAt)->format('M d, Y h:i A') : $completedAt->format('M d, Y h:i A')) : 'N/A';
                                        @endphp
                                        {{ $completedText }}
                                    </td>
                                    <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Tracking Modal -->
<div class="modal fade" id="trackingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delivery Tracking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="trackingContent">
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                    <p class="mt-2">Loading tracking...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function receiveTransfer(id) {
        if (!confirm('Confirm that you have received this delivery?')) {
            return;
        }

        fetch(`/delivery/receive/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error);
        });
    }

    function assignDelivery(event, saleId) {
        event.preventDefault();
        const form = event.target;
        const select = form.querySelector('select');
        const deliveryPersonId = select.value;
        
        if (!deliveryPersonId) {
            alert('Please select a delivery person.');
            return;
        }

        const btn = form.querySelector('.btn-assign');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

        fetch('/delivery/assign', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                sale_id: saleId,
                delivery_person_id: deliveryPersonId
            })
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-user-plus me-1"></i> Assign';
            
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-user-plus me-1"></i> Assign';
            alert('Error: ' + error);
        });
    }

    function updateDeliveryStatus(saleId, status) {
        const statusLabels = {
            'picked_up': 'Pick up this order?',
            'in_transit': 'Mark as in transit?',
            'completed': 'Confirm delivery?'
        };
        
        if (!confirm('Are you sure you want to ' + (statusLabels[status] || 'update this order?'))) {
            return;
        }

        fetch(`/delivery/${saleId}/${status}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error);
        });
    }

    function failDelivery(saleId) {
        const reason = prompt('Reason for delivery failure:');
        if (reason === null) return;
        
        fetch(`/delivery/${saleId}/fail`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ reason: reason || 'Delivery failed' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error);
        });
    }

    function viewTracking(saleId) {
        const modal = new bootstrap.Modal(document.getElementById('trackingModal'));
        const content = document.getElementById('trackingContent');
        
        content.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                <p class="mt-2">Loading tracking...</p>
            </div>
        `;
        
        modal.show();
        
        fetch(`/delivery/tracking/${saleId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.tracking.length > 0) {
                    let html = '<div class="tracking-timeline">';
                    data.tracking.forEach(track => {
                        const statusClass = track.status === 'delivered' ? 'completed' : '';
                        html += `
                            <div class="track-item ${statusClass}">
                                <strong>${track.status.replace('_', ' ').toUpperCase()}</strong>
                                <span class="time">${new Date(track.created_at).toLocaleString()}</span>
                                ${track.notes ? `<div style="font-size:11px;color:#999;">${track.notes}</div>` : ''}
                                ${track.user ? `<div style="font-size:10px;color:#999;">by ${track.user.name}</div>` : ''}
                            </div>
                        `;
                    });
                    html += '</div>';
                    content.innerHTML = html;
                } else {
                    content.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-info-circle fa-2x text-muted"></i>
                            <p class="mt-2">No tracking information available</p>
                        </div>
                    `;
                }
            })
            .catch(() => {
                content.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-circle fa-2x text-danger"></i>
                        <p class="mt-2">Error loading tracking</p>
                    </div>
                `;
            });
    }
</script>
@endpush
@endsection