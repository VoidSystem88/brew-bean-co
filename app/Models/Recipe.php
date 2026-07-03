<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'item_id',
        'quantity',
        'unit',
        'batch_size',
        'is_batch',
    ];

    protected $casts = [
        'is_batch' => 'boolean',
        'batch_size' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Get the actual quantity needed per serving
    public function getQuantityPerServing()
    {
        if ($this->is_batch && $this->batch_size > 1) {
            return $this->quantity / $this->batch_size;
        }
        return $this->quantity;
    }

    // Get servings possible from available stock
    public function getServingsPossible($stockQuantity)
    {
        if ($this->is_batch && $this->batch_size > 1) {
            $batchesPossible = floor($stockQuantity / $this->quantity);
            return $batchesPossible * $this->batch_size;
        }
        return floor($stockQuantity / $this->quantity);
    }
}