<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('name')->get();
        return view('customers.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = Customer::with('sales.items.product')->findOrFail($id);
        $recentPurchases = $customer->sales()->with('branch')->latest()->take(10)->get();
        $totalSpent = $customer->sales()->sum('total_amount') ?? 0;
        $totalOrders = $customer->sales()->count();
        
        return view('customers.show', compact('customer', 'recentPurchases', 'totalSpent', 'totalOrders'));
    }

    public function generateQr($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.qr', compact('customer'));
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customerName = $customer->name;
            
            if ($customer->sales()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete "' . $customerName . '" because they have purchase history.'
                ], 400);
            }
            
            $customer->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Customer "' . $customerName . '" deleted successfully!'
            ]);
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->get('q');
            
            if (empty($query) || strlen($query) < 2) {
                return response()->json([]);
            }
            
            $customers = Customer::where('name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->orWhere('customer_code', 'LIKE', "%{$query}%")
                ->limit(10)
                ->get(['id', 'name', 'email', 'customer_code']);
            
            return response()->json($customers);
            
        } catch (\Exception $e) {
            Log::error('Customer search error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function branches()
    {
        return view('customer.branches');
    }
}