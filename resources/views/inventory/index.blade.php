@extends('layouts.app')

@section('page-title', 'Inventory')

@section('content')
<style>
    .view-only-badge {
        background: #e9ecef;
        color: #6c757d;
        padding: 2px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .unit-badge {
        background: #6F4E37;
        color: white;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
        min-width: 40px;
        text-align: center;
    }
    .stock-number {
        font-weight: 700;
        font-size: 15px;
    }
    .stock-ok { color: #28a745; }
    .stock-warning { color: #ffc107; }
    .stock-critical { color: #dc3545; }
    
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        min-width: 100px;
        text-align: center;
    }
    .status-badge.ok {
        background: #d4edda;
        color: #155724;
    }
    .status-badge.low {
        background: #fff3cd;
        color: #856404;
    }
    .status-badge.out {
        background: #f8d7da;
        color: #721c24;
    }
    
    .table-row-ok { background-color: #ffffff; }
    .table-row-low { background-color: #fffcf0; }
    .table-row-out { background-color: #fff5f5; }
    
    .table thead th {
        background: #f8f9fa;
        font-weight: 600;
        font-size: 13px;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .table tbody td {
        vertical-align: middle;
        padding: 10px 12px;
    }

    .custom-dropdown {
        position: relative;
        display: inline-block;
        width: 100%;
    }
    .custom-dropdown select {
        appearance: none;
        -webkit-appearance: none;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 8px 35px 8px 40px;
        font-size: 14px;
        width: 100%;
        cursor: pointer;
        transition: all 0.2s;
    }
    .custom-dropdown select:hover {
        border-color: #6F4E37;
    }
    .custom-dropdown select:focus {
        border-color: #6F4E37;
        box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.1);
        outline: none;
    }
    .custom-dropdown .dropdown-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 16px;
        color: #6F4E37;
        pointer-events: none;
    }
    .custom-dropdown .dropdown-arrow {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px;
        color: #999;
        pointer-events: none;
    }

    .filter-dropdown {
        position: relative;
        display: inline-block;
        width: 100%;
    }
    .filter-dropdown select {
        appearance: none;
        -webkit-appearance: none;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 8px 35px 8px 40px;
        font-size: 14px;
        width: 100%;
        cursor: pointer;
        transition: all 0.2s;
    }
    .filter-dropdown select:hover {
        border-color: #6F4E37;
    }
    .filter-dropdown select:focus {
        border-color: #6F4E37;
        box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.1);
        outline: none;
    }
    .filter-dropdown .dropdown-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 16px;
        color: #6F4E37;
        pointer-events: none;
    }
    .filter-dropdown .dropdown-arrow {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px;
        color: #999;
        pointer-events: none;
    }

    .critical-alert {
        background: #dc3545;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }
    .critical-alert .alert-icon {
        font-size: 20px;
        margin-right: 10px;
    }
    .critical-alert .alert-text {
        font-weight: 500;
        font-size: 14px;
    }
    .critical-alert .alert-count {
        background: white;
        color: #dc3545;
        padding: 2px 14px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 16px;
    }
    .critical-alert .alert-action {
        background: rgba(255,255,255,0.2);
        color: white;
        border: none;
        padding: 4px 16px;
        border-radius: 20px;
        font-size: 13px;
        cursor: pointer;
        transition: 0.2s;
    }
    .critical-alert .alert-action:hover {
        background: rgba(255,255,255,0.3);
    }

    .branch-form {
        display: inline-block;
        width: 100%;
    }

    .btn-transfer {
        background: #ffc107;
        color: #000;
        border: none;
        padding: 2px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
        margin-left: 4px;
    }
    .btn-transfer:hover {
        background: #e0a800;
        color: #000;
    }
    .btn-transfer.quick {
        background: #17a2b8;
        color: white;
    }
    .btn-transfer.quick:hover {
        background: #138496;
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12 col-md-6">
            <h2 class="mb-0"><i class="fas fa-warehouse me-2"></i>Inventory</h2>
        </div>
        <div class="col-12 col-md-6 mt-2 mt-md-0 text-end">
            <span class="view-only-badge"><i class="fas fa-eye me-1"></i> View Only</span>
        </div>
    </div>

    <!-- Critical Stock Alert -->
    @if(isset($criticalCount) && $criticalCount > 0)
    <div class="critical-alert">
        <div>
            <span class="alert-icon"><i class="fas fa-exclamation-triangle"></i></span>
            <span class="alert-text">Critical Stock Alert</span>
        </div>
        <div>
            <span class="alert-count">{{ $criticalCount }}</span>
            <span style="font-size:13px; margin-left:5px;">item(s) below threshold</span>
            <button class="alert-action ms-2" onclick="document.getElementById('filterStatus').value='low'; document.getElementById('filterStatus').dispatchEvent(new Event('change'));">
                <i class="fas fa-eye me-1"></i> View All
            </button>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ url('/inventory') }}" class="branch-form" id="branchForm">
                <div class="row g-2 align-items-end">
                    @if(!$isStaff)
                        <div class="col-md-3">
                            <label class="form-label small"><i class="fas fa-store me-1"></i>Branch</label>
                            <div class="custom-dropdown">
                                <span class="dropdown-icon"><i class="fas fa-store"></i></span>
                                <select name="branch_id" id="branch_id" onchange="document.getElementById('branchForm').submit()">
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}" {{ $branch->id == $b->id ? 'selected' : '' }}>
                                            {{ str_replace('☕ Brew & Bean Co. - ', '', $b->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="dropdown-arrow"><i class="fas fa-chevron-down"></i></span>
                            </div>
                        </div>
                    @else
                        <div class="col-md-3">
                            <label class="form-label small"><i class="fas fa-store me-1"></i>Branch</label>
                            <p class="fw-bold mb-0">{{ str_replace('☕ Brew & Bean Co. - ', '', $branch->name) }}</p>
                        </div>
                    @endif
                    
                    <div class="col-md-3">
                        <label class="form-label small"><i class="fas fa-search me-1"></i>Search</label>
                        <input type="text" id="searchItem" class="form-control form-control-sm" placeholder="Search items...">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label small"><i class="fas fa-filter me-1"></i>Filter</label>
                        <div class="filter-dropdown">
                            <span class="dropdown-icon"><i class="fas fa-filter"></i></span>
                            <select id="filterStatus" class="form-select form-select-sm">
                                <option value="all">All Items</option>
                                <option value="ok">OK Stock</option>
                                <option value="low">Low Stock</option>
                                <option value="out">Out of Stock</option>
                            </select>
                            <span class="dropdown-arrow"><i class="fas fa-chevron-down"></i></span>
                        </div>
                    </div>
                    
                    <div class="col-md-3 text-end">
                        <span class="text-muted small">Total: <strong>{{ $items->count() }}</strong> items</span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                <table class="table table-hover mb-0" id="inventoryTable">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Item</th>
                            <th style="width: 15%;" class="d-none d-md-table-cell">Category</th>
                            <th style="width: 20%;" class="text-center">Stock</th>
                            @if(!$isStaff)
                            <th style="width: 20%;" class="text-center d-none d-md-table-cell">Warehouse</th>
                            @endif
                            <th style="width: 20%;" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        @php
                            $bi = $item->branches->first();
                            $stock = $bi ? $bi->pivot->stock_quantity : 0;
                            $threshold = $bi ? $bi->pivot->low_stock_threshold : 5;
                            $unit = $item->unit ?? 'pcs';
                            $warehouse = $item->warehouseStock;
                            $warehouseStock = $warehouse ? $warehouse->stock_quantity : 0;
                            $isLowOrOut = $stock <= $threshold;
                            $isAdmin = auth()->user()->isAdmin() || auth()->user()->isManager();
                            
                            $statusClass = 'ok';
                            $statusLabel = 'OK';
                            $rowClass = 'table-row-ok';
                            
                            if ($stock <= 0) {
                                $statusClass = 'out';
                                $statusLabel = 'Out of Stock';
                                $rowClass = 'table-row-out';
                            } elseif ($stock <= $threshold) {
                                $statusClass = 'low';
                                $statusLabel = 'Low Stock';
                                $rowClass = 'table-row-low';
                            }
                            
                            $stockClass = 'stock-ok';
                            if ($stock <= 0) {
                                $stockClass = 'stock-critical';
                            } elseif ($stock <= $threshold) {
                                $stockClass = 'stock-warning';
                            }
                        @endphp
                        <tr class="{{ $rowClass }}" data-name="{{ strtolower($item->name) }}" data-status="{{ $statusClass }}">
                            <td>
                                <div class="fw-bold">{{ $item->name }}</div>
                                @if($isLowOrOut && $isAdmin && $warehouseStock > 0)
                                    <button class="btn-transfer quick" onclick="quickTransfer({{ $item->id }}, '{{ $item->name }}', {{ $warehouseStock }}, {{ $stock }})">
                                        <i class="fas fa-truck me-1"></i> Transfer
                                    </button>
                                @endif
                                <div class="d-md-none">
                                    <span class="text-muted" style="font-size: 12px;">{{ $item->category }}</span>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">{{ $item->category }}</td>
                            <td class="text-center">
                                <span class="stock-number {{ $stockClass }}">
                                    {{ number_format($stock, 2) }}
                                </span>
                                <span class="unit-badge ms-1">{{ $unit }}</span>
                            </td>
                            @if(!$isStaff)
                            <td class="text-center d-none d-md-table-cell">
                                <span class="stock-number">
                                    {{ number_format($warehouseStock, 2) }}
                                </span>
                                <span class="unit-badge ms-1">{{ $unit }}</span>
                            </td>
                            @endif
                            <td class="text-center">
                                <span class="status-badge {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-box-open fa-3x d-block mb-2"></i>
                                No items found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-3 d-flex gap-3 flex-wrap align-items-center">
        <span class="text-muted small fw-bold">Legend:</span>
        <span class="status-badge ok">OK</span>
        <span class="status-badge low">Low Stock</span>
        <span class="status-badge out">Out of Stock</span>
        <span class="text-muted small ms-2">|</span>
        <span class="unit-badge">kg</span>
        <span class="unit-badge">liters</span>
        <span class="unit-badge">g</span>
        <span class="unit-badge">ml</span>
        <span class="unit-badge">tbsp</span>
        <span class="unit-badge">pcs</span>
        <span class="text-muted small ms-2">|</span>
        <span class="btn-transfer quick" style="font-size:11px; padding:2px 10px; cursor:default;">Transfer</span>
        <span class="text-muted small">Quick transfer from warehouse</span>
    </div>

    <div class="mt-2 text-muted small">
        <i class="fas fa-info-circle me-1"></i>
        Showing <strong>{{ $items->count() }}</strong> items
        @if($isStaff)
            <span class="ms-2 badge bg-info">{{ str_replace('☕ Brew & Bean Co. - ', '', $branch->name) }}</span>
        @endif
    </div>
</div>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-truck me-2"></i>Quick Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="transfer_item_id">
                <div class="mb-2">
                    <label class="form-label small">Item</label>
                    <p class="fw-bold" id="transfer_item_name"></p>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Current Branch Stock</label>
                    <p id="transfer_current_stock"></p>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Warehouse Available</label>
                    <p id="transfer_warehouse_available"></p>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Quantity to Transfer *</label>
                    <input type="number" id="transfer_quantity" class="form-control form-control-sm" min="1" required>
                    <small class="text-muted">Enter quantity to transfer from warehouse to branch</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="submitQuickTransfer()">
                    <i class="fas fa-paper-plane me-1"></i> Transfer
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let transferItemId = null;
    let transferItemName = '';
    let transferWarehouseStock = 0;
    let transferCurrentStock = 0;

    // Search
    document.getElementById('searchItem').addEventListener('keyup', function() {
        const search = this.value.toLowerCase();
        document.querySelectorAll('#inventoryTable tbody tr').forEach(row => {
            const name = row.getAttribute('data-name') || '';
            row.style.display = name.includes(search) ? '' : 'none';
        });
    });

    // Filter by status
    document.getElementById('filterStatus').addEventListener('change', function() {
        const filter = this.value;
        document.querySelectorAll('#inventoryTable tbody tr').forEach(row => {
            const status = row.getAttribute('data-status') || '';
            if (filter === 'all' || status === filter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    function quickTransfer(itemId, itemName, warehouseStock, currentStock) {
        transferItemId = itemId;
        transferItemName = itemName;
        transferWarehouseStock = warehouseStock;
        transferCurrentStock = currentStock;

        document.getElementById('transfer_item_id').value = itemId;
        document.getElementById('transfer_item_name').textContent = itemName;
        document.getElementById('transfer_current_stock').textContent = currentStock + ' units';
        document.getElementById('transfer_warehouse_available').textContent = warehouseStock + ' units';
        document.getElementById('transfer_quantity').value = '';
        document.getElementById('transfer_quantity').max = warehouseStock;

        new bootstrap.Modal(document.getElementById('transferModal')).show();
    }

    function submitQuickTransfer() {
        const quantity = document.getElementById('transfer_quantity').value;

        if (!quantity || quantity <= 0) {
            alert('Please enter a valid quantity.');
            return;
        }

        if (parseFloat(quantity) > transferWarehouseStock) {
            alert('Insufficient warehouse stock! Available: ' + transferWarehouseStock);
            return;
        }

        const btn = document.querySelector('#transferModal .btn-primary');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Processing...';

        fetch('{{ route("inventory.quick-transfer") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                item_id: transferItemId,
                branch_id: {{ $branch->id ?? 0 }},
                quantity: parseFloat(quantity)
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error('Server error (Status: ' + response.status + ')');
                });
            }
            return response.json();
        })
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
            console.error('Error:', error);
            alert('❌ Error: ' + error.message);
        });
    }
</script>
@endpush
@endsection