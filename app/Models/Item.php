<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'unit',
        'weight_per_unit',
        'volume_per_unit',
        'supplier_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouseStock()
    {
        return $this->hasOne(WarehouseStock::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_item')
                    ->withPivot('stock_quantity', 'low_stock_threshold')
                    ->withTimestamps();
    }

    public function getConversionFactor($toUnit)
    {
        // If same unit, return 1
        if ($this->unit === $toUnit) {
            return 1;
        }

        // If stock unit is 'bags', 'packs', etc. and recipe unit is 'g'
        if (in_array($this->unit, ['bags', 'packs', 'bottles', 'cans', 'boxes']) && $toUnit === 'g') {
            return $this->weight_per_unit ?? 1000; // Default 1000g per bag if not set
        }

        // If stock unit is 'bags', 'packs', etc. and recipe unit is 'ml'
        if (in_array($this->unit, ['bags', 'packs', 'bottles', 'cans', 'boxes']) && $toUnit === 'ml') {
            return $this->volume_per_unit ?? 1000; // Default 1000ml per bag if not set
        }

        // If stock unit is 'kg' and recipe unit is 'g'
        if ($this->unit === 'kg' && $toUnit === 'g') {
            return 1000;
        }

        // If stock unit is 'g' and recipe unit is 'kg'
        if ($this->unit === 'g' && $toUnit === 'kg') {
            return 0.001;
        }

        // If stock unit is 'liters' and recipe unit is 'ml'
        if ($this->unit === 'liters' && $toUnit === 'ml') {
            return 1000;
        }

        // If stock unit is 'ml' and recipe unit is 'liters'
        if ($this->unit === 'ml' && $toUnit === 'liters') {
            return 0.001;
        }

        return 1;
    }
}