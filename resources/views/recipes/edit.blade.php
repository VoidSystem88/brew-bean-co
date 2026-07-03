@extends('layouts.app')

@section('page-title', 'Edit Recipe')

@section('content')
<style>
    .ingredient-row {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        border: 1px solid #e9ecef;
        transition: all 0.2s;
    }
    .ingredient-row:hover {
        border-color: #6F4E37;
        background: #fefefe;
    }
    .product-section {
        background: #f0f7ff;
        border: 2px dashed #6F4E37;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .product-section-title {
        font-size: 16px;
        font-weight: 700;
        color: #6F4E37;
        margin-bottom: 15px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2 text-warning"></i>
                        Edit Recipe
                    </h5>
                    <small class="text-muted">Update product and ingredients</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('recipes.update', $recipe->id ?? $product->id) }}" method="POST" id="recipeForm">
                        @csrf
                        @method('PUT')

                        <!-- Product Selection Section -->
                        <div class="product-section">
                            <div class="product-section-title">
                                <i class="fas fa-box me-2"></i> Select Product
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="product_id" class="form-label fw-bold">
                                        Product *
                                    </label>
                                    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                        <option value="">-- Select Product --</option>
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}" {{ $product->id == $p->id ? 'selected' : '' }}>
                                                {{ $p->name }} 
                                                @if($p->category)
                                                    ({{ $p->category }})
                                                @endif
                                                - ₱{{ number_format($p->price, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Select the product you want to edit the recipe for.</small>
                                </div>

                                <div class="col-md-12 mb-2" id="productInfo">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Editing recipe for: <strong>{{ $product->name }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ingredients Section -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-list me-2"></i>
                                        Ingredients
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-success" onclick="addIngredient()">
                                        <i class="fas fa-plus-circle me-1"></i> Add Ingredient
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="ingredientsContainer">
                                    @php
                                        $recipeIngredients = $product->recipes ?? collect();
                                    @endphp
                                    
                                    @if($recipeIngredients->count() > 0)
                                        @foreach($recipeIngredients as $index => $ingredient)
                                        <div class="ingredient-row" id="ingredient_{{ $index }}">
                                            <div class="row g-2">
                                                <div class="col-md-5">
                                                    <select name="ingredients[{{ $index }}][item_id]" class="form-select form-select-sm ingredient-item">
                                                        <option value="">Select Item</option>
                                                        @foreach($items as $item)
                                                            <option value="{{ $item->id }}" {{ $ingredient->item_id == $item->id ? 'selected' : '' }}>
                                                                {{ $item->name }} ({{ $item->unit }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" 
                                                           name="ingredients[{{ $index }}][quantity]" 
                                                           class="form-control form-control-sm ingredient-quantity" 
                                                           placeholder="Quantity"
                                                           step="0.01"
                                                           min="0.01"
                                                           value="{{ $ingredient->quantity }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <select name="ingredients[{{ $index }}][unit]" class="form-select form-select-sm ingredient-unit">
                                                        <option value="">Select Unit</option>
                                                        <option value="g" {{ $ingredient->unit == 'g' ? 'selected' : '' }}>g (grams)</option>
                                                        <option value="ml" {{ $ingredient->unit == 'ml' ? 'selected' : '' }}>ml (milliliters)</option>
                                                        <option value="kg" {{ $ingredient->unit == 'kg' ? 'selected' : '' }}>kg (kilograms)</option>
                                                        <option value="liters" {{ $ingredient->unit == 'liters' ? 'selected' : '' }}>liters</option>
                                                        <option value="pieces" {{ $ingredient->unit == 'pieces' ? 'selected' : '' }}>pieces</option>
                                                        <option value="packs" {{ $ingredient->unit == 'packs' ? 'selected' : '' }}>packs</option>
                                                        <option value="bottles" {{ $ingredient->unit == 'bottles' ? 'selected' : '' }}>bottles</option>
                                                        <option value="bags" {{ $ingredient->unit == 'bags' ? 'selected' : '' }}>bags</option>
                                                        <option value="cans" {{ $ingredient->unit == 'cans' ? 'selected' : '' }}>cans</option>
                                                        <option value="boxes" {{ $ingredient->unit == 'boxes' ? 'selected' : '' }}>boxes</option>
                                                        <option value="trays" {{ $ingredient->unit == 'trays' ? 'selected' : '' }}>trays</option>
                                                        <option value="tbsp" {{ $ingredient->unit == 'tbsp' ? 'selected' : '' }}>tbsp (tablespoon)</option>
                                                        <option value="tsp" {{ $ingredient->unit == 'tsp' ? 'selected' : '' }}>tsp (teaspoon)</option>
                                                        <option value="cup" {{ $ingredient->unit == 'cup' ? 'selected' : '' }}>cup</option>
                                                        <option value="oz" {{ $ingredient->unit == 'oz' ? 'selected' : '' }}>oz (ounces)</option>
                                                        <option value="lb" {{ $ingredient->unit == 'lb' ? 'selected' : '' }}>lb (pounds)</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-1 text-center">
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeIngredient(this)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div id="noIngredients" class="text-center text-muted py-4">
                                            <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                                            No ingredients added yet. Click "Add Ingredient" to start.
                                        </div>
                                    @endif
                                </div>

                                @if($recipeIngredients->count() > 0)
                                    <div id="noIngredients" style="display: none;" class="text-center text-muted py-4">
                                        <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                                        No ingredients added yet. Click "Add Ingredient" to start.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('recipes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i> Update Recipe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let ingredientCount = {{ $product->recipes->count() }};

    function addIngredient() {
        const container = document.getElementById('ingredientsContainer');
        const noIngredients = document.getElementById('noIngredients');
        
        if (noIngredients) {
            noIngredients.style.display = 'none';
        }
        
        const row = document.createElement('div');
        row.className = 'ingredient-row';
        row.id = 'ingredient_' + ingredientCount;
        
        row.innerHTML = `
            <div class="row g-2">
                <div class="col-md-5">
                    <select name="ingredients[${ingredientCount}][item_id]" class="form-select form-select-sm ingredient-item">
                        <option value="">Select Item</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" 
                           name="ingredients[${ingredientCount}][quantity]" 
                           class="form-control form-control-sm ingredient-quantity" 
                           placeholder="Quantity"
                           step="0.01"
                           min="0.01">
                </div>
                <div class="col-md-3">
                    <select name="ingredients[${ingredientCount}][unit]" class="form-select form-select-sm ingredient-unit">
                        <option value="">Select Unit</option>
                        <option value="g">g (grams)</option>
                        <option value="ml">ml (milliliters)</option>
                        <option value="kg">kg (kilograms)</option>
                        <option value="liters">liters</option>
                        <option value="pieces">pieces</option>
                        <option value="packs">packs</option>
                        <option value="bottles">bottles</option>
                        <option value="bags">bags</option>
                        <option value="cans">cans</option>
                        <option value="boxes">boxes</option>
                        <option value="trays">trays</option>
                        <option value="tbsp">tbsp (tablespoon)</option>
                        <option value="tsp">tsp (teaspoon)</option>
                        <option value="cup">cup</option>
                        <option value="oz">oz (ounces)</option>
                        <option value="lb">lb (pounds)</option>
                    </select>
                </div>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeIngredient(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        // Add event listener for auto-unit selection
        const itemSelect = row.querySelector('.ingredient-item');
        const unitSelect = row.querySelector('.ingredient-unit');
        
        itemSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const itemText = selectedOption.textContent;
            const unitMatch = itemText.match(/\(([^)]+)\)/);
            if (unitMatch) {
                const unit = unitMatch[1].trim();
                const options = unitSelect.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === unit) {
                        unitSelect.value = unit;
                        break;
                    }
                }
            }
        });
        
        container.appendChild(row);
        ingredientCount++;
    }

    function removeIngredient(button) {
        const row = button.closest('.ingredient-row');
        const container = document.getElementById('ingredientsContainer');
        
        const rows = container.querySelectorAll('.ingredient-row');
        if (rows.length <= 1) {
            const inputs = row.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.tagName === 'SELECT') {
                    input.value = '';
                } else {
                    input.value = '';
                }
            });
            const noIngredients = document.getElementById('noIngredients');
            if (noIngredients) {
                noIngredients.style.display = 'block';
            }
            return;
        }
        
        row.remove();
        
        const remaining = container.querySelectorAll('.ingredient-row');
        if (remaining.length === 0) {
            const noIngredients = document.getElementById('noIngredients');
            if (noIngredients) {
                noIngredients.style.display = 'block';
            }
        }
    }

    // Validate form before submit
    document.getElementById('recipeForm').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('.ingredient-row');
        let hasError = false;
        
        rows.forEach(function(row) {
            const item = row.querySelector('.ingredient-item');
            const quantity = row.querySelector('.ingredient-quantity');
            const unit = row.querySelector('.ingredient-unit');
            
            if (!item.value || !quantity.value || !unit.value) {
                hasError = true;
                row.style.border = '2px solid red';
                row.style.background = '#fff5f5';
            } else {
                row.style.border = '1px solid #e9ecef';
                row.style.background = '#f8f9fa';
            }
        });
        
        if (hasError) {
            e.preventDefault();
            alert('⚠️ Please fill in all ingredient fields (Item, Quantity, and Unit).');
        }
    });
</script>
@endpush
@endsection