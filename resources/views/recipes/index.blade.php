@extends('layouts.app')

@section('page-title', 'Recipes')

@section('content')
<style>
    .recipe-card {
        transition: all 0.2s;
        border: 1px solid #e8e8e8;
        border-radius: 12px;
        overflow: hidden;
        height: 100%;
    }
    .recipe-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .ingredient-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;
        border-bottom: 1px solid #f5f5f5;
        font-size: 13px;
    }
    .ingredient-item:last-child {
        border-bottom: none;
    }
    .ingredient-item .qty-needed {
        font-weight: 600;
    }
    .view-only-badge {
        background: #e9ecef;
        color: #6c757d;
        padding: 2px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-utensils me-2"></i>Recipes</h2>
        <div class="d-flex gap-2 align-items-center">
            @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                <a href="{{ route('recipes.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Add Recipe
                </a>
            @endif
            <span class="view-only-badge"><i class="fas fa-eye me-1"></i> View Only</span>
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

    <div class="row" id="recipeGrid">
        @forelse($products as $product)
            @php
                $productRecipes = $recipes->where('product_id', $product->id);
                $hasRecipe = $productRecipes->count() > 0;
                $isStaff = auth()->user()->isStaff();
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="recipe-card card h-100">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-cube me-1 text-primary"></i>
                                {{ $product->name }}
                            </h5>
                            @if($hasRecipe)
                                <span class="badge bg-success">✅ Has Recipe</span>
                            @else
                                <span class="badge bg-secondary">⚠️ No Recipe</span>
                            @endif
                        </div>
                        <div class="mt-1">
                            <span class="badge bg-success">₱{{ number_format($product->price, 2) }}</span>
                            @if($product->category)
                                <span class="badge bg-secondary">{{ $product->category }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if($hasRecipe)
                            <div class="mb-2">
                                <small class="text-muted">Ingredients:</small>
                            </div>
                            @foreach($productRecipes as $recipe)
                                <div class="ingredient-item">
                                    <span>
                                        {{ $recipe->item->name ?? 'Unknown' }}
                                        <span class="qty-needed">({{ $recipe->quantity }} {{ $recipe->unit }})</span>
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center py-3">
                                <i class="fas fa-info-circle"></i>
                                <br>No recipe yet
                            </p>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-end gap-2">
                            @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                                @if($hasRecipe)
                                    <a href="{{ route('recipes.edit', $product->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="deleteRecipe({{ $product->id }}, '{{ $product->name }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @else
                                    <a href="{{ route('recipes.create') }}?product_id={{ $product->id }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Add Recipe
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No products found.
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
    function deleteRecipe(id, name) {
        if (!confirm(`Are you sure you want to delete the recipe for "${name}"?`)) {
            return;
        }

        fetch(`/recipes/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
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
</script>
@endpush
@endsection