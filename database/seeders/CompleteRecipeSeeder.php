<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Item;
use App\Models\Recipe;
use Illuminate\Database\Seeder;

class CompleteRecipeSeeder extends Seeder
{
    public function run(): void
    {
        // Get all items
        $coffeeBeans = Item::where('name', 'Coffee Beans')->first();
        $milk = Item::where('name', 'Milk')->first();
        $sugar = Item::where('name', 'Sugar')->first();
        $vanilla = Item::where('name', 'Vanilla Syrup')->first();
        $caramel = Item::where('name', 'Caramel Syrup')->first();
        $chocolate = Item::where('name', 'Chocolate Syrup')->first();
        $cup12 = Item::where('name', 'Paper Cups (12oz)')->first();
        $cup16 = Item::where('name', 'Paper Cups (16oz)')->first();
        $lid12 = Item::where('name', 'Lids (12oz)')->first();
        $lid16 = Item::where('name', 'Lids (16oz)')->first();
        $napkins = Item::where('name', 'Napkins')->first();
        $stirrers = Item::where('name', 'Stirrers')->first();

        // Get all products
        $products = Product::all();

        // Helper function
        function addRecipe($product, $item, $quantity, $unit) {
            if ($product && $item) {
                $exists = Recipe::where('product_id', $product->id)
                    ->where('item_id', $item->id)
                    ->exists();
                
                if (!$exists) {
                    Recipe::create([
                        'product_id' => $product->id,
                        'item_id' => $item->id,
                        'quantity' => $quantity,
                        'unit' => $unit
                    ]);
                    return true;
                }
            }
            return false;
        }

        $count = 0;

        // COFFEE DRINKS
        foreach ($products as $product) {
            $name = $product->name;
            
            // Espresso based drinks
            if (str_contains($name, 'Espresso') || 
                str_contains($name, 'Americano') || 
                str_contains($name, 'Cappuccino') || 
                str_contains($name, 'Latte') || 
                str_contains($name, 'Mocha') || 
                str_contains($name, 'Flat White') || 
                str_contains($name, 'Macchiato') || 
                str_contains($name, 'Affogato')) {
                
                // All coffee drinks need beans
                if (addRecipe($product, $coffeeBeans, 18, 'g')) $count++;
                
                // Drinks with milk (not Espresso, Americano, Affogato)
                if (!str_contains($name, 'Espresso') && 
                    !str_contains($name, 'Americano') && 
                    !str_contains($name, 'Affogato')) {
                    if (addRecipe($product, $milk, 150, 'ml')) $count++;
                }
                
                // Drinks with sugar
                if (str_contains($name, 'Cappuccino') || 
                    str_contains($name, 'Latte') || 
                    str_contains($name, 'Mocha')) {
                    if (addRecipe($product, $sugar, 10, 'g')) $count++;
                }
                
                // Vanilla Latte
                if (str_contains($name, 'Vanilla')) {
                    if (addRecipe($product, $vanilla, 20, 'ml')) $count++;
                }
                
                // Caramel Latte / Macchiato
                if (str_contains($name, 'Caramel')) {
                    if (addRecipe($product, $caramel, 20, 'ml')) $count++;
                }
                
                // Mocha / White Mocha
                if (str_contains($name, 'Mocha')) {
                    if (addRecipe($product, $chocolate, 20, 'ml')) $count++;
                }
                
                // Cup and Lid
                if (addRecipe($product, $cup12, 1, 'pc')) $count++;
                if (addRecipe($product, $lid12, 1, 'pc')) $count++;
            }
            
            // Cold Brew and Iced drinks
            if (str_contains($name, 'Cold Brew') || str_contains($name, 'Iced')) {
                if (addRecipe($product, $coffeeBeans, 18, 'g')) $count++;
                if (str_contains($name, 'Latte') || str_contains($name, 'Mocha')) {
                    if (addRecipe($product, $milk, 150, 'ml')) $count++;
                }
                if (str_contains($name, 'Caramel')) {
                    if (addRecipe($product, $caramel, 20, 'ml')) $count++;
                }
                if (str_contains($name, 'Mocha')) {
                    if (addRecipe($product, $chocolate, 20, 'ml')) $count++;
                }
                if (addRecipe($product, $cup16, 1, 'pc')) $count++;
                if (addRecipe($product, $lid16, 1, 'pc')) $count++;
            }
            
            // Frappe
            if (str_contains($name, 'Frappe')) {
                if (addRecipe($product, $coffeeBeans, 18, 'g')) $count++;
                if (addRecipe($product, $milk, 150, 'ml')) $count++;
                if (addRecipe($product, $sugar, 15, 'g')) $count++;
                if (str_contains($name, 'Caramel')) {
                    if (addRecipe($product, $caramel, 20, 'ml')) $count++;
                }
                if (str_contains($name, 'Mocha')) {
                    if (addRecipe($product, $chocolate, 20, 'ml')) $count++;
                }
                if (addRecipe($product, $cup16, 1, 'pc')) $count++;
                if (addRecipe($product, $lid16, 1, 'pc')) $count++;
                if (addRecipe($product, $stirrers, 1, 'pc')) $count++;
            }
            
            // TEA & CHOCOLATE
            if (str_contains($name, 'Tea') || str_contains($name, 'Chai')) {
                // Tea doesn't need beans, uses tea bags
                // Just cup and lid
                if (addRecipe($product, $cup12, 1, 'pc')) $count++;
                if (addRecipe($product, $lid12, 1, 'pc')) $count++;
            }
            
            if (str_contains($name, 'Matcha')) {
                if (addRecipe($product, $milk, 150, 'ml')) $count++;
                if (addRecipe($product, $cup12, 1, 'pc')) $count++;
                if (addRecipe($product, $lid12, 1, 'pc')) $count++;
            }
            
            if (str_contains($name, 'Hot Chocolate') || str_contains($name, 'Iced Chocolate')) {
                if (addRecipe($product, $milk, 150, 'ml')) $count++;
                if (addRecipe($product, $chocolate, 20, 'ml')) $count++;
                if (addRecipe($product, $cup12, 1, 'pc')) $count++;
                if (addRecipe($product, $lid12, 1, 'pc')) $count++;
            }
            
            // PASTRIES - only packaging
            if (str_contains($name, 'Croissant') || 
                str_contains($name, 'Danish') || 
                str_contains($name, 'Muffin') || 
                str_contains($name, 'Bread') || 
                str_contains($name, 'Roll') || 
                str_contains($name, 'Brioche')) {
                if (addRecipe($product, $napkins, 1, 'pc')) $count++;
                // Pastries use paper bag or box (we'll use napkins as proxy)
            }
            
            // DESSERTS - plates and utensils
            if (str_contains($name, 'Cheesecake') || 
                str_contains($name, 'Cake') || 
                str_contains($name, 'Tiramisu') || 
                str_contains($name, 'Mousse') || 
                str_contains($name, 'Tart') || 
                str_contains($name, 'Brownies') || 
                str_contains($name, 'Cupcake')) {
                if (addRecipe($product, $napkins, 1, 'pc')) $count++;
                // Desserts come with plate/fork
            }
            
            // SANDWICHES - packaging
            if (str_contains($name, 'Sandwich') || 
                str_contains($name, 'Panini') || 
                str_contains($name, 'Grilled Cheese')) {
                if (addRecipe($product, $napkins, 1, 'pc')) $count++;
            }
            
            // SALADS - bowls
            if (str_contains($name, 'Salad')) {
                if (addRecipe($product, $napkins, 1, 'pc')) $count++;
            }
        }

        echo "✅ Added/Updated $count recipes\n";
        echo "📊 Total recipes: " . Recipe::count() . "\n";
    }
}