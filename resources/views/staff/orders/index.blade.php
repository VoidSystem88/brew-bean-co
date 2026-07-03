@extends('layouts.app')

@section('page-title', 'Online Orders')

@section('content')
<style>
    .order-card {
        background: white;
        border-radius: 12px;
        padding: 16px;
        border: 1px solid #e8e8e8;
        margin-bottom: 12px;
        transition: 0.2s;
        cursor: pointer;
    }
    .order-card:hover {
        border-color: #6F4E37;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .order-card .order-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 6px;
    }
    .order-card .order-id {
        font-weight: 700;
        color: #6F4E37;
        font-size: 16px;
    }
    .order-card .order-customer {
        font-size: 14px;
        color: #333;
    }
    .order-card .order-items {
        font-size: 13px;
        color: #666;
        margin: 4px 0;
    }
    .order-card .order-total {
        font-weight: 600;
        color: #6F4E37;
        font-size: 16px;
    }
    .order-card .order-time {
        font-size: 12px;
        color: #999;
    }
    .order-card .order-actions {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 8px;
    }
    .order-card .order-actions .btn {
        font-size: 12px;
        padding: 4px 14px;
    }
    
    .status-badge {
        padding: 2px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    .status-badge.pending { background: #fff3cd; color: #856404; }
    .status-badge.preparing { background: #cce5ff; color: #004085; }
    .status-badge.ready { background: #d4edda; color: #155724; }
    .status-badge.completed { background: #d1ecf1; color: #0c5460; }
    .status-badge.cancelled { background: #f8d7da; color: #721c24; }
    
    .status-tabs {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
        margin-bottom: 16px;
        background: white;
        padding: 8px;
        border-radius: 10px;
        border: 1px solid #e8e8e8;
    }
    .status-tabs .tab {
        padding: 6px 16px;
        border-radius: 8px;
        border: none;
        background: transparent;
        font-weight: 500;
        font-size: 13px;
        cursor: pointer;
        transition: 0.2s;
        color: #666;
    }
    .status-tabs .tab:hover {
        background: #f5f0eb;
    }
    .status-tabs .tab.active {
        background: #6F4E37;
        color: white;
    }
    .status-tabs .tab .count {
        background: rgba(255,255,255,0.3);
        padding: 0 8px;
        border-radius: 10px;
        font-size: 11px;
        margin-left: 4px;
    }
    .status-tabs .tab.active .count {
        background: rgba(255,255,255,0.2);
    }
    
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #999;
    }
    .empty-state i {
        font-size: 48px;
        display: block;
        margin-bottom: 12px;
        color: #ddd;
    }
    
    .delivery-badge {
        font-size: 11px;
        padding: 2px 10px;
        border-radius: 12px;
    }
    .delivery-badge.pickup { background: #e8f5e9; color: #2e7d32; }
    .delivery-badge.delivery { background: #e3f2fd; color: #0d47a1; }
    
    @media (max-width: 576px) {
        .order-card .order-header {
            flex-direction: column;
        }
        .status-tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
        }
        .status-tabs .tab {
            white-space: nowrap;
            font-size: 12px;
            padding: 4px 12px;
        }
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-clipboard-list me-2"></i>Online Orders</h2>
        <button class="btn btn-sm btn-outline-secondary" onclick="refreshOrders()">
            <i class="fas fa-sync me-1"></i> Refresh
        </button>
    </div>

    <!-- Status Tabs -->
    <div class="status-tabs" id="statusTabs">
        <button class="tab active" data-tab="all" onclick="switchTab('all')">
            All <span class="count">{{ $statusCounts['total'] }}</span>
        </button>
        <button class="tab" data-tab="pending" onclick="switchTab('pending')">
            ⏳ Pending <span class="count">{{ $statusCounts['pending'] }}</span>
        </button>
        <button class="tab" data-tab="preparing" onclick="switchTab('preparing')">
            🔄 Preparing <span class="count">{{ $statusCounts['preparing'] }}</span>
        </button>
        <button class="tab" data-tab="ready" onclick="switchTab('ready')">
            ✅ Ready <span class="count">{{ $statusCounts['ready'] }}</span>
        </button>
        <button class="tab" data-tab="completed" onclick="switchTab('completed')">
            ✔️ Completed <span class="count">{{ $statusCounts['completed'] }}</span>
        </button>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content active" id="tab-all">
        @include('staff.orders.partials.order-list', ['sales' => $sales])
    </div>
    <div class="tab-content" id="tab-pending">
        @include('staff.orders.partials.order-list', ['sales' => $pendingOrders])
    </div>
    <div class="tab-content" id="tab-preparing">
        @include('staff.orders.partials.order-list', ['sales' => $preparingOrders])
    </div>
    <div class="tab-content" id="tab-ready">
        @include('staff.orders.partials.order-list', ['sales' => $readyOrders])
    </div>
    <div class="tab-content" id="tab-completed">
        @include('staff.orders.partials.order-list', ['sales' => $completedOrders])
    </div>
</div>

<!-- Order Detail Modal -->
<div class="modal fade" id="orderDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetailContent">
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                    <p class="mt-2">Loading...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentTab = 'all';
    let refreshInterval = null;

    function switchTab(tab) {
        currentTab = tab;
        
        // Update tabs
        document.querySelectorAll('.status-tabs .tab').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === tab);
        });
        
        // Update content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.toggle('active', content.id === 'tab-' + tab);
        });
    }

    function viewOrder(saleId) {
        const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
        const content = document.getElementById('orderDetailContent');
        
        content.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                <p class="mt-2">Loading order details...</p>
            </div>
        `;
        
        modal.show();
        
        fetch(`/staff/orders/${saleId}`)
            .then(response => response.text())
            .then(html => {
                content.innerHTML = html;
            })
            .catch(error => {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        Error loading order details. Please try again.
                    </div>
                `;
            });
    }

    function updateOrderStatus(orderId, status, saleId) {
        if (!confirm(`Update this order to "${status}"?`)) return;
        
        const btn = document.querySelector(`[data-order="${orderId}"] .order-actions .btn-primary`);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }
        
        fetch(`/staff/orders/${orderId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                refreshOrders();
                showNotification('success', data.message);
            } else {
                showNotification('danger', data.message);
            }
        })
        .catch(error => {
            showNotification('danger', 'Error updating order status');
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = 'Update';
            }
        });
    }

    function acceptOrder(saleId) {
        if (!confirm('Accept this order?')) return;
        
        const btn = document.querySelector(`[data-sale="${saleId}"] .btn-accept`);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }
        
        fetch(`/staff/orders/${saleId}/accept`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                refreshOrders();
                showNotification('success', data.message);
            } else {
                showNotification('danger', data.message);
            }
        })
        .catch(error => {
            showNotification('danger', 'Error accepting order');
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = 'Accept';
            }
        });
    }

    function markReady(saleId) {
        if (!confirm('Mark this order as ready?')) return;
        
        const btn = document.querySelector(`[data-sale="${saleId}"] .btn-ready`);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }
        
        fetch(`/staff/orders/${saleId}/ready`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                refreshOrders();
                showNotification('success', data.message);
            } else {
                showNotification('danger', data.message);
            }
        })
        .catch(error => {
            showNotification('danger', 'Error marking order ready');
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = 'Ready';
            }
        });
    }

    function completeOrder(saleId) {
        if (!confirm('Mark this order as completed?')) return;
        
        const btn = document.querySelector(`[data-sale="${saleId}"] .btn-complete`);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }
        
        fetch(`/staff/orders/${saleId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                refreshOrders();
                showNotification('success', data.message);
            } else {
                showNotification('danger', data.message);
            }
        })
        .catch(error => {
            showNotification('danger', 'Error completing order');
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = 'Complete';
            }
        });
    }

    function refreshOrders() {
        window.location.reload();
    }

    function showNotification(type, message) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.style.position = 'fixed';
        alert.style.top = '20px';
        alert.style.right = '20px';
        alert.style.zIndex = '9999';
        alert.style.maxWidth = '400px';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }

    // Auto-refresh every 30 seconds
    document.addEventListener('DOMContentLoaded', function() {
        refreshInterval = setInterval(() => {
            // Check for new orders silently
            fetch('/staff/orders/new')
                .then(response => response.json())
                .then(data => {
                    if (data.count > 0) {
                        showNotification('info', `📦 ${data.count} new order(s) received!`);
                        const pendingTab = document.querySelector('[data-tab="pending"] .count');
                        if (pendingTab) {
                            const currentCount = parseInt(pendingTab.textContent) || 0;
                            pendingTab.textContent = currentCount + data.count;
                        }
                    }
                })
                .catch(() => {});
        }, 30000);
    });
</script>
@endpush
@endsection