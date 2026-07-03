<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            $product = new Product();
            $product->name = $request->name;
            $product->category = $request->category;
            $product->description = $request->description;
            $product->price = $request->price;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                if (!Storage::disk('public')->exists('products')) {
                    Storage::disk('public')->makeDirectory('products');
                }
                
                try {
                    $manager = new ImageManager(new Driver());
                    $img = $manager->read($image->getPathname());
                    
                    $width = $img->width();
                    $height = $img->height();
                    $size = min($width, $height);
                    $img->crop($size, $size, ($width - $size) / 2, ($height - $size) / 2);
                    $img->resize(400, 400);
                    $img->save(storage_path('app/public/products/' . $filename));
                    
                    $product->image = $filename;
                } catch (\Exception $e) {
                    $image->storeAs('products', $filename, 'public');
                    $product->image = $filename;
                }
            }

            $product->save();

            return redirect()->route('products.index')
                ->with('success', '✅ Product "' . $product->name . '" created successfully!');

        } catch (\Exception $e) {
            Log::error('Product creation error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            $product->name = $request->name;
            $product->category = $request->category;
            $product->description = $request->description;
            $product->price = $request->price;

            if ($request->hasFile('image')) {
                if ($product->image && Storage::disk('public')->exists('products/' . $product->image)) {
                    Storage::disk('public')->delete('products/' . $product->image);
                }

                $image = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                if (!Storage::disk('public')->exists('products')) {
                    Storage::disk('public')->makeDirectory('products');
                }
                
                try {
                    $manager = new ImageManager(new Driver());
                    $img = $manager->read($image->getPathname());
                    
                    $width = $img->width();
                    $height = $img->height();
                    $size = min($width, $height);
                    $img->crop($size, $size, ($width - $size) / 2, ($height - $size) / 2);
                    $img->resize(400, 400);
                    $img->save(storage_path('app/public/products/' . $filename));
                    
                    $product->image = $filename;
                } catch (\Exception $e) {
                    $image->storeAs('products', $filename, 'public');
                    $product->image = $filename;
                }
            }

            $product->save();

            return redirect()->route('products.index')
                ->with('success', '✅ Product "' . $product->name . '" updated successfully!');

        } catch (\Exception $e) {
            Log::error('Product update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $productName = $product->name;

            if ($product->image && Storage::disk('public')->exists('products/' . $product->image)) {
                Storage::disk('public')->delete('products/' . $product->image);
            }

            $recipeCount = DB::table('recipes')
                ->where('product_id', $id)
                ->count();

            if ($recipeCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete "' . $productName . '" because it has ' . $recipeCount . ' recipe(s).'
                ], 400);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => '✅ Product "' . $productName . '" deleted successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Product deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function deleteMultiple(Request $request)
    {
        try {
            $request->validate([
                'product_ids' => 'required|array',
                'product_ids.*' => 'exists:products,id'
            ]);

            $productIds = $request->product_ids;
            $deletedCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($productIds as $productId) {
                try {
                    $product = Product::find($productId);
                    if ($product) {
                        if ($product->image && Storage::disk('public')->exists('products/' . $product->image)) {
                            Storage::disk('public')->delete('products/' . $product->image);
                        }

                        $recipeCount = DB::table('recipes')
                            ->where('product_id', $productId)
                            ->count();

                        if ($recipeCount > 0) {
                            $errors[] = 'Cannot delete "' . $product->name . '" because it has ' . $recipeCount . ' recipe(s).';
                            continue;
                        }

                        $product->delete();
                        $deletedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = 'Failed to delete product ID ' . $productId . ': ' . $e->getMessage();
                }
            }

            DB::commit();

            $message = '✅ ' . $deletedCount . ' product(s) deleted successfully.';
            if (!empty($errors)) {
                $message .= ' Errors: ' . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete multiple products error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }
}