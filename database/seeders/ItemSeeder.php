<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Raw Materials
            ['name' => 'Coffee Beans', 'category' => 'Raw Materials', 'unit' => 'kg', 'min_stock_alert' => 5],
            ['name' => 'Milk', 'category' => 'Raw Materials', 'unit' => 'liters', 'min_stock_alert' => 10],
            ['name' => 'Sugar', 'category' => 'Raw Materials', 'unit' => 'kg', 'min_stock_alert' => 3],
            ['name' => 'Vanilla Syrup', 'category' => 'Raw Materials', 'unit' => 'liters', 'min_stock_alert' => 2],
            ['name' => 'Caramel Syrup', 'category' => 'Raw Materials', 'unit' => 'liters', 'min_stock_alert' => 2],
            ['name' => 'Chocolate Syrup', 'category' => 'Raw Materials', 'unit' => 'liters', 'min_stock_alert' => 2],
            
            // Packaging
            ['name' => 'Paper Cups (12oz)', 'category' => 'Packaging', 'unit' => 'pieces', 'min_stock_alert' => 100],
            ['name' => 'Paper Cups (16oz)', 'category' => 'Packaging', 'unit' => 'pieces', 'min_stock_alert' => 100],
            ['name' => 'Lids (12oz)', 'category' => 'Packaging', 'unit' => 'pieces', 'min_stock_alert' => 100],
            ['name' => 'Lids (16oz)', 'category' => 'Packaging', 'unit' => 'pieces', 'min_stock_alert' => 100],
            ['name' => 'Napkins', 'category' => 'Packaging', 'unit' => 'packs', 'min_stock_alert' => 20],
            ['name' => 'Stirrers', 'category' => 'Packaging', 'unit' => 'pieces', 'min_stock_alert' => 200],
        ];

        foreach ($items as $itemData) {
            $item = Item::create($itemData);
            
            // Add to all branches with initial stock
            $branches = Branch::all();
            foreach ($branches as $branch) {
                $stock = rand(15, 50);
                $branch->items()->attach($item->id, [
                    'stock_quantity' => $stock,
                    'low_stock_threshold' => $item->min_stock_alert,
                ]);
            }
        }
    }
}
