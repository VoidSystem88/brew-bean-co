@extends('layouts.app')

@section('page-title', 'Search Results')

@section('content')
<style>
    .result-card {
        background: white;
        border-radius: 12px;
        padding: 16px 20px;
        border: 1px solid #e8e8e8;
        transition: 0.2s;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 15px;
        text-decoration: none;
        color: #333;
    }
    .result-card:hover {
        border-color: #6F4E37;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        transform: translateX(4px);
    }
    .result-card .result-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .result-card .result-info {
        flex: 1;
        min-width: 0;
    }
    .result-card .result-info .result-title {
        font-weight: 600;
        font-size: 15px;
        color: #333;
    }
    .result-card .result-info .result-sub {
        font-size: 13px;
        color: #999;
    }
    .result-card .result-badge {
        font-size: 11px;
        padding: 2px 12px;
        border-radius: 12px;
        background: #e9ecef;
        color: #666;
        font-weight: 500;
        text-transform: capitalize;
    }
    .result-card .result-arrow {
        color: #ccc;
        transition: 0.2s;
    }
    .result-card:hover .result-arrow {
        color: #6F4E37;
    }
    .search-header {
        margin-bottom: 20px;
    }
    .search-header h2 {
        font-size: 22px;
        font-weight: 700;
        color: #333;
    }
    .search-header p {
        color: #999;
        font-size: 14px;
        margin-top: 4px;
    }
    .search-stats {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .search-stats .stat-item {
        background: white;
        padding: 6px 16px;
        border-radius: 8px;
        border: 1px solid #e8e8e8;
        font-size: 13px;
    }
    .search-stats .stat-item .stat-count {
        font-weight: 700;
        color: #6F4E37;
    }
    .no-results {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        border: 1px solid #e8e8e8;
    }
    .no-results i {
        font-size: 48px;
        color: #ccc;
        margin-bottom: 16px;
        display: block;
    }
    .no-results h5 {
        color: #333;
        margin-bottom: 8px;
    }
    .no-results p {
        color: #999;
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="search-header">
        <h2><i class="fas fa-search me-2"></i>Search Results</h2>
        <p>Showing results for "<strong>{{ $query }}</strong>"</p>
    </div>

    <!-- Stats -->
    @if(count($results) > 0)
    <div class="search-stats">
        @foreach($counts as $type => $count)
            <span class="stat-item">
                <span class="stat-count">{{ $count }}</span>
                {{ ucfirst($type) }}{{ $count > 1 ? 's' : '' }}
            </span>
        @endforeach
        <span class="stat-item text-muted">
            Total: <span class="stat-count">{{ count($results) }}</span> results
        </span>
    </div>
    @endif

    <!-- Results -->
    @if(count($results) > 0)
        @foreach($grouped as $type => $items)
            <div class="mb-3">
                <h6 class="text-muted text-uppercase" style="font-size: 12px; letter-spacing: 0.5px; font-weight: 600;">
                    {{ ucfirst($type) }}
                </h6>
                @foreach($items as $item)
                    @php
                        $iconMap = [
                            'product' => 'fa-box',
                            'customer' => 'fa-user',
                            'branch' => 'fa-store',
                            'staff' => 'fa-user-tie',
                            'item' => 'fa-boxes',
                            'sale' => 'fa-receipt'
                        ];
                        $colorMap = [
                            'product' => '#6F4E37',
                            'customer' => '#0d6efd',
                            'branch' => '#28a745',
                            'staff' => '#6c757d',
                            'item' => '#ffc107',
                            'sale' => '#17a2b8'
                        ];
                        $icon = $iconMap[$item['type']] ?? 'fa-search';
                        $color = $colorMap[$item['type']] ?? '#6F4E37';
                    @endphp
                    <a href="{{ $item['url'] }}" class="result-card">
                        <div class="result-icon" style="background: {{ $color }}20; color: {{ $color }};">
                            <i class="fas {{ $icon }}"></i>
                        </div>
                        <div class="result-info">
                            <div class="result-title">{{ $item['title'] }}</div>
                            <div class="result-sub">{{ $item['subtitle'] }}</div>
                        </div>
                        <span class="result-badge">{{ $item['type'] }}</span>
                        <span class="result-arrow"><i class="fas fa-chevron-right"></i></span>
                    </a>
                @endforeach
            </div>
        @endforeach
    @else
        <div class="no-results">
            <i class="fas fa-search"></i>
            <h5>No results found</h5>
            <p>We couldn't find any results for "<strong>{{ $query }}</strong>".</p>
            <p class="text-muted" style="font-size: 13px;">Try adjusting your search terms or browse our menu.</p>
        </div>
    @endif
</div>
@endsection