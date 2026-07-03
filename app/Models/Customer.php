<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'customer';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'password',
        'customer_code',
        'qr_code',
        'loyalty_points',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'loyalty_points' => 'integer',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function getQrCodeUrl()
    {
        return route('customer.qr', $this->id);
    }

    public function generateQrCode()
    {
        $data = json_encode([
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->customer_code
        ]);
        return $data;
    }
}