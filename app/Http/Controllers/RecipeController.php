<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Item;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::with(['product', 'item'])->get();
        $products = Product::orderBy('name')->get();
        return view('recipes.index', compact('recipes', 'products'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        $items = Item::orderBy('name')->get();
        return view('recipes.create', compact('products', 'items'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:20',
            'batch_size' => 'nullable|integer|min:1',
            'is_batch' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $recipe = Recipe::create([
                'product_id' => $request->product_id,
                'item_id' => $request->item_id,
                'quantity' => $request->quantity,
                'unit' => $request->unit,
                'batch_size' => $request->batch_size ?? 1,
                'is_batch' => $request->is_batch ?? false,
            ]);

            return redirect()->route('recipes.index')
                ->with('success', 'Recipe added successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error adding recipe: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Recipe $recipe)
    {
        $products = Product::orderBy('name')->get();
        $items = Item::orderBy('name')->get();
        return view('recipes.edit', compact('recipe', 'products', 'items'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:20',
            'batch_size' => 'nullable|integer|min:1',
            'is_batch' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $recipe->update([
                'product_id' => $request->product_id,
                'item_id' => $request->item_id,
                'quantity' => $request->quantity,
                'unit' => $request->unit,
                'batch_size' => $request->batch_size ?? 1,
                'is_batch' => $request->is_batch ?? false,
            ]);

            return redirect()->route('recipes.index')
                ->with('success', 'Recipe updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating recipe: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Recipe $recipe)
    {
        try {
            $recipe->delete();
            return response()->json([
                'success' => true,
                'message' => 'Recipe deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting recipe: ' . $e->getMessage()
            ], 400);
        }
    }
}