@extends('layouts.app')

@section('page-title', 'Point of Sale')

@section('content')
<style>
    .pos-container {
        background: #f8f9fa;
        min-height: calc(100vh - 200px);
    }
    .product-card {
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
        border-radius: 12px;
        padding: 10px;
        text-align: center;
        background: white;
        height: 100%;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .product-card:hover:not(.disabled) {
        transform: translateY(-4px);
        border-color: #6F4E37;
        box-shadow: 0 4px 20px rgba(111, 78, 55, 0.15);
    }
    .product-card.disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .product-card.disabled:hover {
        transform: none;
        box-shadow: none;
    }
    .product-card .product-image {
        width: 100%;
        height: 120px;
        border-radius: 8px;
        overflow: hidden;
        background: #f8f9fa;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .product-card .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .product-card .product-image .no-image {
        font-size: 32px;
        color: #ccc;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
    }
    .product-card .product-image .no-image i {
        font-size: 32px;
    }
    .product-card .product-name {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .product-card .product-price {
        font-size: 14px;
        font-weight: 700;
        color: #6F4E37;
    }
    .product-card .out-of-stock-label {
        font-size: 10px;
        color: #dc3545;
        margin-top: 4px;
        font-weight: 600;
    }
    
    .cart-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .cart-header {
        padding: 15px 20px;
        border-bottom: 1px solid #e8e8e8;
        background: #6F4E37;
        color: white;
        border-radius: 12px 12px 0 0;
    }
    .cart-header h5 {
        margin: 0;
    }
    .cart-body {
        flex: 1;
        padding: 15px 20px;
        max-height: 300px;
        overflow-y: auto;
    }
    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .cart-item:last-child {
        border-bottom: none;
    }
    .cart-item .item-name {
        font-weight: 600;
        font-size: 14px;
    }
    .cart-item .item-price {
        font-size: 13px;
        color: #6F4E37;
    }
    .cart-footer {
        padding: 15px 20px;
        border-top: 2px solid #e8e8e8;
        background: #f8f9fa;
        border-radius: 0 0 12px 12px;
    }
    .cart-total {
        font-size: 28px;
        font-weight: 700;
        color: #6F4E37;
    }
    
    .qty-btn {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 1px solid #ddd;
        background: white;
        font-weight: bold;
        font-size: 14px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .qty-btn:hover {
        background: #6F4E37;
        color: white;
        border-color: #6F4E37;
    }
    .qty-btn.negative {
        border-color: #dc3545;
        color: #dc3545;
    }
    .qty-btn.negative:hover {
        background: #dc3545;
        color: white;
    }
    .qty-btn.positive {
        border-color: #28a745;
        color: #28a745;
    }
    .qty-btn.positive:hover {
        background: #28a745;
        color: white;
    }
    .qty-display {
        min-width: 30px;
        text-align: center;
        font-weight: 700;
        font-size: 16px;
    }
    
    .branch-selector {
        background: white;
        padding: 10px 15px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        margin-bottom: 15px;
    }
    
    .quick-actions {
        display: flex;
        gap: 8px;
        margin-top: 10px;
    }
    .quick-actions .btn {
        flex: 1;
        font-size: 13px;
        padding: 8px;
        border-radius: 8px;
        font-weight: 600;
    }
    .quick-actions .btn-queue {
        background: #ffc107;
        color: #000;
        border: none;
    }
    .quick-actions .btn-queue:hover {
        background: #e0a800;
        color: #000;
    }
    .quick-actions .btn-queue i {
        margin-right: 6px;
    }

    .customer-search-container {
        position: relative;
    }
    .customer-search-container .customer-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        max-height: 150px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .customer-search-container .customer-results .customer-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s;
    }
    .customer-search-container .customer-results .customer-item:hover {
        background: #f8f9fa;
    }
    .customer-search-container .customer-results .customer-item:last-child {
        border-bottom: none;
    }
    .customer-search-container .customer-results .customer-item .customer-name {
        font-weight: 600;
    }
    .customer-search-container .customer-results .customer-item .customer-code {
        font-size: 12px;
        color: #999;
    }
    .selected-customer {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 8px;
        padding: 8px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 5px;
    }
    .selected-customer .customer-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .selected-customer .customer-info .badge-member {
        background: #6F4E37;
        color: white;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
    }

    .category-filter {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        margin-top: 8px;
    }
    .category-filter .btn-category {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid #ddd;
        background: white;
        color: #666;
        transition: all 0.2s;
        cursor: pointer;
    }
    .category-filter .btn-category:hover {
        border-color: #6F4E37;
        color: #6F4E37;
        background: #f8f5f2;
    }
    .category-filter .btn-category.active {
        background: #6F4E37;
        color: white;
        border-color: #6F4E37;
    }
    .category-filter .btn-category i {
        margin-right: 4px;
    }
    
    .customer-name-input {
        display: none;
    }
    .customer-name-input.show {
        display: block;
    }
</style>

<div class="container-fluid pos-container py-3">
    <div class="row">
        <!-- Left: Products -->
        <div class="col-lg-8">
            <!-- Branch Info & Search -->
            <div class="branch-selector">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold" style="font-size: 14px;">
                            <i class="fas fa-store me-1"></i> Branch:
                        </span>
                        <span class="badge bg-primary" style="font-size: 14px; padding: 6px 14px;">
                            {{ str_replace('☕ Brew & Bean Co. - ', '', $branches->first()->name ?? '') }}
                        </span>
                    </div>
                    <div>
                        <input type="text" id="searchProduct" class="form-control form-control-sm" placeholder="Search products..." style="width: 200px;">
                    </div>
                </div>
                
                <!-- Category Filter -->
                <div class="category-filter" id="categoryFilter">
                    <button class="btn-category active" data-category="all" onclick="filterCategory('all')">
                        <i class="fas fa-th-list"></i> All
                    </button>
                    @php
                        $categories = [];
                        foreach($products as $product) {
                            if (!empty($product->category)) {
                                $categories[] = $product->category;
                            }
                        }
                        $categories = array_unique($categories);
                    @endphp
                    @foreach($categories as $category)
                        <button class="btn-category" data-category="{{ $category }}" onclick="filterCategory('{{ $category }}')">
                            @php
                                $icon = 'fa-tag';
                                if (str_contains(strtolower($category), 'coffee')) $icon = 'fa-mug-hot';
                                elseif (str_contains(strtolower($category), 'tea')) $icon = 'fa-leaf';
                                elseif (str_contains(strtolower($category), 'pastry') || str_contains(strtolower($category), 'cake')) $icon = 'fa-cake-candles';
                                elseif (str_contains(strtolower($category), 'sandwich')) $icon = 'fa-bread-slice';
                                elseif (str_contains(strtolower($category), 'dessert')) $icon = 'fa-ice-cream';
                                elseif (str_contains(strtolower($category), 'beverage') || str_contains(strtolower($category), 'drink')) $icon = 'fa-glass-water';
                            @endphp
                            <i class="fas {{ $icon }}"></i> {{ $category }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row" id="productGrid">
                @forelse($products as $product)
                    <div class="col-md-3 col-sm-4 col-6 mb-3 product-item" 
                         data-name="{{ strtolower($product->name) }}" 
                         data-category="{{ $product->category ?? '' }}">
                        <div class="product-card {{ $product->can_make ? '' : 'disabled' }}" 
                             onclick="{{ $product->can_make ? 'addToCart(' . $product->id . ', \'' . addslashes($product->name) . '\', ' . $product->price . ')' : 'alert(\'❌ This product is out of stock. Please check inventory.\')' }}">
                            <div class="product-image">
                                @if($product->image && file_exists(storage_path('app/public/products/' . $product->image)))
                                    <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}">
                                @else
                                    <div class="no-image">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="product-name">{{ $product->name }}</div>
                            <div class="product-price">₱{{ number_format($product->price, 2) }}</div>
                            @if(!$product->can_make)
                                <div class="out-of-stock-label">
                                    <i class="fas fa-times-circle"></i> Out of stock
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-coffee fa-4x text-muted mb-3"></i>
                            <h5>No Products Available</h5>
                            <p class="text-muted">No products found.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Cart -->
        <div class="col-lg-4">
            <div class="cart-section">
                <div class="cart-header">
                    <h5><i class="fas fa-shopping-cart me-2"></i> Order</h5>
                </div>
                
                <div class="cart-body" id="cartBody">
                    <div id="cartItems">
                        <p class="text-muted text-center py-4">
                            <i class="fas fa-cart-plus fa-2x d-block mb-2"></i>
                            No items in cart
                        </p>
                    </div>
                </div>

                <div class="cart-footer">
                    <!-- Customer Type Selection -->
                    <div class="mb-2">
                        <label class="form-label fw-bold" style="font-size: 13px;">Customer Type</label>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm active" id="btnWalkin" onclick="selectCustomerType('walkin')">
                                <i class="fas fa-user"></i> Walk-in
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnMember" onclick="selectCustomerType('member')">
                                <i class="fas fa-user-check"></i> Member
                            </button>
                        </div>
                    </div>

                    <!-- Walk-in Name Input -->
                    <div id="walkinNameInput" class="customer-name-input show mb-2">
                        <label class="form-label fw-bold" style="font-size: 13px;">Customer Name</label>
                        <input type="text" id="walkinName" class="form-control form-control-sm" placeholder="Enter customer name...">
                    </div>

                    <!-- Customer Search (for members) -->
                    <div id="memberSearch" style="display: none;">
                        <div class="customer-search-container">
                            <label class="form-label fw-bold" style="font-size: 13px;">Search Member</label>
                            <input type="text" id="customerSearch" class="form-control form-control-sm" placeholder="Search by name, email, or code..." onkeyup="searchCustomer(this.value)" autocomplete="off">
                            <div class="customer-results" id="customerResults"></div>
                        </div>
                        <div id="selectedCustomer" style="display: none;">
                            <div class="selected-customer">
                                <div class="customer-info">
                                    <i class="fas fa-user-circle text-primary" style="font-size: 24px;"></i>
                                    <div>
                                        <div class="fw-bold" id="selectedCustomerName">John Doe</div>
                                        <div class="text-muted" style="font-size: 12px;">
                                            <span id="selectedCustomerCode">CUS-123</span>
                                            <span class="badge-member ms-2">Member</span>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-outline-danger" onclick="clearCustomer()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <input type="hidden" id="selectedCustomerId" value="">
                    </div>

                    <!-- Order Notes -->
                    <div class="mb-2 mt-2">
                        <input type="text" id="orderNotes" class="form-control form-control-sm" placeholder="Add notes...">
                    </div>

                    <!-- Total -->
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total:</span>
                        <span class="cart-total" id="cartTotal">₱0.00</span>
                    </div>

                    <!-- Quick Actions -->
                    <div class="quick-actions">
                        <button class="btn btn-queue" onclick="window.location.href='{{ route('barista.queue') }}'">
                            <i class="fas fa-clock"></i> Queue
                        </button>
                        <button class="btn btn-success" onclick="checkout()" style="flex: 2;">
                            <i class="fas fa-check-circle me-2"></i> Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let cart = [];
    let cartTotal = 0;
    let customerType = 'walkin';
    let selectedCustomer = null;
    let currentCategory = 'all';

    function selectCustomerType(type) {
        customerType = type;
        document.getElementById('btnWalkin').classList.toggle('active', type === 'walkin');
        document.getElementById('btnMember').classList.toggle('active', type === 'member');
        
        document.getElementById('walkinNameInput').style.display = type === 'walkin' ? 'block' : 'none';
        document.getElementById('memberSearch').style.display = type === 'member' ? 'block' : 'none';
        
        if (type === 'walkin') {
            clearCustomer();
        }
    }

    // Category Filter
    function filterCategory(category) {
        currentCategory = category;
        
        document.querySelectorAll('.btn-category').forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-category') === category) {
                btn.classList.add('active');
            }
        });
        
        document.querySelectorAll('.product-item').forEach(item => {
            const itemCategory = item.getAttribute('data-category') || '';
            if (category === 'all' || itemCategory === category) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Search with category filter
    document.getElementById('searchProduct').addEventListener('keyup', function() {
        const search = this.value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(item => {
            const name = item.getAttribute('data-name') || '';
            const category = item.getAttribute('data-category') || '';
            const showBySearch = name.includes(search);
            const showByCategory = currentCategory === 'all' || category === currentCategory;
            item.style.display = (showBySearch && showByCategory) ? '' : 'none';
        });
    });

    function searchCustomer(query) {
        const results = document.getElementById('customerResults');
        
        if (query.length < 2) {
            results.style.display = 'none';
            results.innerHTML = '';
            return;
        }

        results.innerHTML = '<div class="customer-item text-muted">Searching...</div>';
        results.style.display = 'block';

        fetch('/customers/search?q=' + encodeURIComponent(query))
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                
                if (data.length === 0) {
                    results.innerHTML = '<div class="customer-item text-muted">No customers found</div>';
                    results.style.display = 'block';
                    return;
                }

                let html = '';
                data.forEach(customer => {
                    html += `
                        <div class="customer-item" onclick="selectCustomer(${customer.id}, '${customer.name}', '${customer.customer_code}')">
                            <span class="customer-name">${customer.name}</span>
                            <span class="customer-code">${customer.customer_code}</span>
                        </div>
                    `;
                });
                results.innerHTML = html;
                results.style.display = 'block';
            })
            .catch(error => {
                console.error('Search error:', error);
                results.innerHTML = '<div class="customer-item text-danger">Error loading customers</div>';
                results.style.display = 'block';
            });
    }

    function selectCustomer(id, name, code) {
        selectedCustomer = { id, name, code };
        document.getElementById('selectedCustomerId').value = id;
        document.getElementById('selectedCustomerName').textContent = name;
        document.getElementById('selectedCustomerCode').textContent = code;
        document.getElementById('selectedCustomer').style.display = 'block';
        document.getElementById('customerResults').style.display = 'none';
        document.getElementById('customerSearch').value = name;
    }

    function clearCustomer() {
        selectedCustomer = null;
        document.getElementById('selectedCustomerId').value = '';
        document.getElementById('selectedCustomer').style.display = 'none';
        document.getElementById('customerSearch').value = '';
        document.getElementById('customerResults').style.display = 'none';
        document.getElementById('customerResults').innerHTML = '';
    }

    function addToCart(id, name, price) {
        const existing = cart.find(item => item.id === id);
        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push({ id, name, price, quantity: 1 });
        }
        updateCart();
    }

    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        updateCart();
    }

    function updateQuantity(id, change) {
        const item = cart.find(item => item.id === id);
        if (item) {
            item.quantity += change;
            if (item.quantity <= 0) {
                removeFromCart(id);
                return;
            }
            updateCart();
        }
    }

    function updateCart() {
        const cartItems = document.getElementById('cartItems');
        const cartTotalEl = document.getElementById('cartTotal');
        
        if (cart.length === 0) {
            cartItems.innerHTML = `
                <p class="text-muted text-center py-4">
                    <i class="fas fa-cart-plus fa-2x d-block mb-2"></i>
                    No items in cart
                </p>
            `;
            cartTotalEl.textContent = '₱0.00';
            return;
        }

        let html = '';
        let total = 0;

        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            html += `
                <div class="cart-item">
                    <div>
                        <div class="item-name">${item.name}</div>
                        <div class="item-price">₱${item.price.toFixed(2)} × ${item.quantity}</div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <button class="qty-btn negative" onclick="updateQuantity(${item.id}, -2)">-2</button>
                        <button class="qty-btn negative" onclick="updateQuantity(${item.id}, -1)">-1</button>
                        <span class="qty-display">${item.quantity}</span>
                        <button class="qty-btn positive" onclick="updateQuantity(${item.id}, 1)">+1</button>
                        <button class="qty-btn positive" onclick="updateQuantity(${item.id}, 2)">+2</button>
                        <button class="btn btn-sm btn-danger ms-1" onclick="removeFromCart(${item.id})" style="border-radius: 50%; width: 30px; height: 30px; padding: 0;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        cartItems.innerHTML = html;
        cartTotalEl.textContent = '₱' + total.toFixed(2);
    }

    function checkout() {
        if (cart.length === 0) {
            alert('Cart is empty!');
            return;
        }

        const branchId = '{{ $branchId ?? $branches->first()->id ?? 0 }}';
        const notes = document.getElementById('orderNotes').value;
        const customerId = document.getElementById('selectedCustomerId').value;
        const walkinName = document.getElementById('walkinName').value;

        if (customerType === 'walkin' && !walkinName.trim()) {
            alert('Please enter customer name.');
            document.getElementById('walkinName').focus();
            return;
        }

        const items = cart.map(item => ({
            product_id: item.id,
            quantity: item.quantity
        }));

        const btn = document.querySelector('.btn-success');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Processing...';

        fetch('{{ route("pos.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                branch_id: parseInt(branchId),
                customer_id: customerId || null,
                walkin_name: walkinName || null,
                items: items,
                notes: notes || null
            })
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle me-2"></i> Place Order';
            
            if (data.success) {
                const customerMsg = customerId ? ' (Member)' : ' (Walk-in: ' + walkinName + ')';
                alert('✅ ' + data.message + customerMsg);
                cart = [];
                updateCart();
                document.getElementById('orderNotes').value = '';
                document.getElementById('walkinName').value = '';
                if (customerId) {
                    clearCustomer();
                }
            } else {
                alert('❌ ' + data.error);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle me-2"></i> Place Order';
            alert('Error: ' + error);
        });
    }

    document.addEventListener('click', function(e) {
        const container = document.querySelector('.customer-search-container');
        if (container && !container.contains(e.target)) {
            document.getElementById('customerResults').style.display = 'none';
        }
    });

    // Enter key to checkout
    document.getElementById('walkinName').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            checkout();
        }
    });
</script>
@endpush
@endsection