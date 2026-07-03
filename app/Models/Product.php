<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'price',
        'image',
    ];

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image && file_exists(storage_path('app/public/products/' . $this->image))) {
            return asset('storage/products/' . $this->image);
        }
        return asset('images/default-product.png');
    }

    public function hasImage()
    {
        return $this->image && file_exists(storage_path('app/public/products/' . $this->image));
    }
}