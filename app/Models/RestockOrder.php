<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'supplier_id',
        'order_number',
        'status',
        'order_date',
        'expected_delivery_date',
        'notes',
        'created_by',
        'approved_at',
        'approved_token',
        'delivered_at'
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'approved_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(RestockOrderItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isSent()
    {
        return $this->status === 'sent';
    }

    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}