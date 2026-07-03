<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Item;
use App\Models\Recipe;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductWithRecipeController extends Controller
{
    public function create()
    {
        $items = Item::all();
        return view('products.create-with-recipe', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.item_id' => 'required|exists:items,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01',
            'ingredients.*.unit' => 'required|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create Product
            $product = Product::create([
                'name' => $request->name,
                'category' => $request->category,
                'price' => $request->price,
                'description' => $request->description,
            ]);

            // 2. Add to all branches with initial stock
            $branches = Branch::all();
            foreach ($branches as $branch) {
                $branch->products()->attach($product->id, [
                    'stock_quantity' => 0,
                    'low_stock_threshold' => 5,
                ]);
            }

            // 3. Add Recipe (Ingredients)
            foreach ($request->ingredients as $ingredient) {
                Recipe::create([
                    'product_id' => $product->id,
                    'item_id' => $ingredient['item_id'],
                    'quantity' => $ingredient['quantity'],
                    'unit' => $ingredient['unit'],
                ]);
            }

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', '✅ Product "' . $product->name . '" created with ' . count($request->ingredients) . ' ingredients!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }
}