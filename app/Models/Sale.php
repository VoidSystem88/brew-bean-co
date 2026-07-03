<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'customer_id',
        'walkin_name',
        'total_amount',
        'sale_date',
        'sync_status',
        'delivery_address',
        'delivery_status',
        'order_notes',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getCustomerNameAttribute()
    {
        if ($this->customer_id) {
            return $this->customer->name ?? 'Unknown Member';
        }
        if ($this->walkin_name) {
            return $this->walkin_name;
        }
        return 'Walk-in Customer';
    }
}