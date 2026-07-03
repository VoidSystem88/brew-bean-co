<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Item;
use App\Models\Branch;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $stats = [
            'products' => Product::count(),
            'items' => Item::count(),
            'branches' => Branch::count(),
            'suppliers' => Supplier::count(),
        ];
        return view('admin.settings', compact('stats'));
    }
}