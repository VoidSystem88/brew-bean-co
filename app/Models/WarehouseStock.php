<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    use HasFactory;

    protected $table = 'warehouse_stock';

    protected $fillable = [
        'item_id',
        'stock_quantity',
        'low_stock_threshold',
        'reorder_point',
        'reorder_quantity',
    ];

    protected $casts = [
        'stock_quantity' => 'decimal:2',
        'low_stock_threshold' => 'integer',
        'reorder_point' => 'integer',
        'reorder_quantity' => 'integer',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}