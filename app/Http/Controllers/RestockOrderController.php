<?php

namespace App\Http\Controllers;

use App\Models\RestockOrder;
use App\Models\RestockOrderItem;
use App\Models\Item;
use App\Models\Branch;
use App\Models\Supplier;
use App\Mail\RestockOrderMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RestockOrderController extends Controller
{
    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        $lowStockItems = [];

        // Get low stock items for selected branch
        $branchId = request('branch_id', Auth::user()->branch_id ?? 1);
        $branch = Branch::find($branchId);

        if ($branch) {
            $lowStockItems = \DB::table('branch_item')
                ->join('items', 'branch_item.item_id', '=', 'items.id')
                ->where('branch_item.branch_id', $branchId)
                ->whereColumn('branch_item.stock_quantity', '<=', 'branch_item.low_stock_threshold')
                ->select('items.*', 'branch_item.stock_quantity as stock', 'branch_item.low_stock_threshold as threshold')
                ->get();
        }

        return view('restock.create', compact('branches', 'suppliers', 'lowStockItems', 'branch'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'expected_delivery_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
        ]);

        // Generate order number
        $orderNumber = 'RSO-' . date('Ymd') . '-' . Str::random(6);

        // Create order
        $order = RestockOrder::create([
            'branch_id' => $request->branch_id,
            'supplier_id' => $request->supplier_id,
            'order_number' => $orderNumber,
            'status' => 'pending',
            'order_date' => now(),
            'expected_delivery_date' => $request->expected_delivery_date,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        // Create order items
        foreach ($request->items as $itemData) {
            $item = Item::find($itemData['item_id']);
            $currentStock = $item->getStockInBranch($request->branch_id);

            RestockOrderItem::create([
                'restock_order_id' => $order->id,
                'item_id' => $itemData['item_id'],
                'quantity' => $itemData['quantity'],
                'current_stock' => $currentStock,
            ]);
        }

        // Send email to supplier
        try {
            Mail::to($order->supplier->email)->send(new RestockOrderMail($order));
            $order->update(['status' => 'sent']);
        } catch (\Exception $e) {
            \Log::error('Failed to send restock email: ' . $e->getMessage());
        }

        return redirect()->route('restock.orders')
            ->with('success', 'Restock order #' . $orderNumber . ' created and sent to supplier!');
    }

    public function orders()
    {
        $orders = RestockOrder::with(['branch', 'supplier', 'items.item'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('restock.orders', compact('orders'));
    }

    public function show($id)
    {
        $order = RestockOrder::with(['branch', 'supplier', 'items.item', 'createdBy'])
            ->findOrFail($id);

        return view('restock.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = RestockOrder::findOrFail($id);
        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated to ' . $request->status
        ]);
    }

    public function receiveOrder($id)
    {
        $order = RestockOrder::with('items')->findOrFail($id);
        
        // Update stock for each item
        foreach ($order->items as $orderItem) {
            $branchItem = Item::find($orderItem->item_id)
                ->branches()
                ->where('branch_id', $order->branch_id)
                ->first();

            if ($branchItem) {
                $newStock = $branchItem->pivot->stock_quantity + $orderItem->quantity;
                $branchItem->pivot->update(['stock_quantity' => $newStock]);
            }
        }

        $order->update(['status' => 'delivered']);

        return redirect()->route('restock.orders')
            ->with('success', 'Order #' . $order->order_number . ' received and stock updated!');
    }
}
