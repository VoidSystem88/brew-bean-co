<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'category',
    ];

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Check if product is available in a branch
    public function isAvailableInBranch($branchId)
    {
        if ($this->recipes->isEmpty()) {
            return false;
        }

        foreach ($this->recipes as $recipe) {
            $branchStock = \DB::table('branch_item')
                ->where('branch_id', $branchId)
                ->where('item_id', $recipe->item_id)
                ->first();

            $stockQty = $branchStock->stock_quantity ?? 0;
            
            // If batch, check if enough for at least 1 batch
            if ($recipe->is_batch && $recipe->batch_size > 1) {
                if ($stockQty < $recipe->quantity) {
                    return false;
                }
            } else {
                if ($stockQty < $recipe->quantity) {
                    return false;
                }
            }
        }
        
        return true;
    }

    // Get max servings possible in a branch
    public function getMaxServingsInBranch($branchId)
    {
        if ($this->recipes->isEmpty()) {
            return 0;
        }

        $maxServings = PHP_INT_MAX;

        foreach ($this->recipes as $recipe) {
            $branchStock = \DB::table('branch_item')
                ->where('branch_id', $branchId)
                ->where('item_id', $recipe->item_id)
                ->first();

            $stockQty = $branchStock->stock_quantity ?? 0;
            $servings = $recipe->getServingsPossible($stockQty);
            
            if ($servings < $maxServings) {
                $maxServings = $servings;
            }
        }
        
        return $maxServings;
    }
}