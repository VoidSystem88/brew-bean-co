@extends('layouts.customer')

@section('page-title', 'Menu')

@section('content')
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 18px 20px;
        border: 1px solid #eee;
        text-align: center;
        transition: 0.2s;
    }
    .stat-card:hover { border-color: #6F4E37; }
    .stat-card .stat-number { font-size: 24px; font-weight: 600; color: #6F4E37; }
    .stat-card .stat-label { color: #999; font-size: 13px; margin-top: 2px; }
    .greeting { font-size: 22px; font-weight: 600; color: #333; }
    .greeting small { font-size: 14px; font-weight: 400; color: #999; display: block; margin-top: 2px; }
    
    .slideshow-container {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        background: white;
        border: 1px solid #eee;
        height: 260px;
    }
    .slideshow-container .slide {
        display: none;
        height: 100%;
        padding: 25px 35px;
        align-items: center;
        justify-content: space-between;
        background: white;
    }
    .slideshow-container .slide.active { display: flex; }
    .slideshow-container .slide .info { flex: 1; }
    .slideshow-container .slide .info .category { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #999; margin-bottom: 4px; }
    .slideshow-container .slide .info .name { font-size: 24px; font-weight: 600; color: #333; }
    .slideshow-container .slide .info .price { font-size: 18px; font-weight: 600; color: #6F4E37; margin-top: 4px; }
    .slideshow-container .slide .info .desc { font-size: 13px; color: #777; margin-top: 4px; max-width: 280px; }
    .slideshow-container .slide .image {
        width: 160px; height: 160px; border-radius: 50%;
        background: #f5f0eb;
        display: flex; align-items: center; justify-content: center;
        font-size: 56px; color: #6F4E37;
        flex-shrink: 0; overflow: hidden;
    }
    .slideshow-container .slide .image img { width: 100%; height: 100%; object-fit: cover; }
    .slide-dots {
        position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%);
        display: flex; gap: 6px;
    }
    .slide-dots .dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #ddd; cursor: pointer; transition: 0.3s;
        border: none; padding: 0;
    }
    .slide-dots .dot.active { background: #6F4E37; width: 24px; border-radius: 4px; }
    
    .product-card {
        cursor: pointer;
        transition: 0.2s;
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 12px 10px;
        text-align: center;
        background: white;
        height: 100%;
    }
    .product-card:hover { transform: translateY(-3px); border-color: #6F4E37; box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
    .product-card .product-image {
        width: 100%; height: 100px; border-radius: 8px;
        overflow: hidden; background: #f8f6f4;
        margin-bottom: 6px; display: flex; align-items: center; justify-content: center;
    }
    .product-card .product-image img { width: 100%; height: 100%; object-fit: cover; }
    .product-card .product-image .no-image { font-size: 32px; color: #ddd; }
    .product-card .product-price { font-size: 15px; font-weight: 600; color: #6F4E37; }
    .product-card .product-name { font-size: 13px; font-weight: 500; margin-bottom: 2px; }
    .product-card .product-desc { font-size: 11px; color: #999; margin-top: 2px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    
    .menu-category {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin: 20px 0 10px;
        padding-bottom: 6px;
        border-bottom: 2px solid #6F4E37;
        display: inline-block;
    }

    .qr-box {
        background: white;
        padding: 16px;
        border-radius: 10px;
        text-align: center;
        border: 1px solid #eee;
    }
    .qr-box canvas { max-width: 100%; height: auto; }
    
    .order-status {
        padding: 3px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    .order-status.pending { background: #fff3cd; color: #856404; }
    .order-status.completed { background: #d4edda; color: #155724; }

    .purchase-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f5f5f5;
        font-size: 13px;
    }
    .purchase-item:last-child { border-bottom: none; }
    .purchase-item .purchase-date { color: #999; font-size: 12px; }
    .purchase-item .purchase-total { font-weight: 600; color: #6F4E37; }

    .product-modal {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.7);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .product-modal.show { display: flex; }
    .product-modal .modal-content {
        background: white;
        border-radius: 16px;
        max-width: 500px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        padding: 30px;
        position: relative;
        animation: modalIn 0.3s ease;
    }
    @keyframes modalIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .product-modal .modal-close {
        position: absolute;
        top: 12px;
        right: 16px;
        background: none;
        border: none;
        font-size: 28px;
        color: #999;
        cursor: pointer;
        transition: 0.2s;
    }
    .product-modal .modal-close:hover { color: #333; }
    .product-modal .modal-image {
        width: 100%;
        height: 250px;
        border-radius: 12px;
        overflow: hidden;
        background: #f8f6f4;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
    }
    .product-modal .modal-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .product-modal .modal-image .no-image { font-size: 64px; color: #ddd; }
    .product-modal .modal-name { font-size: 24px; font-weight: 600; color: #333; }
    .product-modal .modal-price { font-size: 20px; font-weight: 600; color: #6F4E37; margin: 4px 0 10px; }
    .product-modal .modal-category { font-size: 13px; color: #999; }
    .product-modal .modal-desc { font-size: 15px; color: #555; line-height: 1.6; margin-top: 10px; }

    .qr-section {
        display: flex;
        justify-content: center;
        gap: 30px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .qr-section .qr-box {
        flex: 0 0 auto;
        min-width: 150px;
    }

    @media (max-width: 576px) {
        .slideshow-container .slide {
            flex-direction: column; text-align: center; padding: 20px;
            height: auto; min-height: 280px;
        }
        .slideshow-container .slide .image { width: 120px; height: 120px; font-size: 40px; margin-top: 10px; }
        .slideshow-container .slide .info .name { font-size: 20px; }
        .slideshow-container { height: auto; min-height: 300px; }
        .col-4 { flex: 0 0 50%; max-width: 50%; }
        .product-modal .modal-content { padding: 20px; margin: 10px; }
        .product-modal .modal-image { height: 180px; }
        .product-modal .modal-name { font-size: 20px; }
        .qr-section { gap: 15px; }
        .qr-section .qr-box { min-width: 120px; }
    }
</style>

<!-- Stats -->
<div class="row g-2 mb-3">
    <div class="col-4 col-md-3">
        <div class="stat-card">
            <div class="stat-number">{{ $totalOrders }}</div>
            <div class="stat-label">Total Orders</div>
        </div>
    </div>
    <div class="col-4 col-md-3">
        <div class="stat-card">
            <div class="stat-number">₱{{ number_format($totalSpent, 0) }}</div>
            <div class="stat-label">Total Spent</div>
        </div>
    </div>
    <div class="col-4 col-md-3">
        <div class="stat-card">
            <div class="stat-number">{{ $customer->loyalty_points ?? 0 }}</div>
            <div class="stat-label">Points</div>
        </div>
    </div>
</div>



<!-- Featured Slideshow -->
<div class="slideshow-container mb-4" id="slideshow">
    @foreach($products->take(5) as $index => $product)
        <div class="slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
            <div class="info">
                <div class="category">{{ $product->category ?? 'Featured' }}</div>
                <div class="name">{{ $product->name }}</div>
                <div class="price">₱{{ number_format($product->price, 2) }}</div>
                @if($product->description)
                    <div class="desc">{{ Str::limit($product->description, 60) }}</div>
                @endif
            </div>
            <div class="image">
                @if($product->image && file_exists(storage_path('app/public/products/' . $product->image)))
                    <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}">
                @else
                    <i class="fas fa-coffee"></i>
                @endif
            </div>
        </div>
    @endforeach
    <div class="slide-dots">
        @for($i = 0; $i < min(5, $products->count()); $i++)
            <button class="dot {{ $i === 0 ? 'active' : '' }}" onclick="goToSlide({{ $i }})"></button>
        @endfor
    </div>
</div>

<!-- Menu Categories -->
@php
    $categories = $products->pluck('category')->filter()->unique()->sort()->values();
@endphp

@if($categories->count() > 0)
    @foreach($categories as $category)
        <div class="menu-category">{{ $category }}</div>
        <div class="row g-2 mb-4">
            @foreach($products->where('category', $category) as $product)
                <div class="col-4 col-md-3 col-lg-2 product-item" data-name="{{ strtolower($product->name) }}" 
                     onclick="openModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ addslashes($product->description) }}', '{{ $product->category ?? '' }}', '{{ $product->image }}')">
                    <div class="product-card">
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
                        @if($product->description)
                            <div class="product-desc">{{ Str::limit($product->description, 50) }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
@else
    <div class="row g-2">
        @foreach($products as $product)
            <div class="col-4 col-md-3 col-lg-2 product-item" data-name="{{ strtolower($product->name) }}"
                 onclick="openModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ addslashes($product->description) }}', '{{ $product->category ?? '' }}', '{{ $product->image }}')">
                <div class="product-card">
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
                    @if($product->description)
                        <div class="product-desc">{{ Str::limit($product->description, 50) }}</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

<!-- Recent Purchases -->
@if($recentPurchases->count() > 0)
    <div class="mt-4">
        <h6 style="font-weight:600;color:#333;margin-bottom:10px;">
            <i class="fas fa-history me-2"></i>Recent Purchases
        </h6>
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body p-0">
                <div style="padding:10px 16px;">
                    @foreach($recentPurchases->take(5) as $sale)
                        <div class="purchase-item">
                            <div>
                                <div>#{{ $sale->id }} - {{ str_replace('☕ Brew & Bean Co. - ', '', $sale->branch->name ?? 'Unknown') }}</div>
                                <div class="purchase-date">{{ $sale->sale_date->format('M d, Y h:i A') }}</div>
                            </div>
                            <div>
                                <span class="order-status {{ $sale->delivery_status ?? 'completed' }}">
                                    {{ ucfirst($sale->delivery_status ?? 'completed') }}
                                </span>
                                <span class="purchase-total ms-2">₱{{ number_format($sale->total_amount, 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Footer Note -->
<div class="text-center text-muted mt-4 pt-3 border-top" style="font-size:13px;">
    <i class="fas fa-store me-1"></i> 
    Visit our shop to place your order. Show your QR code for faster checkout.
</div>

<!-- Product Modal -->
<div class="product-modal" id="productModal" onclick="closeModalOutside(event)">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal()">&times;</button>
        <div class="modal-image" id="modalImage">
            <i class="fas fa-image no-image"></i>
        </div>
        <div class="modal-category" id="modalCategory"></div>
        <div class="modal-name" id="modalName"></div>
        <div class="modal-price" id="modalPrice"></div>
        <div class="modal-desc" id="modalDesc"></div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    // QR Code
    document.addEventListener('DOMContentLoaded', function() {
        const qrContainer = document.getElementById('qrcode');
        if (qrContainer) {
            new QRCode(qrContainer, {
                text: '{{ route('customer.qr', $customer->id) }}',
                width: 140,
                height: 140,
                colorDark: '#6F4E37',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        }
        startSlideshow();
    });

    // Slideshow
    let slideIndex = 0;
    let slideInterval;

    function startSlideshow() {
        slideInterval = setInterval(nextSlide, 4000);
    }

    function nextSlide() {
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');
        if (slides.length === 0) return;
        slides.forEach(s => s.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        slideIndex = (slideIndex + 1) % slides.length;
        slides[slideIndex].classList.add('active');
        dots[slideIndex].classList.add('active');
    }

    function goToSlide(index) {
        clearInterval(slideInterval);
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');
        slides.forEach(s => s.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        slideIndex = index;
        slides[index].classList.add('active');
        dots[index].classList.add('active');
        startSlideshow();
    }

    // Product Modal
    function openModal(id, name, price, description, category, image) {
        const modal = document.getElementById('productModal');
        const modalImage = document.getElementById('modalImage');
        const modalName = document.getElementById('modalName');
        const modalPrice = document.getElementById('modalPrice');
        const modalDesc = document.getElementById('modalDesc');
        const modalCategory = document.getElementById('modalCategory');
        
        modalName.textContent = name;
        modalPrice.textContent = '₱' + parseFloat(price).toFixed(2);
        modalDesc.textContent = description || 'No description available.';
        modalCategory.textContent = category || '';
        
        if (image) {
            modalImage.innerHTML = '<img src="{{ asset('storage/products/') }}/' + image + '" alt="' + name + '">';
        } else {
            modalImage.innerHTML = '<i class="fas fa-image no-image"></i>';
        }
        
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('productModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    function closeModalOutside(event) {
        if (event.target === event.currentTarget) {
            closeModal();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>
@endpush
@endsection