<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\User;
use App\Models\Item;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];
        $user = Auth::user();

        // Search Products
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get();
        foreach ($products as $product) {
            $results[] = [
                'type' => 'product',
                'title' => $product->name,
                'subtitle' => '₱' . number_format($product->price, 2),
                'url' => route('products.edit', $product),
                'image' => $product->image
            ];
        }

        // Search Customers
        if ($user->isAdmin() || $user->isManager() || $user->isStaff()) {
            $customers = Customer::where('name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->orWhere('customer_code', 'LIKE', "%{$query}%")
                ->limit(5)
                ->get();
            foreach ($customers as $customer) {
                $results[] = [
                    'type' => 'customer',
                    'title' => $customer->name,
                    'subtitle' => $customer->customer_code . ' | ' . $customer->email,
                    'url' => route('customers.show', $customer)
                ];
            }
        }

        // Search Branches (Admin/Manager only)
        if ($user->isAdmin() || $user->isManager()) {
            $branches = Branch::where('name', 'LIKE', "%{$query}%")
                ->orWhere('address', 'LIKE', "%{$query}%")
                ->limit(5)
                ->get();
            foreach ($branches as $branch) {
                $results[] = [
                    'type' => 'branch',
                    'title' => str_replace('☕ Brew & Bean Co. - ', '', $branch->name),
                    'subtitle' => $branch->address ?? '',
                    'url' => route('branches.edit', $branch)
                ];
            }

            // Search Staff
            $staff = User::where('name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->limit(5)
                ->get();
            foreach ($staff as $staffMember) {
                if ($staffMember->id !== $user->id) {
                    $results[] = [
                        'type' => 'staff',
                        'title' => $staffMember->name,
                        'subtitle' => $staffMember->email . ' | ' . ucfirst($staffMember->role),
                        'url' => route('staff.edit', $staffMember)
                    ];
                }
            }

            // Search Items (Ingredients)
            $items = Item::where('name', 'LIKE', "%{$query}%")
                ->orWhere('category', 'LIKE', "%{$query}%")
                ->limit(5)
                ->get();
            foreach ($items as $item) {
                $results[] = [
                    'type' => 'item',
                    'title' => $item->name,
                    'subtitle' => $item->category . ' | ' . $item->unit,
                    'url' => route('warehouse.index') . '?search=' . urlencode($item->name)
                ];
            }
        }

        // Search Sales (Admin/Manager only)
        if ($user->isAdmin() || $user->isManager()) {
            $sales = Sale::with(['customer', 'branch'])
                ->where('id', 'LIKE', "%{$query}%")
                ->orWhereHas('customer', function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%");
                })
                ->limit(5)
                ->get();
            foreach ($sales as $sale) {
                $results[] = [
                    'type' => 'sale',
                    'title' => 'Sale #' . $sale->id,
                    'subtitle' => ($sale->customer->name ?? 'Walk-in') . ' | ₱' . number_format($sale->total_amount, 2),
                    'url' => route('reports.index') . '?search=' . $sale->id
                ];
            }
        }

        // Limit results to 20
        $results = array_slice($results, 0, 20);

        return response()->json($results);
    }

    public function index(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return redirect()->route('dashboard');
        }

        $user = Auth::user();
        $results = [];

        // Search Products
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->get();
        foreach ($products as $product) {
            $results[] = [
                'type' => 'product',
                'title' => $product->name,
                'subtitle' => '₱' . number_format($product->price, 2),
                'url' => route('products.edit', $product),
                'image' => $product->image
            ];
        }

        // Search Customers
        if ($user->isAdmin() || $user->isManager() || $user->isStaff()) {
            $customers = Customer::where('name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->orWhere('customer_code', 'LIKE', "%{$query}%")
                ->get();
            foreach ($customers as $customer) {
                $results[] = [
                    'type' => 'customer',
                    'title' => $customer->name,
                    'subtitle' => $customer->customer_code . ' | ' . $customer->email,
                    'url' => route('customers.show', $customer)
                ];
            }
        }

        // Search Branches (Admin/Manager only)
        if ($user->isAdmin() || $user->isManager()) {
            $branches = Branch::where('name', 'LIKE', "%{$query}%")
                ->orWhere('address', 'LIKE', "%{$query}%")
                ->get();
            foreach ($branches as $branch) {
                $results[] = [
                    'type' => 'branch',
                    'title' => str_replace('☕ Brew & Bean Co. - ', '', $branch->name),
                    'subtitle' => $branch->address ?? '',
                    'url' => route('branches.edit', $branch)
                ];
            }

            // Search Staff
            $staff = User::where('name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->get();
            foreach ($staff as $staffMember) {
                if ($staffMember->id !== $user->id) {
                    $results[] = [
                        'type' => 'staff',
                        'title' => $staffMember->name,
                        'subtitle' => $staffMember->email . ' | ' . ucfirst($staffMember->role),
                        'url' => route('staff.edit', $staffMember)
                    ];
                }
            }

            // Search Items (Ingredients)
            $items = Item::where('name', 'LIKE', "%{$query}%")
                ->orWhere('category', 'LIKE', "%{$query}%")
                ->get();
            foreach ($items as $item) {
                $results[] = [
                    'type' => 'item',
                    'title' => $item->name,
                    'subtitle' => $item->category . ' | ' . $item->unit,
                    'url' => route('warehouse.index') . '?search=' . urlencode($item->name)
                ];
            }
        }

        // Search Sales (Admin/Manager only)
        if ($user->isAdmin() || $user->isManager()) {
            $sales = Sale::with(['customer', 'branch'])
                ->where('id', 'LIKE', "%{$query}%")
                ->orWhereHas('customer', function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%");
                })
                ->get();
            foreach ($sales as $sale) {
                $results[] = [
                    'type' => 'sale',
                    'title' => 'Sale #' . $sale->id,
                    'subtitle' => ($sale->customer->name ?? 'Walk-in') . ' | ₱' . number_format($sale->total_amount, 2),
                    'url' => route('reports.index') . '?search=' . $sale->id
                ];
            }
        }

        // Group results by type
        $grouped = [];
        foreach ($results as $result) {
            $grouped[$result['type']][] = $result;
        }

        // Get counts for each type
        $counts = [];
        foreach ($grouped as $type => $items) {
            $counts[$type] = count($items);
        }

        return view('search.results', compact('results', 'grouped', 'counts', 'query'));
    }
}