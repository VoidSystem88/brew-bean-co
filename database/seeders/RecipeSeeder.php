<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Item;
use App\Models\Recipe;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        // Get items
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

        // Get products
        $espresso = Product::where('name', 'Espresso')->first();
        $doubleEspresso = Product::where('name', 'Double Espresso')->first();
        $americano = Product::where('name', 'Americano')->first();
        $cappuccino = Product::where('name', 'Cappuccino')->first();
        $latte = Product::where('name', 'Latte')->first();
        $vanillaLatte = Product::where('name', 'Vanilla Latte')->first();
        $caramelLatte = Product::where('name', 'Caramel Latte')->first();
        $mocha = Product::where('name', 'Mocha')->first();
        $whiteMocha = Product::where('name', 'White Mocha')->first();
        $flatWhite = Product::where('name', 'Flat White')->first();
        $macchiato = Product::where('name', 'Macchiato')->first();
        $caramelMacchiato = Product::where('name', 'Caramel Macchiato')->first();
        $affogato = Product::where('name', 'Affogato')->first();
        $coldBrew = Product::where('name', 'Cold Brew')->first();

        $recipes = [];

        // Helper function to add recipe
        function addRecipe(&$recipes, $product, $item, $quantity, $unit) {
            if ($product && $item) {
                $recipes[] = [
                    'product_id' => $product->id,
                    'item_id' => $item->id,
                    'quantity' => $quantity,
                    'unit' => $unit
                ];
            }
        }

        // Espresso: 18g beans + 1 cup + 1 lid
        addRecipe($recipes, $espresso, $coffeeBeans, 18, 'g');
        addRecipe($recipes, $espresso, $cup12, 1, 'pc');
        addRecipe($recipes, $espresso, $lid12, 1, 'pc');

        // Double Espresso: 36g beans + 1 cup + 1 lid
        addRecipe($recipes, $doubleEspresso, $coffeeBeans, 36, 'g');
        addRecipe($recipes, $doubleEspresso, $cup12, 1, 'pc');
        addRecipe($recipes, $doubleEspresso, $lid12, 1, 'pc');

        // Americano: 18g beans + 1 cup + 1 lid
        addRecipe($recipes, $americano, $coffeeBeans, 18, 'g');
        addRecipe($recipes, $americano, $cup12, 1, 'pc');
        addRecipe($recipes, $americano, $lid12, 1, 'pc');

        // Cappuccino: 18g beans + 150ml milk + 10g sugar + 1 cup + 1 lid
        addRecipe($recipes, $cappuccino, $coffeeBeans, 18, 'g');
        addRecipe($recipes, $cappuccino, $milk, 150, 'ml');
        addRecipe($recipes, $cappuccino, $sugar, 10, 'g');
        addRecipe($recipes, $cappuccino, $cup12, 1, 'pc');
        addRecipe($recipes, $cappuccino, $lid12, 1, 'pc');

        // Latte: 18g beans + 200ml milk + 1 cup + 1 lid
        addRecipe($recipes, $latte, $coffeeBeans, 18, 'g');
        addRecipe($recipes, $latte, $milk, 200, 'ml');
        addRecipe($recipes, $latte, $cup12, 1, 'pc');
        addRecipe($recipes, $latte, $lid12, 1, 'pc');

        // Vanilla Latte: 18g beans + 200ml milk + 20ml vanilla + 1 cup + 1 lid
        addRecipe($recipes, $vanillaLatte, $coffeeBeans, 18, 'g');
        addRecipe($recipes, $vanillaLatte, $milk, 200, 'ml');
        addRecipe($recipes, $vanillaLatte, $vanilla, 20, 'ml');
        addRecipe($recipes, $vanillaLatte, $cup12, 1, 'pc');
        addRecipe($recipes, $vanillaLatte, $lid12, 1, 'pc');

        // Caramel Latte: 18g beans + 200ml milk + 20ml caramel + 1 cup + 1 lid
        addRecipe($recipes, $caramelLatte, $coffeeBeans, 18, 'g');
        addRecipe($recipes, $caramelLatte, $milk, 200, 'ml');
        addRecipe($recipes, $caramelLatte, $caramel, 20, 'ml');
        addRecipe($recipes, $caramelLatte, $cup12, 1, 'pc');
        addRecipe($recipes, $caramelLatte, $lid12, 1, 'pc');

        // Mocha: 18g beans + 150ml milk + 20ml chocolate + 1 cup + 1 lid
        addRecipe($recipes, $mocha, $coffeeBeans, 18, 'g');
        addRecipe($recipes, $mocha, $milk, 150, 'ml');
        addRecipe($recipes, $mocha, $chocolate, 20, 'ml');
        addRecipe($recipes, $mocha, $cup12, 1, 'pc');
        addRecipe($recipes, $mocha, $lid12, 1, 'pc');

        // Insert all recipes
        $count = 0;
        foreach ($recipes as $recipe) {
            // Check if recipe already exists
            $exists = Recipe::where('product_id', $recipe['product_id'])
                ->where('item_id', $recipe['item_id'])
                ->exists();
            
            if (!$exists) {
                Recipe::create($recipe);
                $count++;
            }
        }

        echo "✅ Created $count new recipes\n";
        echo "📊 Total recipes: " . Recipe::count() . "\n";
    }
}