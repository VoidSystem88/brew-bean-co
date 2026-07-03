<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'contact_person',
        'website',
        'notes'
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function restockOrders()
    {
        return $this->hasMany(RestockOrder::class);
    }
}