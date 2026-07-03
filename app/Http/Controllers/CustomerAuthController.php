<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Order;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CustomerAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return redirect()->back()
                ->with('error', 'Invalid email or password.')
                ->withInput();
        }

        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.dashboard')
            ->with('success', 'Welcome back, ' . $customer->name . '!');
    }

    public function showRegisterForm()
    {
        return view('customer.auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'password' => Hash::make($request->password),
                'customer_code' => 'CUS-' . strtoupper(uniqid()),
                'loyalty_points' => 0,
                'is_active' => true,
            ]);

            Auth::guard('customer')->login($customer);

            return redirect()->route('customer.dashboard')
                ->with('success', 'Welcome to Brew & Bean Co., ' . $customer->name . '!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Registration failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function logout()
    {
        Auth::guard('customer')->logout();
        return redirect()->route('customer.login')
            ->with('success', 'Logged out successfully.');
    }

    public function dashboard()
    {
        $customer = Auth::guard('customer')->user();
        
        if (!$customer) {
            return redirect()->route('customer.login');
        }

        $products = Product::orderBy('name')->get();
        $branches = Branch::where('is_active', true)->get();
        $recentPurchases = $customer->sales()->with('branch')->latest()->take(10)->get();
        $totalSpent = $customer->sales()->sum('total_amount') ?? 0;
        $totalOrders = $customer->sales()->count();
        
        $mostBought = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.customer_id', $customer->id)
            ->select('products.id', 'products.name', 'products.price', 
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_spent'))
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        return view('customer.dashboard', compact(
            'customer', 'products', 'branches',
            'recentPurchases', 'totalSpent', 'totalOrders', 'mostBought'
        ));
    }

    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'branch_id' => 'required|exists:branches,id',
            'delivery_address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $customer = Auth::guard('customer')->user();
            $totalAmount = 0;
            $items = [];

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            $sale = Sale::create([
                'branch_id' => $request->branch_id,
                'customer_id' => $customer->id,
                'user_id' => null,
                'total_amount' => $totalAmount,
                'sale_date' => now(),
                'sync_status' => 'synced',
                'delivery_address' => $request->delivery_address ?? $customer->address,
                'delivery_status' => 'pending',
                'order_notes' => $request->notes,
            ]);

            foreach ($items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);

                Order::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'status' => 'pending',
                    'notes' => $request->notes ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $sale->id,
                'total' => $totalAmount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], 400);
        }
    }

    public function orderHistory()
    {
        $customer = Auth::guard('customer')->user();
        $orders = $customer->sales()->with(['items.product', 'branch'])->latest()->paginate(20);
        
        return view('customer.orders', compact('orders'));
    }

    public function orderDetails($id)
    {
        $customer = Auth::guard('customer')->user();
        $order = Sale::with(['items.product', 'branch'])
            ->where('customer_id', $customer->id)
            ->findOrFail($id);
        
        return view('customer.order-details', compact('order'));
    }
}