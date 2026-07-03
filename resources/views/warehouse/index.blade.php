@extends('layouts.app')

@section('page-title', 'Warehouse')

@section('content')
<style>
    .warehouse-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e8e8e8;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: transform 0.2s;
        margin-bottom: 16px;
    }
    .warehouse-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #6F4E37;
    }
    .stat-label {
        font-size: 13px;
        color: #999;
    }
    .low-stock {
        color: #dc3545 !important;
    }
    .critical-stock {
        color: #dc3545 !important;
        font-weight: 700;
    }
    .delete-mode-active {
        background-color: #fff3f3 !important;
        border: 2px solid #dc3545 !important;
    }
    .checkbox-column {
        display: none;
    }
    .checkbox-column.show {
        display: table-cell;
    }
    .delete-toolbar {
        display: none;
        background: #dc3545;
        color: white;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        align-items: center;
        justify-content: space-between;
    }
    .delete-toolbar.show {
        display: flex;
    }
    .delete-toolbar .selected-count {
        font-weight: bold;
        font-size: 16px;
    }
    .delete-toolbar .btn-delete-selected {
        background: white;
        color: #dc3545;
        border: none;
        padding: 6px 20px;
        border-radius: 6px;
        font-weight: 600;
    }
    .delete-toolbar .btn-delete-selected:hover {
        background: #f8f9fa;
    }
    .delete-toolbar .btn-cancel-delete {
        background: transparent;
        color: white;
        border: 1px solid white;
        padding: 6px 20px;
        border-radius: 6px;
        font-weight: 600;
    }
    .delete-toolbar .btn-cancel-delete:hover {
        background: rgba(255,255,255,0.2);
    }
    .item-checkbox {
        transform: scale(1.3);
        cursor: pointer;
    }
    .delete-mode-btn.active {
        background-color: #dc3545 !important;
        color: white !important;
    }
    .weight-per-unit-group {
        display: none;
        background: #f8f9fa;
        padding: 10px;
        border-radius: 8px;
        margin-top: 5px;
    }
    .weight-per-unit-group.show {
        display: block;
    }
    .unit-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        background: #6F4E37;
        color: white;
        cursor: pointer;
        position: relative;
    }
    .unit-badge .unit-tooltip {
        visibility: hidden;
        opacity: 0;
        width: 220px;
        background: #333;
        color: #fff;
        text-align: center;
        border-radius: 8px;
        padding: 8px 12px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        transition: opacity 0.3s;
        font-weight: normal;
        font-size: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .unit-badge .unit-tooltip::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #333 transparent transparent transparent;
    }
    .unit-badge:hover .unit-tooltip {
        visibility: visible;
        opacity: 1;
    }
    .unit-badge.container-unit {
        background: #ffc107;
        color: #000;
    }
    .status-badge {
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }
    .status-badge.pending {
        background: #fff3cd;
        color: #856404;
    }
    .status-badge.received {
        background: #d4edda;
        color: #155724;
    }
    .transfer-list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f5f5f5;
        font-size: 13px;
    }
    .transfer-list-item:last-child {
        border-bottom: none;
    }
    .modal-body {
        max-height: 500px;
        overflow-y: auto;
    }
</style>

<div class="container-fluid">
    <!-- Header with Delete Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-warehouse me-2"></i>Warehouse</h2>
        <div>
            <button class="btn btn-info me-2" onclick="openTransferHistory()">
                <i class="fas fa-history me-1"></i> History
            </button>
            <button class="btn btn-danger me-2" id="toggleDeleteMode" onclick="toggleDeleteMode()">
                <i class="fas fa-trash-alt me-1"></i> Delete
            </button>
            <button class="btn btn-primary" onclick="showAddItem()">
                <i class="fas fa-plus-circle me-2"></i> Add Item
            </button>
        </div>
    </div>

    <!-- Delete Toolbar -->
    <div class="delete-toolbar" id="deleteToolbar">
        <div>
            <i class="fas fa-trash-alt me-2"></i>
            <span class="selected-count" id="selectedCount">0</span> items selected
        </div>
        <div>
            <button class="btn-cancel-delete me-2" onclick="cancelDeleteMode()">
                <i class="fas fa-times me-1"></i> Cancel
            </button>
            <button class="btn-delete-selected" onclick="confirmDeleteSelected()">
                <i class="fas fa-trash me-1"></i> Delete Selected
            </button>
        </div>
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

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="warehouse-card text-center">
                <div class="stat-number">{{ $items->count() }}</div>
                <div class="stat-label"><i class="fas fa-boxes me-1"></i>Total Items</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="warehouse-card text-center">
                <div class="stat-number" id="lowStockCount">{{ $items->filter(function($i) { 
                    $ws = $i->warehouseStock; 
                    return $ws && $ws->stock_quantity <= $ws->low_stock_threshold; 
                })->count() }}</div>
                <div class="stat-label"><i class="fas fa-exclamation-triangle text-danger me-1"></i>Low Stock</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="warehouse-card text-center">
                <div class="stat-number">{{ $items->sum(function($i) { 
                    $ws = $i->warehouseStock; 
                    return $ws ? $ws->stock_quantity : 0; 
                }) }}</div>
                <div class="stat-label"><i class="fas fa-weight-hanging me-1"></i>Total Units</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="warehouse-card text-center">
                <div class="stat-number">{{ $suppliers->count() }}</div>
                <div class="stat-label"><i class="fas fa-truck me-1"></i>Suppliers</div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="warehouse-card">
        <div class="row g-2">
            <div class="col-md-5">
                <input type="text" id="searchItem" class="form-control form-control-sm" placeholder="Search items...">
            </div>
            <div class="col-md-3">
                <select id="filterStatus" class="form-select form-select-sm">
                    <option value="all">All Items</option>
                    <option value="low">Low Stock Only</option>
                    <option value="critical">Critical (0-5)</option>
                    <option value="healthy">Healthy Stock</option>
                </select>
            </div>
           
        </div>
    </div>

    <!-- Warehouse Stock Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0" id="warehouseTable">
                    <thead class="table-light">
                        <tr>
                            <th class="checkbox-column" id="checkboxHeader"><input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes()"></th>
                            <th><i class="fas fa-tag me-1"></i>Item</th>
                            <th><i class="fas fa-list me-1"></i>Category</th>
                            <th><i class="fas fa-ruler me-1"></i>Unit</th>
                            <th class="text-center"><i class="fas fa-warehouse me-1"></i>Stock</th>
                            <th class="text-center"><i class="fas fa-bell me-1"></i>Alert</th>
                            <th class="text-center"><i class="fas fa-circle me-1"></i>Status</th>
                            <th class="text-center"><i class="fas fa-cog me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        @php
                            $ws = $item->warehouseStock;
                            $stock = $ws ? $ws->stock_quantity : 0;
                            $threshold = $ws ? $ws->low_stock_threshold : 10;
                            $isLow = $stock <= $threshold;
                            $isCritical = $stock <= 5;
                            $isContainer = in_array($item->unit, ['bags', 'packs', 'bottles', 'cans', 'boxes', 'trays', 'sacks']);
                            $hasWeightPerUnit = $item->weight_per_unit && $item->weight_per_unit > 0;
                            
                            $tooltipText = '';
                            if ($isContainer && $hasWeightPerUnit) {
                                if ($item->unit === 'trays') {
                                    $tooltipText = '1 tray = ' . number_format($item->weight_per_unit) . ' pieces';
                                } else {
                                    $tooltipText = '1 ' . $item->unit . ' = ' . number_format($item->weight_per_unit) . 'g (' . number_format($item->weight_per_unit / 1000, 2) . 'kg)';
                                }
                            }
                        @endphp
                        <tr data-name="{{ strtolower($item->name) }}" data-stock="{{ $stock }}" data-item-id="{{ $item->id }}">
                            <td class="checkbox-column"><input type="checkbox" class="item-checkbox" data-item-id="{{ $item->id }}" onchange="updateSelectedCount()"></td>
                            <td><strong>{{ $item->name }}</strong></td>
                            <td>{{ $item->category }}</td>
                            <td>
                                @if($isContainer && $hasWeightPerUnit)
                                    <span class="unit-badge container-unit" title="Click for details">
                                        {{ $item->unit }}
                                        <span class="unit-tooltip">
                                            <i class="fas fa-info-circle me-1"></i>
                                            {{ $tooltipText }}
                                        </span>
                                    </span>
                                @else
                                    <span class="unit-badge">{{ $item->unit }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="{{ $isCritical ? 'critical-stock' : ($isLow ? 'low-stock' : '') }}">
                                    {{ number_format($stock, 2) }}
                                </span>
                            </td>
                            <td class="text-center">{{ $threshold }}</td>
                            <td class="text-center">
                                @if($isCritical)
                                    <span class="badge bg-danger">🔴 Critical</span>
                                @elseif($isLow)
                                    <span class="badge bg-warning">🟡 Low</span>
                                @else
                                    <span class="badge bg-success">🟢 Healthy</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary" onclick="transferItem({{ $item->id }}, '{{ $item->name }}', {{ $stock }})">
                                    <i class="fas fa-exchange-alt"></i> Transfer
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editStock({{ $item->id }}, '{{ $item->name }}', {{ $stock }}, {{ $threshold }})">
                                    <i class="fas fa-edit"></i> Stock
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-box-open fa-3x d-block mb-2"></i>
                                No items in warehouse. Click "Add Item" to start.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-2 text-muted small">
        <i class="fas fa-info-circle me-1"></i>Total: <strong>{{ $items->count() }}</strong> items
        <span class="ms-2 text-muted">
            <i class="fas fa-question-circle me-1"></i>
            Hover over <span class="unit-badge container-unit" style="display:inline-block; padding:0 8px; font-size:11px;">bags</span> to see weight per unit
        </span>
    </div>
</div>

<!-- Transfer History Modal -->
<div class="modal fade" id="transferHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-history me-2"></i>Transfer History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="transferHistoryBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading transfer history...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add Item to Warehouse</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('warehouse.store') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small">Item Name *</label>
                        <input type="text" name="name" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Category *</label>
                        <select name="category" class="form-select form-select-sm" required>
                            <option value="Raw Materials">Raw Materials</option>
                            <option value="Packaging">Packaging</option>
                            <option value="Cleaning Supplies">Cleaning Supplies</option>
                            <option value="Pastry">Pastry</option>
                            <option value="Dessert">Dessert</option>
                            <option value="Savory">Savory</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Unit *</label>
                        <select name="unit" id="unitSelect" class="form-select form-select-sm" required>
                            <option value="kg">kg</option>
                            <option value="g">g</option>
                            <option value="liters">liters</option>
                            <option value="ml">ml</option>
                            <option value="pieces">pieces</option>
                            <option value="packs">packs</option>
                            <option value="bottles">bottles</option>
                            <option value="bags">bags</option>
                            <option value="cans">cans</option>
                            <option value="boxes">boxes</option>
                            <option value="trays">trays</option>
                            <option value="sacks">sacks</option>
                        </select>
                    </div>
                    
                    <div class="weight-per-unit-group" id="weightPerUnitGroup">
                        <label class="form-label small">Weight/Volume per Unit</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" name="weight_per_unit" id="weightPerUnit" class="form-control form-control-sm" placeholder="e.g., 30" step="0.01" min="0">
                            </div>
                            <div class="col-6">
                                <select name="weight_unit" id="weightUnit" class="form-select form-select-sm">
                                    <option value="g">grams (g)</option>
                                    <option value="kg">kilograms (kg)</option>
                                    <option value="ml">milliliters (ml)</option>
                                    <option value="liters">liters</option>
                                    <option value="pieces">pieces</option>
                                </select>
                            </div>
                        </div>
                        <small class="text-muted" style="font-size:10px;">
                            e.g., 1 bag = 1000g, 1 sack = 50000g (50kg), 1 tray = 30 pieces
                        </small>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small">Supplier</label>
                        <select name="supplier_id" class="form-select form-select-sm">
                            <option value="">No supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Min Stock Alert *</label>
                        <input type="number" name="min_stock_alert" class="form-control form-control-sm" value="5" min="0">
                        <small class="text-muted" style="font-size:10px;">Alert when stock drops to or below this number</small>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Initial Stock *</label>
                        <input type="number" name="initial_stock" class="form-control form-control-sm" value="50" min="0">
                        <small class="text-muted" style="font-size:10px;">Starting stock in warehouse</small>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Add to Warehouse</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exchange-alt me-2"></i>Transfer to Branch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="transferForm">
                    @csrf
                    <input type="hidden" id="transfer_item_id" name="item_id">
                    <div class="mb-2">
                        <label class="form-label small">Item</label>
                        <p class="fw-bold mb-0" id="transfer_item_name"></p>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Warehouse Stock</label>
                        <p class="mb-0" id="transfer_warehouse_stock"></p>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Branch *</label>
                        <select name="to_branch_id" id="transfer_branch" class="form-select form-select-sm" required>
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Quantity *</label>
                        <input type="number" name="quantity" id="transfer_quantity" class="form-control form-control-sm" min="1" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Notes</label>
                        <input type="text" name="notes" id="transfer_notes" class="form-control form-control-sm" placeholder="Optional notes...">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-sm" onclick="submitTransfer()">
                    <i class="fas fa-paper-plane me-1"></i> Transfer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Stock Modal -->
<div class="modal fade" id="editStockModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Update Warehouse Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_stock_item_id">
                <div class="mb-2">
                    <label class="form-label small">Item</label>
                    <p class="fw-bold mb-0" id="edit_stock_item_name"></p>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Current Stock</label>
                    <p class="mb-0" id="edit_stock_current"></p>
                </div>
                <div class="mb-2">
                    <label class="form-label small">New Stock Quantity *</label>
                    <input type="number" id="edit_stock_quantity" class="form-control form-control-sm" min="0" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Alert Threshold</label>
                    <input type="number" id="edit_stock_threshold" class="form-control form-control-sm" min="0">
                    <small class="text-muted" style="font-size:10px;">Alert when stock drops to or below this number</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="submitEditStock()">
                    <i class="fas fa-save me-1"></i> Update Stock
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentItemId = null;
    let currentStock = 0;
    let editItemId = null;
    let editCurrentStock = 0;
    let editThreshold = 0;
    let deleteModeActive = false;

    // Show/hide weight per unit based on unit selection
    document.getElementById('unitSelect').addEventListener('change', function() {
        const unit = this.value;
        const weightGroup = document.getElementById('weightPerUnitGroup');
        const containerUnits = ['packs', 'bottles', 'bags', 'cans', 'boxes', 'trays', 'sacks'];
        
        if (containerUnits.includes(unit)) {
            weightGroup.classList.add('show');
            if (unit === 'trays') {
                document.getElementById('weightUnit').value = 'pieces';
            } else {
                document.getElementById('weightUnit').value = 'g';
            }
        } else {
            weightGroup.classList.remove('show');
            document.getElementById('weightPerUnit').value = '';
            document.getElementById('weightUnit').value = 'g';
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const unitSelect = document.getElementById('unitSelect');
        if (unitSelect && unitSelect.value) {
            const containerUnits = ['packs', 'bottles', 'bags', 'cans', 'boxes', 'trays', 'sacks'];
            if (containerUnits.includes(unitSelect.value)) {
                document.getElementById('weightPerUnitGroup').classList.add('show');
            }
        }
    });

    function openTransferHistory() {
        const modal = new bootstrap.Modal(document.getElementById('transferHistoryModal'));
        const body = document.getElementById('transferHistoryBody');
        
        // Show loading
        body.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading transfer history...</p>
            </div>
        `;
        
        modal.show();
        
        // Fetch transfer history
        fetch('{{ route("warehouse.transfers") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Extract the transfer list from the response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const transfers = doc.querySelector('.container-fluid') || doc.querySelector('body');
            
            if (transfers) {
                body.innerHTML = transfers.innerHTML;
            } else {
                body.innerHTML = '<div class="text-center py-4"><p class="text-muted">No transfers found.</p></div>';
            }
        })
        .catch(() => {
            body.innerHTML = `
                <div class="text-center py-4">
                    <p class="text-danger">Error loading transfer history.</p>
                    <button class="btn btn-sm btn-primary" onclick="openTransferHistory()">Try Again</button>
                </div>
            `;
        });
    }

    function toggleDeleteMode() {
        deleteModeActive = !deleteModeActive;
        const checkboxes = document.querySelectorAll('.checkbox-column');
        const toolbar = document.getElementById('deleteToolbar');
        const btn = document.getElementById('toggleDeleteMode');
        
        if (deleteModeActive) {
            checkboxes.forEach(el => el.classList.add('show'));
            toolbar.classList.add('show');
            btn.classList.add('active');
            btn.innerHTML = '<i class="fas fa-times me-1"></i> Cancel Delete';
            document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
            updateSelectedCount();
        } else {
            checkboxes.forEach(el => el.classList.remove('show'));
            toolbar.classList.remove('show');
            btn.classList.remove('active');
            btn.innerHTML = '<i class="fas fa-trash-alt me-1"></i> Delete';
        }
    }

    function cancelDeleteMode() {
        if (deleteModeActive) {
            toggleDeleteMode();
        }
    }

    function toggleAllCheckboxes() {
        const selectAll = document.getElementById('selectAll');
        document.querySelectorAll('.item-checkbox').forEach(cb => {
            cb.checked = selectAll.checked;
        });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const selected = document.querySelectorAll('.item-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = selected;
        document.getElementById('selectAll').checked = 
            document.querySelectorAll('.item-checkbox').length === selected && selected > 0;
    }

    function confirmDeleteSelected() {
        const selected = document.querySelectorAll('.item-checkbox:checked');
        if (selected.length === 0) {
            alert('Please select at least one item to delete.');
            return;
        }

        const itemNames = [];
        const itemIds = [];
        selected.forEach(cb => {
            const row = cb.closest('tr');
            const name = row.querySelector('td:nth-child(2)').textContent.trim();
            itemNames.push(name);
            itemIds.push(cb.getAttribute('data-item-id'));
        });

        if (!confirm(`Are you sure you want to delete ${selected.length} item(s)?\n\n${itemNames.join('\n')}`)) {
            return;
        }

        const btn = document.querySelector('.btn-delete-selected');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

        fetch('{{ route("warehouse.delete-multiple") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                item_ids: itemIds
            })
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-trash me-1"></i> Delete Selected';
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-trash me-1"></i> Delete Selected';
            alert('Error: ' + error);
        });
    }

    function showAddItem() {
        if (deleteModeActive) {
            toggleDeleteMode();
        }
        new bootstrap.Modal(document.getElementById('addItemModal')).show();
    }

    document.getElementById('searchItem').addEventListener('keyup', function() {
        const search = this.value.toLowerCase();
        const rows = document.querySelectorAll('#warehouseTable tbody tr');
        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            row.style.display = name.includes(search) ? '' : 'none';
        });
    });

    document.getElementById('filterStatus').addEventListener('change', function() {
        const filter = this.value;
        const rows = document.querySelectorAll('#warehouseTable tbody tr');
        rows.forEach(row => {
            const stock = parseInt(row.getAttribute('data-stock')) || 0;
            let show = true;
            if (filter === 'low') show = stock <= 10;
            else if (filter === 'critical') show = stock <= 5;
            else if (filter === 'healthy') show = stock > 10;
            row.style.display = show ? '' : 'none';
        });
    });

    function transferItem(itemId, itemName, stock) {
        if (deleteModeActive) {
            toggleDeleteMode();
        }
        currentItemId = itemId;
        currentStock = stock;
        document.getElementById('transfer_item_id').value = itemId;
        document.getElementById('transfer_item_name').textContent = itemName;
        document.getElementById('transfer_warehouse_stock').textContent = stock + ' units';
        
        document.getElementById('transfer_quantity').value = '';
        document.getElementById('transfer_notes').value = '';
        document.getElementById('transfer_branch').value = '';
        
        new bootstrap.Modal(document.getElementById('transferModal')).show();
    }

    function submitTransfer() {
        const itemId = document.getElementById('transfer_item_id').value;
        const branchId = document.getElementById('transfer_branch').value;
        const quantity = document.getElementById('transfer_quantity').value;
        const notes = document.getElementById('transfer_notes').value;

        if (!branchId) {
            alert('Please select a branch.');
            return;
        }
        if (!quantity || quantity <= 0) {
            alert('Please enter a valid quantity.');
            return;
        }
        if (parseInt(quantity) > currentStock) {
            alert('Insufficient warehouse stock! Available: ' + currentStock);
            return;
        }

        const btn = document.querySelector('#transferModal .btn-success');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

        fetch('{{ route("warehouse.transfer.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                item_id: parseInt(itemId),
                to_branch_id: parseInt(branchId),
                quantity: parseFloat(quantity),
                notes: notes || ''
            })
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Transfer';
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Transfer';
            alert('Error: ' + error);
        });
    }

    function editStock(itemId, itemName, stock, threshold) {
        if (deleteModeActive) {
            toggleDeleteMode();
        }
        editItemId = itemId;
        editCurrentStock = stock;
        editThreshold = threshold;
        
        document.getElementById('edit_stock_item_id').value = itemId;
        document.getElementById('edit_stock_item_name').textContent = itemName;
        document.getElementById('edit_stock_current').textContent = stock + ' units';
        document.getElementById('edit_stock_quantity').value = stock;
        document.getElementById('edit_stock_threshold').value = threshold;
        
        new bootstrap.Modal(document.getElementById('editStockModal')).show();
    }

    function submitEditStock() {
        const quantity = document.getElementById('edit_stock_quantity').value;
        const threshold = document.getElementById('edit_stock_threshold').value;
        const itemId = document.getElementById('edit_stock_item_id').value;
        
        if (quantity === '' || quantity < 0) {
            alert('Please enter a valid quantity.');
            return;
        }

        const btn = document.querySelector('#editStockModal .btn-primary');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

        fetch('{{ route("warehouse.update-stock") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                item_id: parseInt(itemId),
                stock_quantity: parseFloat(quantity)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (threshold !== '' && parseFloat(threshold) !== editThreshold) {
                    return fetch('{{ route("warehouse.update-threshold") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            item_id: parseInt(itemId),
                            threshold: parseFloat(threshold)
                        })
                    });
                }
                return data;
            } else {
                throw new Error(data.message);
            }
        })
        .then(response => {
            if (response && !response.success) {
                throw new Error(response.message);
            }
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Update Stock';
            alert('✅ Stock updated successfully!');
            location.reload();
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Update Stock';
            alert('❌ Error: ' + error.message);
        });
    }
</script>
@endpush
@endsection