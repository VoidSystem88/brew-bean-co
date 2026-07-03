<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Staff;
use App\Models\Branch;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index()
    {
        return view('search.index');
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];

        // Search Products
        $results['products'] = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'price', 'description']);

        // Search Customers
        $results['customers'] = Customer::where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->orWhere('customer_code', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'email', 'customer_code']);

        // Search Orders (Sales)
        $results['orders'] = Sale::where('id', 'LIKE', "%{$query}%")
            ->orWhere('walkin_name', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'total_amount', 'walkin_name', 'created_at']);

        // Search Staff
        $results['staff'] = DB::table('users')
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'email', 'role']);

        // Search Branches
        $results['branches'] = Branch::where('name', 'LIKE', "%{$query}%")
            ->orWhere('address', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'address']);

        // Search Inventory
        $results['inventory'] = Item::where('name', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'unit']);

        return response()->json($results);
    }
}