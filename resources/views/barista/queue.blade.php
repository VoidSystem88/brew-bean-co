@extends('layouts.app')

@section('page-title', 'Barista Queue')

@section('content')
<style>
    .queue-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 15px;
        background: white;
        padding: 10px 16px;
        border-radius: 10px;
        border: 1px solid #e8e8e8;
    }
    
    .queue-header .title-section {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .queue-header .title-section h2 {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        color: #333;
    }
    
    .queue-header .title-section h2 i {
        color: #6F4E37;
        margin-right: 8px;
    }
    
    .btn-refresh {
        background: transparent;
        border: 1px solid #e8e8e8;
        border-radius: 6px;
        padding: 6px 14px;
        font-size: 13px;
        color: #666;
        cursor: pointer;
        transition: 0.2s;
    }
    
    .btn-refresh:hover {
        background: #f5f0eb;
        border-color: #6F4E37;
    }
    
    .btn-refresh i {
        margin-right: 4px;
    }
    
    .btn-pos {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 6px 16px;
        border-radius: 6px;
        font-size: 13px;
        cursor: pointer;
        transition: 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-pos:hover {
        background: #5a3d2b;
        color: white;
    }
    
    .queue-stats-mini {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .queue-stats-mini .stat-pill {
        background: #f8f6f4;
        padding: 4px 12px;
        border-radius: 14px;
        font-size: 12px;
        font-weight: 500;
        color: #666;
        display: flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    
    .queue-stats-mini .stat-pill .num {
        font-weight: 700;
        color: #6F4E37;
    }
    
    .queue-stats-mini .stat-pill.pending { background: #fff3cd; color: #856404; }
    .queue-stats-mini .stat-pill.preparing { background: #cce5ff; color: #004085; }
    .queue-stats-mini .stat-pill.ready { background: #d4edda; color: #155724; }
    
    .queue-stats-mini .stat-pill .num {
        color: inherit;
    }
    
    .queue-container {
        display: flex;
        flex-direction: column;
        gap: 16px;
        max-height: calc(100vh - 180px);
        overflow-y: auto;
        padding-right: 4px;
    }
    
    .queue-container::-webkit-scrollbar {
        width: 5px;
    }
    .queue-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .queue-container::-webkit-scrollbar-thumb {
        background: #6F4E37;
        border-radius: 10px;
    }
    
    .queue-section {
        background: white;
        border-radius: 10px;
        border: 1px solid #e8e8e8;
        overflow: hidden;
        flex-shrink: 0;
    }
    
    .queue-section .section-header {
        padding: 10px 16px;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 2px solid #e8e8e8;
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fafafa;
    }
    
    .queue-section .section-header .badge-count {
        background: #6F4E37;
        color: white;
        padding: 0 10px;
        border-radius: 10px;
        font-size: 12px;
        margin-left: auto;
    }
    
    .queue-section .section-header.in-store {
        border-bottom-color: #6F4E37;
    }
    
    .queue-section .section-header.online {
        border-bottom-color: #1976d2;
    }
    
    .queue-section .section-header .header-icon {
        font-size: 16px;
        width: 24px;
        text-align: center;
    }
    
    .queue-columns {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 0;
        min-height: 120px;
    }
    
    .queue-column {
        padding: 10px;
        border-right: 1px solid #f0f0f0;
        min-height: 120px;
        max-height: 350px;
        overflow-y: auto;
    }
    
    .queue-column:last-child {
        border-right: none;
    }
    
    .queue-column::-webkit-scrollbar {
        width: 4px;
    }
    .queue-column::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .queue-column::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 10px;
    }
    
    .queue-column .column-title {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        color: #999;
        text-align: center;
        padding-bottom: 8px;
        border-bottom: 2px solid #f0f0f0;
        margin-bottom: 8px;
        position: sticky;
        top: 0;
        background: white;
        z-index: 2;
        letter-spacing: 0.5px;
    }
    
    .queue-column .column-title .count {
        background: #f0f0f0;
        padding: 0 8px;
        border-radius: 10px;
        font-size: 11px;
        margin-left: 4px;
    }
    
    .queue-column.status-pending .column-title { border-bottom-color: #ffc107; color: #856404; }
    .queue-column.status-preparing .column-title { border-bottom-color: #17a2b8; color: #004085; }
    .queue-column.status-serve .column-title { border-bottom-color: #28a745; color: #155724; }
    .queue-column.status-deliver .column-title { border-bottom-color: #0d47a1; color: #0d47a1; }
    
    .order-card-small {
        background: white;
        border-radius: 8px;
        padding: 10px 12px;
        margin-bottom: 8px;
        border: 1px solid #eee;
        transition: 0.15s;
        cursor: pointer;
        position: relative;
    }
    
    .order-card-small:hover {
        border-color: #6F4E37;
        box-shadow: 0 1px 6px rgba(0,0,0,0.06);
    }
    
    .order-card-small .order-id {
        font-weight: 700;
        color: #6F4E37;
        font-size: 14px;
    }
    
    .order-card-small .order-customer {
        font-size: 13px;
        color: #333;
        font-weight: 500;
    }
    
    .order-card-small .order-items {
        font-size: 12px;
        color: #999;
    }
    
    .order-card-small .order-total {
        font-weight: 600;
        color: #6F4E37;
        font-size: 14px;
    }
    
    .order-card-small .order-time {
        font-size: 11px;
        color: #bbb;
    }
    
    .order-card-small .order-actions {
        display: flex;
        gap: 6px;
        margin-top: 8px;
        flex-wrap: wrap;
    }
    
    .order-card-small .order-actions .btn {
        font-size: 11px;
        padding: 4px 14px;
        border-radius: 12px;
        font-weight: 600;
    }
    
    .order-card-small .order-type-badge {
        font-size: 10px;
        padding: 1px 10px;
        border-radius: 10px;
        position: absolute;
        top: 6px;
        right: 6px;
    }
    
    .order-type-badge.in-store {
        background: #f8f6f4;
        color: #6F4E37;
    }
    
    .order-type-badge.online {
        background: #e3f2fd;
        color: #0d47a1;
    }
    
    .empty-column {
        text-align: center;
        padding: 30px 10px;
        color: #ccc;
        font-size: 13px;
    }
    
    .empty-column i {
        font-size: 28px;
        display: block;
        margin-bottom: 8px;
        color: #eee;
    }
    
    .btn-complete-order {
        background: #28a745;
        color: white;
        border: none;
        padding: 4px 16px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-complete-order:hover {
        background: #218838;
        color: white;
    }
    
    .btn-accept {
        background: #28a745;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-accept:hover {
        background: #218838;
        color: white;
    }
    
    .btn-ready {
        background: #17a2b8;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-ready:hover {
        background: #138496;
        color: white;
    }
    
    .btn-cancel {
        background: #dc3545;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-cancel:hover {
        background: #c82333;
        color: white;
    }
    
    .btn-view {
        background: #6c757d;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-view:hover {
        background: #5a6268;
        color: white;
    }
    
    .btn-assign-rider {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-assign-rider:hover {
        background: #5a3d2b;
        color: white;
    }
    
    .notification-toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        z-index: 9999;
        display: none;
        animation: slideUp 0.3s ease;
        font-size: 14px;
    }
    
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .order-detail-modal .modal-content {
        border-radius: 12px;
    }
    
    @media (max-width: 992px) {
        .queue-columns {
            grid-template-columns: 1fr 1fr 1fr;
        }
        .queue-column {
            max-height: 260px;
        }
        .queue-header {
            flex-direction: column;
            align-items: stretch;
        }
        .queue-header .title-section {
            justify-content: center;
        }
        .queue-stats-mini {
            justify-content: center;
        }
    }
    
    @media (max-width: 576px) {
        .queue-columns {
            grid-template-columns: 1fr;
        }
        .queue-column {
            border-right: none;
            border-bottom: 1px solid #f0f0f0;
            min-height: 80px;
            max-height: 200px;
        }
        .queue-column:last-child {
            border-bottom: none;
        }
        .queue-container {
            max-height: calc(100vh - 160px);
        }
        .queue-header .title-section h2 {
            font-size: 16px;
        }
        .queue-stats-mini .stat-pill {
            font-size: 10px;
            padding: 2px 8px;
        }
        .order-card-small {
            padding: 8px 10px;
        }
        .order-card-small .order-id {
            font-size: 13px;
        }
        .order-card-small .order-actions .btn {
            font-size: 10px;
            padding: 3px 10px;
        }
    }
</style>

<!-- Queue Header -->
<div class="queue-header">
    <div class="title-section">
        <h2><i class="fas fa-mug-hot"></i> Barista Queue</h2>
        <button class="btn-refresh" onclick="refreshQueue()">
            <i class="fas fa-sync-alt"></i> <span id="lastUpdate">Now</span>
        </button>
        <a href="{{ route('pos.index') }}" class="btn-pos">
            <i class="fas fa-cash-register"></i> POS
        </a>
    </div>
    <div class="queue-stats-mini">
        <span class="stat-pill"><i class="fas fa-store"></i> In-Store: <span class="num" id="inStoreTotalStat">0</span></span>
        <span class="stat-pill"><i class="fas fa-shopping-cart"></i> Online: <span class="num" id="onlineTotalStat">0</span></span>
        <span class="stat-pill pending"><i class="fas fa-clock"></i> <span class="num" id="pendingTotalStat">0</span></span>
        <span class="stat-pill preparing"><i class="fas fa-spinner"></i> <span class="num" id="preparingTotalStat">0</span></span>
        <span class="stat-pill ready"><i class="fas fa-check"></i> <span class="num" id="readyTotalStat">0</span></span>
    </div>
</div>

<!-- Queue Containers -->
<div class="queue-container" id="queueContainer">
    <!-- In-Store Orders -->
    <div class="queue-section">
        <div class="section-header in-store">
            <span class="header-icon"><i class="fas fa-store"></i></span>
            In-Store Orders
            <span class="badge-count" id="inStoreTotal">0</span>
        </div>
        <div class="queue-columns">
            <div class="queue-column status-pending" id="inStorePending">
                <div class="column-title">
                    <i class="fas fa-clock"></i> Pending
                    <span class="count" id="inStorePendingCount">0</span>
                </div>
                <div id="inStorePendingList">
                    <div class="empty-column">
                        <i class="fas fa-inbox"></i>
                        No orders
                    </div>
                </div>
            </div>
            <div class="queue-column status-preparing" id="inStorePreparing">
                <div class="column-title">
                    <i class="fas fa-spinner"></i> Preparing
                    <span class="count" id="inStorePreparingCount">0</span>
                </div>
                <div id="inStorePreparingList">
                    <div class="empty-column">
                        <i class="fas fa-inbox"></i>
                        No orders
                    </div>
                </div>
            </div>
            <div class="queue-column status-serve" id="inStoreServe">
                <div class="column-title">
                    <i class="fas fa-check-circle"></i> Serve
                    <span class="count" id="inStoreServeCount">0</span>
                </div>
                <div id="inStoreServeList">
                    <div class="empty-column">
                        <i class="fas fa-inbox"></i>
                        No orders
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Online Orders -->
    <div class="queue-section">
        <div class="section-header online">
            <span class="header-icon"><i class="fas fa-shopping-cart"></i></span>
            Online Orders
            <span class="badge-count" id="onlineTotal">0</span>
        </div>
        <div class="queue-columns">
            <div class="queue-column status-pending" id="onlinePending">
                <div class="column-title">
                    <i class="fas fa-clock"></i> Pending
                    <span class="count" id="onlinePendingCount">0</span>
                </div>
                <div id="onlinePendingList">
                    <div class="empty-column">
                        <i class="fas fa-inbox"></i>
                        No orders
                    </div>
                </div>
            </div>
            <div class="queue-column status-preparing" id="onlinePreparing">
                <div class="column-title">
                    <i class="fas fa-spinner"></i> Preparing
                    <span class="count" id="onlinePreparingCount">0</span>
                </div>
                <div id="onlinePreparingList">
                    <div class="empty-column">
                        <i class="fas fa-inbox"></i>
                        No orders
                    </div>
                </div>
            </div>
            <div class="queue-column status-deliver" id="onlineDeliver">
                <div class="column-title">
                    <i class="fas fa-truck"></i> Assign Rider
                    <span class="count" id="onlineDeliverCount">0</span>
                </div>
                <div id="onlineDeliverList">
                    <div class="empty-column">
                        <i class="fas fa-inbox"></i>
                        No orders
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Rider Modal -->
<div class="modal fade" id="assignRiderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-truck me-2"></i>Assign Delivery Rider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="assign_sale_id">
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Rider</label>
                    <select id="rider_select" class="form-select">
                        <option value="">-- Select Rider --</option>
                    </select>
                </div>
                <div class="text-muted" style="font-size:12px;">
                    <i class="fas fa-info-circle"></i> 
                    Only delivery riders will appear in the list.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmAssignRider()">
                    <i class="fas fa-check me-1"></i> Assign Rider
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Order Detail Modal -->
<div class="modal fade order-detail-modal" id="orderDetailModal" tabindex="-1">
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

<!-- Notification Toast -->
<div class="notification-toast" id="notificationToast">
    <i class="fas fa-check-circle me-2"></i>
    <span id="notificationMessage">Order updated!</span>
</div>

@push('scripts')
<script>
    let refreshInterval = null;
    let isRefreshing = false;

    function loadQueue() {
        if (isRefreshing) return;
        isRefreshing = true;
        
        fetch('/barista/queue-data')
            .then(response => response.json())
            .then(data => {
                renderQueue(data);
                document.getElementById('lastUpdate').textContent = 'Now';
                isRefreshing = false;
            })
            .catch(error => {
                console.error('Error loading queue:', error);
                isRefreshing = false;
            });
    }

    function renderQueue(data) {
        renderColumn('inStorePending', data.in_store.pending);
        renderColumn('inStorePreparing', data.in_store.preparing);
        renderColumn('inStoreServe', data.in_store.serve);
        renderColumn('onlinePending', data.online.pending);
        renderColumn('onlinePreparing', data.online.preparing);
        renderColumn('onlineDeliver', data.online.deliver);
        
        const inStoreTotal = data.in_store.pending.length + data.in_store.preparing.length + data.in_store.serve.length;
        const onlineTotal = data.online.pending.length + data.online.preparing.length + data.online.deliver.length;
        const pendingTotal = data.in_store.pending.length + data.online.pending.length;
        const preparingTotal = data.in_store.preparing.length + data.online.preparing.length;
        const readyTotal = data.in_store.serve.length + data.online.deliver.length;
        
        document.getElementById('inStorePendingCount').textContent = data.in_store.pending.length;
        document.getElementById('inStorePreparingCount').textContent = data.in_store.preparing.length;
        document.getElementById('inStoreServeCount').textContent = data.in_store.serve.length;
        document.getElementById('inStoreTotal').textContent = inStoreTotal;
        document.getElementById('inStoreTotalStat').textContent = inStoreTotal;
            
        document.getElementById('onlinePendingCount').textContent = data.online.pending.length;
        document.getElementById('onlinePreparingCount').textContent = data.online.preparing.length;
        document.getElementById('onlineDeliverCount').textContent = data.online.deliver.length;
        document.getElementById('onlineTotal').textContent = onlineTotal;
        document.getElementById('onlineTotalStat').textContent = onlineTotal;
        
        document.getElementById('pendingTotalStat').textContent = pendingTotal;
        document.getElementById('preparingTotalStat').textContent = preparingTotal;
        document.getElementById('readyTotalStat').textContent = readyTotal;
    }

    function renderColumn(containerId, orders) {
        const container = document.getElementById(containerId + 'List');
        if (!container) return;
        
        if (orders.length === 0) {
            container.innerHTML = `
                <div class="empty-column">
                    <i class="fas fa-inbox"></i>
                    No orders
                </div>
            `;
            return;
        }
        
        let html = '';
        orders.forEach(order => {
            const status = order.status || 'pending';
            const isOnline = order.type === 'online';
            const typeBadge = isOnline ? 'online' : 'in-store';
            const typeLabel = isOnline ? 'Online' : 'In-Store';
            
            html += `
                <div class="order-card-small" onclick="viewOrder(${order.sale_id})">
                    <span class="order-type-badge ${typeBadge}">${typeLabel}</span>
                    <div class="order-id">#${order.sale_id}</div>
                    <div class="order-customer">
                        <i class="fas fa-user"></i> ${order.customer_name || 'Walk-in'}
                    </div>
                    <div class="order-items">
                        <i class="fas fa-list"></i> ${order.item_count} item(s)
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="order-total">₱${parseFloat(order.total).toFixed(2)}</span>
                        <span class="order-time">${order.time_ago || 'Just now'}</span>
                    </div>
                    <div class="order-actions" onclick="event.stopPropagation();">
                        ${getActionButtons(order.sale_id, status, isOnline)}
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function getActionButtons(saleId, status, isOnline) {
        let buttons = '';
        
        if (status === 'pending') {
            buttons += `
                <button class="btn-accept" onclick="acceptOrder(${saleId})">
                    <i class="fas fa-check"></i> Accept
                </button>
            `;
        }
        
        if (status === 'preparing') {
            buttons += `
                <button class="btn-ready" onclick="markReady(${saleId})">
                    <i class="fas fa-utensils"></i> Ready
                </button>
            `;
        }
        
        if (status === 'ready') {
            if (isOnline) {
                buttons += `
                    <button class="btn-assign-rider" onclick="assignRider(${saleId})">
                        <i class="fas fa-truck"></i> Assign Rider
                    </button>
                `;
            } else {
                buttons += `
                    <button class="btn-complete-order" onclick="completeOrder(${saleId})">
                        <i class="fas fa-check-double"></i> Complete
                    </button>
                `;
            }
        }
        
        if (status !== 'completed' && status !== 'cancelled') {
            buttons += `
                <button class="btn-cancel" onclick="cancelOrder(${saleId})">
                    <i class="fas fa-times"></i>
                </button>
            `;
        }
        
        buttons += `
            <button class="btn-view" onclick="viewOrder(${saleId})">
                <i class="fas fa-eye"></i>
            </button>
        `;
        
        return buttons;
    }

    // ============= ASSIGN RIDER =============
    function assignRider(saleId) {
        const modal = new bootstrap.Modal(document.getElementById('assignRiderModal'));
        document.getElementById('assign_sale_id').value = saleId;
        
        const select = document.getElementById('rider_select');
        select.innerHTML = '<option value="">Loading riders...</option>';
        select.disabled = true;
        
        fetch('/delivery/riders', {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            select.innerHTML = '<option value="">-- Select Rider --</option>';
            if (data.success && data.riders && data.riders.length > 0) {
                data.riders.forEach(rider => {
                    const option = document.createElement('option');
                    option.value = rider.id;
                    option.textContent = rider.name + ' (' + rider.email + ')';
                    select.appendChild(option);
                });
                select.disabled = false;
            } else {
                select.innerHTML = '<option value="">No riders available</option>';
                select.disabled = true;
            }
        })
        .catch(() => {
            select.innerHTML = '<option value="">Error loading riders</option>';
            select.disabled = true;
        });
        
        modal.show();
    }

    function confirmAssignRider() {
        const saleId = document.getElementById('assign_sale_id').value;
        const riderId = document.getElementById('rider_select').value;
        
        if (!riderId) {
            alert('Please select a rider.');
            return;
        }
        
        const btn = document.querySelector('#assignRiderModal .btn-primary');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Assigning...';
        
        fetch('/delivery/assign', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                sale_id: parseInt(saleId),
                delivery_person_id: parseInt(riderId)
            })
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Assign Rider';
            
            if (data.success) {
                alert('✅ ' + data.message);
                bootstrap.Modal.getInstance(document.getElementById('assignRiderModal')).hide();
                loadQueue();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Assign Rider';
            alert('Error: ' + error);
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
        
        fetch(`/barista/orders/${saleId}`)
            .then(response => response.text())
            .then(html => {
                content.innerHTML = html;
            })
            .catch(() => {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        Error loading order details. Please try again.
                    </div>
                `;
            });
    }

    function acceptOrder(saleId) {
        if (!confirm('Accept this order?')) return;
        
        fetch(`/barista/orders/${saleId}/accept`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Order accepted!');
                loadQueue();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(() => alert('Error accepting order'));
    }

    function markReady(saleId) {
        if (!confirm('Mark this order as ready?')) return;
        
        fetch(`/barista/orders/${saleId}/ready`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Order marked ready!');
                loadQueue();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(() => alert('Error marking order ready'));
    }

    function completeOrder(saleId) {
        if (!confirm('Complete this order?')) return;
        
        const btn = document.querySelector(`[onclick*="completeOrder(${saleId})"]`);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }
        
        fetch(`/barista/orders/${saleId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Order completed!');
                loadQueue();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(() => {
            alert('Error completing order');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = 'Complete';
            }
        });
    }

    function cancelOrder(saleId) {
        if (!confirm('Cancel this order?')) return;
        
        fetch(`/barista/orders/${saleId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Order cancelled');
                loadQueue();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(() => alert('Error cancelling order'));
    }

    function refreshQueue() {
        loadQueue();
        showNotification('Refreshing...');
    }

    function showNotification(message) {
        const toast = document.getElementById('notificationToast');
        const msg = document.getElementById('notificationMessage');
        msg.textContent = message;
        toast.style.display = 'block';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 3000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadQueue();
        refreshInterval = setInterval(loadQueue, 15000);
    });
</script>
@endpush
@endsection