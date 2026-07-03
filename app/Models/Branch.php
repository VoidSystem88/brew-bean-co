<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'location',
        'contact_number',
        'address',
        'phone',
        'email',
    ];

    protected $dates = ['deleted_at'];

    public function staff()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'branch_item')
                    ->withPivot('stock_quantity', 'low_stock_threshold')
                    ->withTimestamps();
    }

    public function transfersFrom()
    {
        return $this->hasMany(Transfer::class, 'from_branch_id');
    }

    public function transfersTo()
    {
        return $this->hasMany(Transfer::class, 'to_branch_id');
    }
}