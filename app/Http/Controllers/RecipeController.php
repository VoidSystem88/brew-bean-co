<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Item;
use App\Models\Recipe;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecipeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $recipes = Recipe::with(['product', 'item'])->get();
        $products = Product::with('recipes.item')->orderBy('name')->get();
        
        // Get branch only for staff
        $branch = null;
        if ($user->isStaff()) {
            $branch = $user->branch;
        }
        
        return view('recipes.index', compact('recipes', 'products', 'branch'));
    }

    public function create()
    {
        $items = Item::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        return view('recipes.create', compact('items', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.item_id' => 'required|exists:items,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01',
            'ingredients.*.unit' => 'required|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::find($request->product_id);

            Recipe::where('product_id', $product->id)->delete();

            foreach ($request->ingredients as $ingredient) {
                Recipe::create([
                    'product_id' => $product->id,
                    'item_id' => $ingredient['item_id'],
                    'quantity' => $ingredient['quantity'],
                    'unit' => $ingredient['unit'],
                ]);
            }

            DB::commit();

            return redirect()->route('recipes.index')
                ->with('success', '✅ Recipe for "' . $product->name . '" created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Recipe creation error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $product = Product::with('recipes.item')->findOrFail($id);
        $items = Item::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $recipe = $product->recipes->first();
        
        return view('recipes.edit', compact('product', 'items', 'products', 'recipe'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.item_id' => 'required|exists:items,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01',
            'ingredients.*.unit' => 'required|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);

            Recipe::where('product_id', $product->id)->delete();

            foreach ($request->ingredients as $ingredient) {
                Recipe::create([
                    'product_id' => $product->id,
                    'item_id' => $ingredient['item_id'],
                    'quantity' => $ingredient['quantity'],
                    'unit' => $ingredient['unit'],
                ]);
            }

            DB::commit();

            return redirect()->route('recipes.index')
                ->with('success', '✅ Recipe for "' . $product->name . '" updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Recipe update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $product = Product::with('recipes.item')->findOrFail($id);
        return view('recipes.show', compact('product'));
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $productName = $product->name;

            Recipe::where('product_id', $product->id)->delete();

            return response()->json([
                'success' => true,
                'message' => '✅ Recipe for "' . $productName . '" deleted successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Recipe deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }
}