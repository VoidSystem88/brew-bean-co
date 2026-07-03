<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\WarehouseStock;
use Illuminate\Database\Seeder;

class WarehouseStockSeeder extends Seeder
{
    public function run(): void
    {
        $items = Item::all();
        
        foreach ($items as $item) {
            WarehouseStock::create([
                'item_id' => $item->id,
                'stock_quantity' => rand(50, 200),
                'low_stock_threshold' => 10,
                'reorder_point' => 20,
                'reorder_quantity' => 50,
            ]);
        }
    }
}