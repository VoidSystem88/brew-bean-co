<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Espresso',
                'category' => 'Coffee',
                'price' => 3.50,
                'description' => 'Strong and bold single shot of espresso.',
            ],
            [
                'name' => 'Cappuccino',
                'category' => 'Coffee',
                'price' => 4.50,
                'description' => 'Rich espresso with steamed milk and foam.',
            ],
            [
                'name' => 'Latte',
                'category' => 'Coffee',
                'price' => 4.50,
                'description' => 'Smooth espresso with steamed milk.',
            ],
            [
                'name' => 'Americano',
                'category' => 'Coffee',
                'price' => 3.75,
                'description' => 'Espresso diluted with hot water.',
            ],
            [
                'name' => 'Mocha',
                'category' => 'Coffee',
                'price' => 5.00,
                'description' => 'Chocolate and espresso with steamed milk.',
            ],
            [
                'name' => 'Green Tea',
                'category' => 'Tea',
                'price' => 3.00,
                'description' => 'Premium green tea with antioxidants.',
            ],
            [
                'name' => 'Chai Latte',
                'category' => 'Tea',
                'price' => 4.25,
                'description' => 'Spiced chai with steamed milk.',
            ],
            [
                'name' => 'Croissant',
                'category' => 'Pastry',
                'price' => 2.50,
                'description' => 'Buttery and flaky croissant.',
            ],
            [
                'name' => 'Blueberry Muffin',
                'category' => 'Pastry',
                'price' => 2.75,
                'description' => 'Moist muffin with fresh blueberries.',
            ],
            [
                'name' => 'Chocolate Chip Cookie',
                'category' => 'Pastry',
                'price' => 1.50,
                'description' => 'Classic cookie with chocolate chips.',
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);
            
            $branches = Branch::all();
            foreach ($branches as $branch) {
                $branch->products()->attach($product->id, [
                    'stock_quantity' => rand(20, 100),
                    'low_stock_threshold' => 5,
                ]);
            }
        }
    }
}
