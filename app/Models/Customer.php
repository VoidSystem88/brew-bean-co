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
        'birthday',  // <-- Add this
        'password',
        'customer_code',
        'qr_code',
        'loyalty_points',
        'is_active',
        'latitude',
        'longitude',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'loyalty_points' => 'integer',
        'birthday' => 'date',  // <-- Add this
    ];

    // Check if today is the customer's birthday
    public function isBirthdayToday()
    {
        if (!$this->birthday) {
            return false;
        }
        $today = now();
        $birthday = $this->birthday;
        return $birthday->month == $today->month && $birthday->day == $today->day;
    }

    // Get birthday bonus multiplier
    public function getBirthdayMultiplier()
    {
        return $this->isBirthdayToday() ? 2 : 1;
    }

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