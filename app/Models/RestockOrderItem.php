<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'restock_order_id',
        'item_id',
        'quantity',
        'current_stock',
    ];

    public function restockOrder()
    {
        return $this->belongsTo(RestockOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}