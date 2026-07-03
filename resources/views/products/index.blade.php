@extends('layouts.app')

@section('page-title', 'Products')

@section('content')
<style>
    .product-card {
        transition: transform 0.2s;
        border: 1px solid #e8e8e8;
        border-radius: 12px;
        overflow: hidden;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .product-card .product-image {
        width: 100%;
        height: 200px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }
    .product-card .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .product-card .product-image .no-image {
        font-size: 48px;
        color: #ccc;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
    }
    .product-card .product-image .no-image i {
        font-size: 48px;
        margin-bottom: 8px;
    }
    .product-card .product-image .no-image span {
        font-size: 12px;
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
    .checkbox-column {
        display: none;
    }
    .checkbox-column.show {
        display: table-cell;
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-box me-2"></i>Products</h2>
        <div>
            <button class="btn btn-danger me-2" id="toggleDeleteMode" onclick="toggleDeleteMode()">
                <i class="fas fa-trash-alt me-1"></i> Delete
            </button>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> Add Product
            </a>
        </div>
    </div>

    <!-- Delete Toolbar -->
    <div class="delete-toolbar" id="deleteToolbar">
        <div>
            <i class="fas fa-trash-alt me-2"></i>
            <span class="selected-count" id="selectedCount">0</span> products selected
        </div>
        <div>
            <button class="btn-cancel-delete me-2" onclick="cancelDeleteMode()" style="background: transparent; color: white; border: 1px solid white; padding: 6px 20px; border-radius: 6px;">
                <i class="fas fa-times me-1"></i> Cancel
            </button>
            <button class="btn-delete-selected" onclick="confirmDeleteSelected()" style="background: white; color: #dc3545; border: none; padding: 6px 20px; border-radius: 6px; font-weight: 600;">
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

    <!-- Search -->
    <div class="mb-3">
        <input type="text" id="searchProduct" class="form-control" placeholder="Search products...">
    </div>

    <!-- Products Grid -->
    <div class="row" id="productGrid">
        @forelse($products as $product)
            <div class="col-md-4 col-lg-3 mb-4" data-name="{{ strtolower($product->name) }}">
                <div class="product-card card h-100">
                    <div class="product-image">
                        @if($product->image && file_exists(storage_path('app/public/products/' . $product->image)))
                            <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}">
                        @else
                            <div class="no-image">
                                <i class="fas fa-image"></i>
                                <span>No Image</span>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="card-title mb-1">{{ $product->name }}</h6>
                            <div class="checkbox-column">
                                <input type="checkbox" class="product-checkbox" data-product-id="{{ $product->id }}" onchange="updateSelectedCount()">
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="badge bg-success">₱{{ number_format($product->price, 2) }}</span>
                        </div>
                        @if($product->description)
                            <p class="card-text small text-muted mt-2">{{ Str::limit($product->description, 60) }}</p>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteProduct({{ $product->id }}, '{{ $product->name }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-box fa-4x text-muted mb-3"></i>
                    <h5>No Products Found</h5>
                    <p class="text-muted">Click "Add Product" to create your first product.</p>
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i> Add Product
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-2 text-muted small">
        <i class="fas fa-info-circle me-1"></i>Total: <strong>{{ $products->count() }}</strong> products
    </div>
</div>

@push('scripts')
<script>
    let deleteModeActive = false;

    function toggleDeleteMode() {
        deleteModeActive = !deleteModeActive;
        const checkboxes = document.querySelectorAll('.checkbox-column');
        const toolbar = document.getElementById('deleteToolbar');
        const btn = document.getElementById('toggleDeleteMode');
        
        if (deleteModeActive) {
            checkboxes.forEach(el => el.style.display = 'table-cell');
            toolbar.classList.add('show');
            btn.innerHTML = '<i class="fas fa-times me-1"></i> Cancel Delete';
            document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = false);
            updateSelectedCount();
        } else {
            checkboxes.forEach(el => el.style.display = 'none');
            toolbar.classList.remove('show');
            btn.innerHTML = '<i class="fas fa-trash-alt me-1"></i> Delete';
        }
    }

    function cancelDeleteMode() {
        if (deleteModeActive) {
            toggleDeleteMode();
        }
    }

    function updateSelectedCount() {
        const selected = document.querySelectorAll('.product-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = selected;
    }

    function confirmDeleteSelected() {
        const selected = document.querySelectorAll('.product-checkbox:checked');
        if (selected.length === 0) {
            alert('Please select at least one product to delete.');
            return;
        }

        const productNames = [];
        const productIds = [];
        selected.forEach(cb => {
            const card = cb.closest('.product-card');
            const name = card.querySelector('.card-title').textContent.trim();
            productNames.push(name);
            productIds.push(cb.getAttribute('data-product-id'));
        });

        if (!confirm(`Are you sure you want to delete ${selected.length} product(s)?\n\n${productNames.join('\n')}`)) {
            return;
        }

        const btn = document.querySelector('.btn-delete-selected');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

        fetch('{{ route("products.delete-multiple") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_ids: productIds
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

    function deleteProduct(id, name) {
        if (!confirm(`Are you sure you want to delete "${name}"?`)) {
            return;
        }

        fetch(`/products/${id}`, {
            method: 'DELETE',
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

    // Search functionality
    document.getElementById('searchProduct').addEventListener('keyup', function() {
        const search = this.value.toLowerCase();
        const cards = document.querySelectorAll('#productGrid .col-md-4');
        cards.forEach(card => {
            const name = card.getAttribute('data-name') || '';
            card.style.display = name.includes(search) ? '' : 'none';
        });
    });
</script>
@endpush
@endsection