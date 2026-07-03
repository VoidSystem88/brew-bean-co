@extends('layouts.app')

@section('page-title', 'Point of Sale')

@section('content')
<style>
    :root {
        --pos-primary: #6F4E37;
        --pos-secondary: #8B6B4A;
        --pos-light: #f8f6f4;
        --pos-dark: #2d1f14;
        --pos-success: #28a745;
        --pos-danger: #dc3545;
        --pos-warning: #ffc107;
        --pos-info: #17a2b8;
    }

    .pos-container {
        display: grid;
        grid-template-columns: 1fr 420px;
        gap: 20px;
        height: calc(100vh - 110px);
        min-height: 500px;
    }
    
    .pos-products {
        background: white;
        border-radius: 16px;
        border: 1px solid #e8e8e8;
        padding: 20px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    }
    
    .pos-products .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .pos-products .header h5 {
        font-weight: 700;
        color: var(--pos-dark);
        margin: 0;
    }
    
    .pos-products .header .search-box {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    
    .pos-products .header .search-box input {
        padding: 6px 14px;
        border: 1px solid #e8e8e8;
        border-radius: 20px;
        font-size: 13px;
        width: 180px;
        transition: 0.2s;
    }
    
    .pos-products .header .search-box input:focus {
        border-color: var(--pos-primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.1);
    }
    
    .pos-products .header .search-box select {
        padding: 6px 12px;
        border: 1px solid #e8e8e8;
        border-radius: 20px;
        font-size: 13px;
        background: white;
    }
    
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 12px;
        overflow-y: auto;
        flex: 1;
        padding-right: 4px;
    }
    
    .product-grid::-webkit-scrollbar {
        width: 4px;
    }
    .product-grid::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .product-grid::-webkit-scrollbar-thumb {
        background: var(--pos-primary);
        border-radius: 10px;
    }
    
    .product-item {
        background: white;
        border: 1px solid #e8e8e8;
        border-radius: 12px;
        padding: 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }
    
    .product-item:hover {
        border-color: var(--pos-primary);
        transform: translateY(-3px);
        box-shadow: 0 4px 16px rgba(111, 78, 55, 0.12);
    }
    
    .product-item:active {
        transform: scale(0.96);
    }
    
    .product-item .product-image {
        width: 100%;
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
        background: var(--pos-light);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 6px;
    }
    
    .product-item .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .product-item .product-image .no-image {
        font-size: 28px;
        color: #ddd;
    }
    
    .product-item .product-name {
        font-size: 12px;
        font-weight: 600;
        color: var(--pos-dark);
        margin-bottom: 2px;
        line-height: 1.2;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .product-item .product-price {
        font-size: 14px;
        font-weight: 700;
        color: var(--pos-primary);
    }
    
    .product-item .stock-info {
        font-size: 10px;
        color: #999;
        margin-top: 2px;
    }
    
    .product-item .stock-info.available {
        color: var(--pos-success);
    }
    
    .product-item .stock-info.out-of-stock {
        color: var(--pos-danger);
    }
    
    .product-item .out-of-stock-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: var(--pos-danger);
        color: white;
        font-size: 9px;
        padding: 1px 8px;
        border-radius: 10px;
        font-weight: 700;
    }
    
    .product-item.out-of-stock {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .product-item.out-of-stock:hover {
        transform: none;
        box-shadow: none;
    }
    
    .pos-cart {
        background: white;
        border-radius: 16px;
        border: 1px solid #e8e8e8;
        padding: 20px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    }
    
    .pos-cart .cart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        border-bottom: 2px solid var(--pos-light);
        padding-bottom: 10px;
    }
    
    .pos-cart .cart-header h6 {
        font-weight: 700;
        color: var(--pos-dark);
        margin: 0;
    }
    
    .pos-cart .cart-header .item-count {
        background: var(--pos-primary);
        color: white;
        padding: 0 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .cart-items {
        flex: 1;
        overflow-y: auto;
        padding-right: 4px;
    }
    
    .cart-items::-webkit-scrollbar {
        width: 4px;
    }
    .cart-items::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .cart-items::-webkit-scrollbar-thumb {
        background: var(--pos-primary);
        border-radius: 10px;
    }
    
    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 10px;
        background: var(--pos-light);
        border-radius: 8px;
        margin-bottom: 6px;
        transition: 0.2s;
    }
    
    .cart-item:hover {
        background: #f0ebe6;
    }
    
    .cart-item .item-info {
        flex: 1;
    }
    
    .cart-item .item-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--pos-dark);
    }
    
    .cart-item .item-price {
        font-size: 12px;
        color: #999;
    }
    
    .cart-item .item-qty {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .cart-item .item-qty .qty-btn {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 1px solid #ddd;
        background: white;
        cursor: pointer;
        font-size: 13px;
        font-weight: 700;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .cart-item .item-qty .qty-btn:hover {
        background: var(--pos-primary);
        color: white;
        border-color: var(--pos-primary);
    }
    
    .cart-item .item-qty .qty-num {
        min-width: 20px;
        text-align: center;
        font-weight: 700;
        font-size: 14px;
    }
    
    .cart-item .item-subtotal {
        font-weight: 700;
        color: var(--pos-primary);
        font-size: 14px;
        min-width: 60px;
        text-align: right;
    }
    
    .cart-item .remove-btn {
        background: none;
        border: none;
        color: var(--pos-danger);
        cursor: pointer;
        font-size: 14px;
        padding: 0 4px;
        opacity: 0.5;
        transition: 0.2s;
    }
    
    .cart-item .remove-btn:hover {
        opacity: 1;
    }
    
    .cart-empty {
        text-align: center;
        padding: 40px 20px;
        color: #ccc;
    }
    
    .cart-empty i {
        font-size: 48px;
        display: block;
        margin-bottom: 12px;
    }
    
    .cart-empty p {
        font-size: 14px;
        margin: 0;
    }
    
    .cart-footer {
        border-top: 2px solid var(--pos-light);
        padding-top: 12px;
        margin-top: 8px;
    }
    
    .cart-footer .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    
    .cart-footer .total-row .label {
        font-size: 14px;
        color: #666;
        font-weight: 500;
    }
    
    .cart-footer .total-row .amount {
        font-size: 24px;
        font-weight: 800;
        color: var(--pos-primary);
    }
    
    .customer-section {
        background: var(--pos-light);
        border-radius: 10px;
        padding: 10px 14px;
        margin: 8px 0;
        position: relative;
    }
    
    .customer-section .customer-type {
        display: flex;
        gap: 8px;
        margin-bottom: 6px;
    }
    
    .customer-section .customer-type .btn-type {
        padding: 3px 14px;
        border-radius: 14px;
        border: 1px solid #ddd;
        background: white;
        font-size: 12px;
        cursor: pointer;
        transition: 0.2s;
        font-weight: 500;
    }
    
    .customer-section .customer-type .btn-type.active {
        background: var(--pos-primary);
        color: white;
        border-color: var(--pos-primary);
    }
    
    .customer-section .customer-type .btn-type:hover:not(.active) {
        border-color: var(--pos-primary);
    }
    
    .customer-section .customer-input {
        display: flex;
        gap: 8px;
        position: relative;
    }
    
    .customer-section .customer-input input {
        flex: 1;
        padding: 5px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
    }
    
    .customer-section .customer-input input:focus {
        border-color: var(--pos-primary);
        outline: none;
    }
    
    .customer-section .customer-points {
        font-size: 12px;
        color: var(--pos-success);
        font-weight: 600;
        margin-top: 4px;
    }
    
    .customer-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin-top: 2px;
    }
    
    .customer-suggestions.show {
        display: block;
    }
    
    .customer-suggestions .suggestion-item {
        padding: 8px 14px;
        cursor: pointer;
        transition: 0.2s;
        border-bottom: 1px solid #f5f5f5;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .customer-suggestions .suggestion-item:hover {
        background: var(--pos-light);
    }
    
    .customer-suggestions .suggestion-item:last-child {
        border-bottom: none;
    }
    
    .customer-suggestions .suggestion-item .name {
        font-weight: 500;
        font-size: 13px;
    }
    
    .customer-suggestions .suggestion-item .code {
        font-size: 11px;
        color: #999;
    }
    
    .customer-suggestions .suggestion-item .points {
        font-size: 11px;
        color: var(--pos-success);
        font-weight: 600;
    }
    
    .customer-suggestions .suggestion-empty {
        padding: 12px 14px;
        text-align: center;
        color: #999;
        font-size: 13px;
    }
    
    .customer-selected {
        background: #d4edda;
        border-radius: 6px;
        padding: 4px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 4px;
    }
    
    .customer-selected .name {
        font-weight: 600;
        font-size: 13px;
        color: #155724;
    }
    
    .customer-selected .points {
        font-size: 12px;
        color: #155724;
    }
    
    .customer-selected .remove-customer {
        background: none;
        border: none;
        color: #721c24;
        cursor: pointer;
        font-size: 14px;
    }
    
    .payment-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin: 8px 0;
    }
    
    .payment-section select,
    .payment-section input {
        padding: 6px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        width: 100%;
    }
    
    .payment-section select:focus,
    .payment-section input:focus {
        border-color: var(--pos-primary);
        outline: none;
    }
    
    .cart-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 8px;
    }
    
    .cart-actions .btn-pay {
        width: 100%;
        padding: 10px;
        background: var(--pos-success);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: 0.2s;
    }
    
    .cart-actions .btn-pay:hover {
        background: #218838;
    }
    
    .cart-actions .btn-pay:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .cart-actions .btn-queue {
        width: 100%;
        padding: 8px;
        background: #6F4E37;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .cart-actions .btn-queue:hover {
        background: #5a3d2b;
    }
    
    .cart-actions .btn-clear-row {
        display: flex;
        gap: 8px;
    }
    
    .cart-actions .btn-clear {
        flex: 1;
        padding: 8px;
        background: transparent;
        color: var(--pos-danger);
        border: 1px solid var(--pos-danger);
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    
    .cart-actions .btn-clear:hover {
        background: var(--pos-danger);
        color: white;
    }
    
    .change-display {
        text-align: center;
        font-size: 16px;
        font-weight: 700;
        padding: 6px;
        border-radius: 6px;
        margin-top: 4px;
    }
    
    .change-display.positive {
        color: var(--pos-success);
        background: #d4edda;
    }
    
    .change-display.negative {
        color: var(--pos-danger);
        background: #f8d7da;
    }
    
    @media (max-width: 992px) {
        .pos-container {
            grid-template-columns: 1fr;
            height: auto;
        }
        .pos-products {
            max-height: 400px;
        }
        .pos-cart {
            max-height: 500px;
        }
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
        }
    }
    
    @media (max-width: 576px) {
        .payment-section {
            grid-template-columns: 1fr;
        }
        .pos-products .header {
            flex-direction: column;
        }
        .pos-products .header .search-box {
            width: 100%;
        }
        .pos-products .header .search-box input {
            width: 100%;
        }
        .customer-section .customer-input {
            flex-direction: column;
        }
        .cart-actions .btn-clear-row {
            flex-direction: column;
        }
    }
</style>

<div class="container-fluid">
    <!-- Header - Single title only -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-cash-register me-2" style="color:var(--pos-primary);"></i>Point of Sale</h2>
        <div class="d-flex gap-2 align-items-center">
            <span class="text-muted" style="font-size:13px;">
                <i class="fas fa-user me-1"></i> {{ Auth::user()->name ?? 'Staff' }}
            </span>
        </div>
    </div>

    <div class="pos-container">
        <!-- Products -->
        <div class="pos-products">
            <div class="header">
                <h5><i class="fas fa-coffee me-2" style="color:var(--pos-primary);"></i>Menu</h5>
                <div class="search-box">
                    <input type="text" id="searchProduct" placeholder="Search products..." onkeyup="filterProducts()">
                    <select id="categoryFilter" onchange="filterProducts()">
                        <option value="all">All Categories</option>
                        @php
                            $categories = $products->pluck('category')->unique()->filter();
                        @endphp
                        @foreach($categories as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="product-grid" id="productGrid">
                @foreach($products as $product)
                    <div class="product-item" data-id="{{ $product->id }}" data-name="{{ strtolower($product->name) }}" data-price="{{ $product->price }}" data-category="{{ $product->category ?? '' }}" onclick="window.addToCart({{ $product->id }})">
                        <div class="product-image">
                            @if($product->image && file_exists(storage_path('app/public/products/' . $product->image)))
                                <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}">
                            @else
                                <div class="no-image"><i class="fas fa-image"></i></div>
                            @endif
                        </div>
                        <div class="product-name">{{ Str::limit($product->name, 25) }}</div>
                        <div class="product-price">₱{{ number_format($product->price, 2) }}</div>
                        <div class="stock-info" id="stock_{{ $product->id }}">Loading...</div>
                        <div class="out-of-stock-badge" id="oos_{{ $product->id }}" style="display:none;">Out of Stock</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Cart -->
        <div class="pos-cart">
            <div class="cart-header">
                <h6><i class="fas fa-shopping-cart me-2" style="color:var(--pos-primary);"></i>Current Order</h6>
                <span class="item-count" id="itemCount">0</span>
            </div>
            
            <div class="cart-items" id="cartItems">
                <div class="cart-empty">
                    <i class="fas fa-cart-plus" style="color:#ddd;"></i>
                    <p>No items in cart</p>
                    <small class="text-muted">Click on products to add</small>
                </div>
            </div>
            
            <div class="cart-footer">
                <div class="customer-section">
                    <div class="customer-type">
                        <button class="btn-type active" onclick="window.setCustomerType('walkin')">
                            <i class="fas fa-user"></i> Walk-in
                        </button>
                        <button class="btn-type" onclick="window.setCustomerType('member')">
                            <i class="fas fa-id-card"></i> Member
                        </button>
                    </div>
                    <div class="customer-input" id="customerInput">
                        <input type="text" id="customerSearch" placeholder="Search member by name, email, or code..." style="flex:1;display:none;">
                        <input type="text" id="walkinName" placeholder="Walk-in name (optional)" style="flex:1;">
                        <div class="customer-suggestions" id="customerSuggestions"></div>
                    </div>
                    <div id="selectedCustomerDisplay" style="display:none;"></div>
                    <div class="customer-points" id="customerPoints" style="display:none;">
                        <i class="fas fa-star"></i> <span id="pointsDisplay">0</span> loyalty points
                    </div>
                </div>

                <div class="total-row">
                    <span class="label">Total</span>
                    <span class="amount" id="cartTotal">₱0.00</span>
                </div>

                <div class="payment-section">
                    <select id="paymentMethod" class="form-select">
                        <option value="cash">💵 Cash</option>
                        <option value="card">💳 Card</option>
                        <option value="gcash">📱 GCash</option>
                    </select>
                    <input type="number" id="amountPaid" placeholder="Amount paid" step="0.01" oninput="window.updateChange()">
                </div>
                
                <div class="change-display" id="changeDisplay"></div>

                <div class="cart-actions">
                    <button class="btn-pay" onclick="window.processSale()" id="payBtn">
                        <i class="fas fa-credit-card me-2"></i> Pay
                    </button>
                    <a href="{{ route('barista.queue') }}" class="btn-queue">
                        <i class="fas fa-clock"></i> View Queue
                    </a>
                    <div class="btn-clear-row">
                        <button class="btn-clear" onclick="window.clearCart()">
                            <i class="fas fa-trash me-2"></i> Clear Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        // State
        let cart = [];
        let customerType = 'walkin';
        let selectedCustomer = null;
        let searchTimeout = null;
        let branchId = {{ $currentBranchId ?? 'null' }};

        // ===== CUSTOMER FUNCTIONS =====
        window.setCustomerType = function(type) {
            customerType = type;
            document.querySelectorAll('.btn-type').forEach(btn => btn.classList.remove('active'));
            document.querySelector(`.btn-type[onclick*="${type}"]`).classList.add('active');
            
            const searchInput = document.getElementById('customerSearch');
            const walkinInput = document.getElementById('walkinName');
            const pointsDiv = document.getElementById('customerPoints');
            const suggestions = document.getElementById('customerSuggestions');
            
            if (type === 'member') {
                searchInput.style.display = 'block';
                walkinInput.style.display = 'none';
                walkinInput.value = '';
                pointsDiv.style.display = 'block';
                suggestions.classList.remove('show');
                selectedCustomer = null;
                updateSelectedCustomerDisplay();
                document.getElementById('pointsDisplay').textContent = '0';
            } else {
                searchInput.style.display = 'none';
                walkinInput.style.display = 'block';
                searchInput.value = '';
                pointsDiv.style.display = 'none';
                suggestions.classList.remove('show');
                selectedCustomer = null;
                updateSelectedCustomerDisplay();
            }
        };

        function updateSelectedCustomerDisplay() {
            const display = document.getElementById('selectedCustomerDisplay');
            if (selectedCustomer) {
                display.style.display = 'block';
                display.innerHTML = `
                    <div class="customer-selected">
                        <div>
                            <span class="name">${selectedCustomer.name}</span>
                            <span style="font-size:11px;color:#999;margin-left:8px;">${selectedCustomer.customer_code}</span>
                        </div>
                        <div>
                            <span class="points">⭐ ${selectedCustomer.loyalty_points || 0} pts</span>
                            <button class="remove-customer" onclick="window.removeCustomer()">✕</button>
                        </div>
                    </div>
                `;
            } else {
                display.style.display = 'none';
            }
        }

        window.removeCustomer = function() {
            selectedCustomer = null;
            document.getElementById('customerSearch').value = '';
            document.getElementById('selectedCustomerDisplay').style.display = 'none';
            document.getElementById('pointsDisplay').textContent = '0';
            document.getElementById('customerSuggestions').classList.remove('show');
        };

        function selectCustomer(customer) {
            selectedCustomer = customer;
            document.getElementById('customerSearch').value = customer.name;
            document.getElementById('customerSuggestions').classList.remove('show');
            document.getElementById('pointsDisplay').textContent = customer.loyalty_points || 0;
            updateSelectedCustomerDisplay();
        }

        // Customer search
        document.getElementById('customerSearch').addEventListener('input', function() {
            const query = this.value.trim();
            const suggestions = document.getElementById('customerSuggestions');
            
            if (searchTimeout) clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                suggestions.classList.remove('show');
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`/pos/search-customer?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestions.innerHTML = '';
                        
                        if (data.length === 0) {
                            suggestions.innerHTML = '<div class="suggestion-empty">No customers found</div>';
                            suggestions.classList.add('show');
                            return;
                        }
                        
                        data.forEach(customer => {
                            const div = document.createElement('div');
                            div.className = 'suggestion-item';
                            div.innerHTML = `
                                <div>
                                    <div class="name">${customer.name}</div>
                                    <div class="code">${customer.customer_code}</div>
                                </div>
                                <div class="points">⭐ ${customer.loyalty_points || 0} pts</div>
                            `;
                            div.addEventListener('click', function(e) {
                                e.stopPropagation();
                                selectCustomer(customer);
                            });
                            suggestions.appendChild(div);
                        });
                        
                        suggestions.classList.add('show');
                    })
                    .catch(() => {
                        suggestions.innerHTML = '<div class="suggestion-empty">Error searching customers</div>';
                        suggestions.classList.add('show');
                    });
            }, 300);
        });

        // Hide suggestions on outside click
        document.addEventListener('click', function(e) {
            const container = document.querySelector('.customer-input');
            if (container && !container.contains(e.target)) {
                document.getElementById('customerSuggestions').classList.remove('show');
            }
        });

        // ===== STOCK FUNCTIONS =====
        function loadStocks() {
            const products = document.querySelectorAll('.product-item');
            
            products.forEach(el => {
                const id = el.dataset.id;
                const stockEl = document.getElementById('stock_' + id);
                const oosEl = document.getElementById('oos_' + id);
                
                stockEl.textContent = 'Loading...';
                stockEl.className = 'stock-info';
                
                fetch(`/pos/get-stock?product_id=${id}&branch_id=${branchId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.available && data.max_quantity > 0) {
                            stockEl.textContent = '✅ ' + data.max_quantity + ' available';
                            stockEl.className = 'stock-info available';
                            oosEl.style.display = 'none';
                            el.classList.remove('out-of-stock');
                        } else {
                            stockEl.textContent = '❌ Out of stock';
                            stockEl.className = 'stock-info out-of-stock';
                            oosEl.style.display = 'block';
                            el.classList.add('out-of-stock');
                        }
                    })
                    .catch(() => {
                        stockEl.textContent = '⚠️ Error';
                        stockEl.className = 'stock-info';
                    });
            });
        }

        // ===== PRODUCT FUNCTIONS =====
        window.filterProducts = function() {
            const search = document.getElementById('searchProduct').value.toLowerCase();
            const category = document.getElementById('categoryFilter').value;
            
            document.querySelectorAll('.product-item').forEach(el => {
                const name = el.dataset.name.toLowerCase();
                const cat = el.dataset.category || '';
                let show = true;
                
                if (search && !name.includes(search)) show = false;
                if (category !== 'all' && cat !== category) show = false;
                
                el.style.display = show ? '' : 'none';
            });
        };

        window.addToCart = function(productId) {
            const el = document.querySelector(`.product-item[data-id="${productId}"]`);
            if (el.classList.contains('out-of-stock')) {
                alert('This product is out of stock!');
                return;
            }
            
            const name = el.dataset.name;
            const price = parseFloat(el.dataset.price);
            
            const existing = cart.find(item => item.id === productId);
            if (existing) {
                existing.qty += 1;
            } else {
                cart.push({ id: productId, name, price, qty: 1 });
            }
            updateCart();
            
            el.style.transform = 'scale(0.95)';
            setTimeout(() => el.style.transform = '', 200);
        };

        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            updateCart();
        }

        window.updateQty = function(id, change) {
            const item = cart.find(i => i.id === id);
            if (item) {
                item.qty += change;
                if (item.qty <= 0) {
                    removeFromCart(id);
                    return;
                }
                updateCart();
            }
        };

        // ===== CART FUNCTIONS =====
        function updateCart() {
            const container = document.getElementById('cartItems');
            const totalEl = document.getElementById('cartTotal');
            const countEl = document.getElementById('itemCount');
            
            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="cart-empty">
                        <i class="fas fa-cart-plus" style="color:#ddd;"></i>
                        <p>No items in cart</p>
                        <small class="text-muted">Click on products to add</small>
                    </div>
                `;
                totalEl.textContent = '₱0.00';
                countEl.textContent = '0';
                return;
            }
            
            let html = '';
            let total = 0;
            let itemCount = 0;
            
            cart.forEach(item => {
                const subtotal = item.price * item.qty;
                total += subtotal;
                itemCount += item.qty;
                html += `
                    <div class="cart-item">
                        <div class="item-info">
                            <div class="item-name">${item.name}</div>
                            <div class="item-price">₱${item.price.toFixed(2)}</div>
                        </div>
                        <div class="item-qty">
                            <button class="qty-btn" onclick="window.updateQty(${item.id}, -1)">−</button>
                            <span class="qty-num">${item.qty}</span>
                            <button class="qty-btn" onclick="window.updateQty(${item.id}, 1)">+</button>
                        </div>
                        <div class="item-subtotal">₱${subtotal.toFixed(2)}</div>
                        <button class="remove-btn" onclick="removeFromCart(${item.id})">✕</button>
                    </div>
                `;
            });
            
            container.innerHTML = html;
            totalEl.textContent = '₱' + total.toFixed(2);
            countEl.textContent = itemCount;
            updateChange();
        }

        window.updateChange = function() {
            const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            const amount = parseFloat(document.getElementById('amountPaid').value) || 0;
            const change = amount - total;
            const display = document.getElementById('changeDisplay');
            
            if (amount === 0) {
                display.textContent = '';
                display.className = 'change-display';
                return;
            }
            
            if (change >= 0) {
                display.textContent = 'Change: ₱' + change.toFixed(2);
                display.className = 'change-display positive';
            } else {
                display.textContent = 'Insufficient: ₱' + Math.abs(change).toFixed(2) + ' short';
                display.className = 'change-display negative';
            }
        };

        window.clearCart = function() {
            if (cart.length === 0) return;
            if (!confirm('Clear all items from cart?')) return;
            cart = [];
            updateCart();
        };

        // ===== SALE FUNCTIONS =====
        window.processSale = function() {
            if (cart.length === 0) {
                alert('Cart is empty!');
                return;
            }
            
            const items = cart.map(item => ({
                product_id: item.id,
                quantity: item.qty,
                price: item.price
            }));
            
            const walkinName = document.getElementById('walkinName').value;
            const paymentMethod = document.getElementById('paymentMethod').value;
            const amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
            
            const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            
            if (amountPaid <= 0) {
                alert('Please enter amount paid.');
                return;
            }
            
            if (amountPaid < total) {
                alert('Insufficient payment. Amount paid must be at least ₱' + total.toFixed(2));
                return;
            }
            
            let customerId = null;
            if (customerType === 'member') {
                if (!selectedCustomer) {
                    alert('Please search and select a member customer.');
                    return;
                }
                customerId = selectedCustomer.id;
            }
            
            const btn = document.getElementById('payBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
            
            fetch('{{ route("pos.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    items: items,
                    branch_id: parseInt(branchId),
                    customer_id: customerId,
                    walkin_name: walkinName || null,
                    payment_method: paymentMethod,
                    amount_paid: amountPaid
                })
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-credit-card me-2"></i> Pay';
                
                if (data.success) {
                    const customerMsg = customerId ? ' (Member: ' + selectedCustomer.name + ')' : (walkinName ? ' (Walk-in: ' + walkinName + ')' : '');
                    alert('✅ Order placed! Total: ₱' + data.total.toFixed(2) + ' Change: ₱' + data.change.toFixed(2) + customerMsg + '\n\nOrder is now in the queue.');
                    
                    cart = [];
                    updateCart();
                    document.getElementById('walkinName').value = '';
                    document.getElementById('customerSearch').value = '';
                    document.getElementById('amountPaid').value = '';
                    document.getElementById('changeDisplay').textContent = '';
                    document.getElementById('changeDisplay').className = 'change-display';
                    document.getElementById('pointsDisplay').textContent = '0';
                    window.removeCustomer();
                    
                    loadStocks();
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-credit-card me-2"></i> Pay';
                alert('Error: ' + error);
            });
        };

        // ===== INIT =====
        document.addEventListener('DOMContentLoaded', function() {
            loadStocks();
            window.setCustomerType('walkin');
        });

        // Keyboard shortcut
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'INPUT') {
                window.processSale();
            }
        });

    })();
</script>
@endpush
@endsection