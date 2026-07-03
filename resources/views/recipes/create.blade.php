@extends('layouts.app')

@section('page-title', 'Create Recipe')

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
                        <i class="fas fa-utensils me-2 text-warning"></i>
                        Create New Recipe
                    </h5>
                    <small class="text-muted">Select a product and add ingredients</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('recipes.store') }}" method="POST" id="recipeForm">
                        @csrf

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
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} 
                                                @if($product->category)
                                                    ({{ $product->category }})
                                                @endif
                                                - ₱{{ number_format($product->price, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Select the product you want to create a recipe for.</small>
                                </div>

                                <div class="col-md-12 mb-2" id="productInfo" style="{{ request('product_id') ? 'display:block;' : 'display:none;' }}">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <span id="selectedProductInfo">
                                            Selected product: <strong>
                                                @php
                                                    $selectedProduct = $products->firstWhere('id', request('product_id'));
                                                @endphp
                                                {{ $selectedProduct ? $selectedProduct->name : 'None' }}
                                            </strong>
                                        </span>
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
                                    <!-- Template Row -->
                                    <div class="ingredient-row" id="ingredientTemplate" style="display: none;">
                                        <div class="row g-2">
                                            <div class="col-md-5">
                                                <select name="ingredients[0][item_id]" class="form-select form-select-sm ingredient-item">
                                                    <option value="">Select Item</option>
                                                    @foreach($items as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" 
                                                       name="ingredients[0][quantity]" 
                                                   class="form-control form-control-sm ingredient-quantity" 
                                                       placeholder="Quantity"
                                                       step="0.01"
                                                       min="0.01">
                                            </div>
                                            <div class="col-md-3">
                                                <select name="ingredients[0][unit]" class="form-select form-select-sm ingredient-unit">
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
                                    </div>
                                </div>

                                <div id="noIngredients" class="text-center text-muted py-4">
                                    <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                                    No ingredients added yet. Click "Add Ingredient" to start.
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('recipes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i> Create Recipe
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
    let ingredientCount = 0;

    // Show product info when selected
    document.getElementById('product_id').addEventListener('change', function() {
        const productInfo = document.getElementById('productInfo');
        const infoText = document.getElementById('selectedProductInfo');
        
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            productInfo.style.display = 'block';
            infoText.innerHTML = 'Selected product: <strong>' + selectedOption.text + '</strong>';
        } else {
            productInfo.style.display = 'none';
        }
    });

    function addIngredient() {
        const container = document.getElementById('ingredientsContainer');
        const template = document.getElementById('ingredientTemplate');
        const noIngredients = document.getElementById('noIngredients');
        
        noIngredients.style.display = 'none';
        
        const clone = template.cloneNode(true);
        clone.style.display = 'block';
        clone.id = 'ingredient_' + ingredientCount;
        
        const selects = clone.querySelectorAll('select');
        const inputs = clone.querySelectorAll('input');
        
        selects.forEach(select => {
            if (select.classList.contains('ingredient-item')) {
                select.name = 'ingredients[' + ingredientCount + '][item_id]';
            }
            if (select.classList.contains('ingredient-unit')) {
                select.name = 'ingredients[' + ingredientCount + '][unit]';
                select.setAttribute('data-index', ingredientCount);
            }
        });
        
        inputs.forEach(input => {
            if (input.classList.contains('ingredient-quantity')) {
                input.name = 'ingredients[' + ingredientCount + '][quantity]';
            }
        });
        
        const itemSelect = clone.querySelector('.ingredient-item');
        const unitSelect = clone.querySelector('.ingredient-unit');
        
        itemSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const itemText = selectedOption.textContent;
            
            const unitMatch = itemText.match(/\(([^)]+)\)/);
            if (unitMatch) {
                const unit = unitMatch[1].trim();
                const options = unitSelect.options;
                let found = false;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === unit) {
                        unitSelect.value = unit;
                        found = true;
                        break;
                    }
                }
                if (!found) {
                    const unitMap = {
                        'g': 'g',
                        'ml': 'ml',
                        'kg': 'kg',
                        'liters': 'liters',
                        'pieces': 'pieces',
                        'packs': 'packs',
                        'bottles': 'bottles',
                        'bags': 'bags',
                        'cans': 'cans',
                        'boxes': 'boxes'
                    };
                    if (unitMap[unit]) {
                        unitSelect.value = unitMap[unit];
                    }
                }
            }
        });
        
        container.appendChild(clone);
        ingredientCount++;
    }

    function removeIngredient(button) {
        const row = button.closest('.ingredient-row');
        const container = document.getElementById('ingredientsContainer');
        
        const rows = container.querySelectorAll('.ingredient-row:not([style*="display: none"])');
        if (rows.length <= 1) {
            const inputs = row.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.tagName === 'SELECT') {
                    input.value = '';
                } else {
                    input.value = '';
                }
            });
            document.getElementById('noIngredients').style.display = 'block';
            return;
        }
        
        row.remove();
        
        const remaining = container.querySelectorAll('.ingredient-row:not([style*="display: none"])');
        if (remaining.length === 0) {
            document.getElementById('noIngredients').style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.ingredient-item').forEach(function(select) {
            select.addEventListener('change', function() {
                const row = this.closest('.ingredient-row');
                const unitSelect = row.querySelector('.ingredient-unit');
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
        });
    });

    document.getElementById('recipeForm').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('.ingredient-row:not([style*="display: none"])');
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