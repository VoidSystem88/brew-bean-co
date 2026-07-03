@extends('layouts.app')

@section('page-title', 'Create Product with Recipe')

@section('content')
<style>
    .ingredient-row {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        border: 1px solid #e8e8e8;
        transition: 0.2s;
    }
    .ingredient-row:hover {
        border-color: #6F4E37;
        background: #f5f0eb;
    }
    .remove-ingredient {
        color: #dc3545;
        cursor: pointer;
        font-size: 20px;
        padding: 0 8px;
        line-height: 1;
    }
    .remove-ingredient:hover {
        color: #b91c1c;
    }
    .add-ingredient-btn {
        border: 2px dashed #ddd;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
        cursor: pointer;
        transition: 0.3s;
        width: 100%;
        background: white;
        font-size: 14px;
    }
    .add-ingredient-btn:hover {
        border-color: #6F4E37;
        background: #fafafa;
    }
    .section-title {
        font-weight: 600;
        color: #6F4E37;
        border-bottom: 2px solid #6F4E37;
        padding-bottom: 8px;
        margin-bottom: 16px;
    }
</style>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Create Product with Recipe</h5>
            <small class="text-muted">Create a new menu item and its ingredients in one go</small>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('products-with-recipe.store') }}" method="POST" id="productForm">
                @csrf

                <!-- Product Details -->
                <div class="section-title"><i class="fas fa-cube me-2"></i>Product Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Cappuccino" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <input type="text" name="category" class="form-control" value="{{ old('category') }}" placeholder="e.g. Coffee, Tea, Pastry" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', 0) }}" placeholder="0.00" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" value="{{ old('description') }}" placeholder="Brief description">
                    </div>
                </div>

                <!-- Recipe / Ingredients -->
                <div class="section-title mt-3"><i class="fas fa-list me-2"></i>Recipe / Ingredients</div>
                <p class="text-muted small">Add all ingredients needed to make this product</p>
                
                <div id="ingredientsContainer">
                    <!-- Template -->
                    <div class="ingredient-row" id="ingredientTemplate" style="display:none;">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-5">
                                <select name="ingredients[__INDEX__][item_id]" class="form-select form-select-sm" required>
                                    <option value="">Select Ingredient</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" step="0.01" name="ingredients[__INDEX__][quantity]" class="form-control form-control-sm" placeholder="Qty" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="ingredients[__INDEX__][unit]" class="form-control form-control-sm" placeholder="Unit (g, ml, pc)" required>
                            </div>
                            <div class="col-md-1 text-center">
                                <span class="remove-ingredient" onclick="removeIngredient(this)">✕</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="add-ingredient-btn mt-2" onclick="addIngredient()">
                    <i class="fas fa-plus-circle me-2"></i> Add Ingredient
                </button>

                <!-- Quick Add Presets -->
                <div class="mt-3">
                    <small class="text-muted">Quick preset for common drinks:</small>
                    <div class="d-flex gap-2 flex-wrap mt-1">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="loadPreset('espresso')">☕ Espresso</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="loadPreset('cappuccino')">☕ Cappuccino</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="loadPreset('latte')">☕ Latte</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="loadPreset('mocha')">☕ Mocha</button>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Create Product with Recipe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let ingredientCount = 0;
    let ingredientData = {};

    // Presets
    const presets = {
        espresso: {
            product: { name: 'Espresso', category: 'Coffee', price: 120, description: 'Strong and bold single shot' },
            ingredients: [
                { item: 'Coffee Beans', qty: 18, unit: 'g' },
                { item: 'Paper Cups (12oz)', qty: 1, unit: 'pc' },
                { item: 'Lids (12oz)', qty: 1, unit: 'pc' }
            ]
        },
        cappuccino: {
            product: { name: 'Cappuccino', category: 'Coffee', price: 160, description: 'Espresso with steamed milk and foam' },
            ingredients: [
                { item: 'Coffee Beans', qty: 18, unit: 'g' },
                { item: 'Milk', qty: 150, unit: 'ml' },
                { item: 'Sugar', qty: 10, unit: 'g' },
                { item: 'Paper Cups (12oz)', qty: 1, unit: 'pc' },
                { item: 'Lids (12oz)', qty: 1, unit: 'pc' }
            ]
        },
        latte: {
            product: { name: 'Latte', category: 'Coffee', price: 160, description: 'Smooth espresso with steamed milk' },
            ingredients: [
                { item: 'Coffee Beans', qty: 18, unit: 'g' },
                { item: 'Milk', qty: 200, unit: 'ml' },
                { item: 'Paper Cups (12oz)', qty: 1, unit: 'pc' },
                { item: 'Lids (12oz)', qty: 1, unit: 'pc' }
            ]
        },
        mocha: {
            product: { name: 'Mocha', category: 'Coffee', price: 180, description: 'Chocolate and espresso with steamed milk' },
            ingredients: [
                { item: 'Coffee Beans', qty: 18, unit: 'g' },
                { item: 'Milk', qty: 150, unit: 'ml' },
                { item: 'Chocolate Syrup', qty: 20, unit: 'ml' },
                { item: 'Paper Cups (12oz)', qty: 1, unit: 'pc' },
                { item: 'Lids (12oz)', qty: 1, unit: 'pc' }
            ]
        }
    };

    function loadPreset(type) {
        const preset = presets[type];
        if (!preset) return;

        // Fill product details
        document.querySelector('[name="name"]').value = preset.product.name;
        document.querySelector('[name="category"]').value = preset.product.category;
        document.querySelector('[name="price"]').value = preset.product.price;
        document.querySelector('[name="description"]').value = preset.product.description;

        // Clear existing ingredients
        document.querySelectorAll('.ingredient-row:not(#ingredientTemplate)').forEach(el => el.remove());

        // Add preset ingredients
        preset.ingredients.forEach(ing => {
            // Find item id by name
            const select = document.querySelector('#ingredientTemplate select');
            const options = select.querySelectorAll('option');
            let itemId = null;
            options.forEach(opt => {
                if (opt.textContent.trim().includes(ing.item)) {
                    itemId = opt.value;
                }
            });

            if (itemId) {
                addIngredientWithData(itemId, ing.qty, ing.unit);
            }
        });
    }

    function addIngredientWithData(itemId, qty, unit) {
        const container = document.getElementById('ingredientsContainer');
        const template = document.getElementById('ingredientTemplate');
        const clone = template.cloneNode(true);
        
        clone.style.display = 'block';
        clone.id = 'ingredient-' + ingredientCount;
        
        let html = clone.innerHTML;
        html = html.replace(/__INDEX__/g, ingredientCount);
        clone.innerHTML = html;
        
        // Set values
        const select = clone.querySelector('select');
        const qtyInput = clone.querySelector('input[type="number"]');
        const unitInput = clone.querySelector('input[placeholder="Unit (g, ml, pc)"]');
        
        if (itemId) select.value = itemId;
        if (qty) qtyInput.value = qty;
        if (unit) unitInput.value = unit;
        
        container.appendChild(clone);
        ingredientCount++;
    }

    function addIngredient() {
        const container = document.getElementById('ingredientsContainer');
        const template = document.getElementById('ingredientTemplate');
        const clone = template.cloneNode(true);
        
        clone.style.display = 'block';
        clone.id = 'ingredient-' + ingredientCount;
        
        let html = clone.innerHTML;
        html = html.replace(/__INDEX__/g, ingredientCount);
        clone.innerHTML = html;
        
        container.appendChild(clone);
        ingredientCount++;
    }

    function removeIngredient(element) {
        const row = element.closest('.ingredient-row');
        if (row && row.id !== 'ingredientTemplate') {
            row.remove();
        }
    }

    // Add first ingredient automatically
    document.addEventListener('DOMContentLoaded', function() {
        addIngredient();
    });

    // Validate before submit
    document.getElementById('productForm').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('.ingredient-row:not(#ingredientTemplate)');
        if (rows.length === 0) {
            e.preventDefault();
            alert('Please add at least one ingredient.');
            return false;
        }
    });
</script>
@endpush
@endsection