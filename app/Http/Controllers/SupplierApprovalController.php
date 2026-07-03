<?php

namespace App\Http\Controllers;

use App\Models\RestockOrder;
use Illuminate\Http\Request;

class SupplierApprovalController extends Controller
{
    public function approve($token)
    {
        $order = RestockOrder::where('approved_token', $token)->firstOrFail();
        
        if ($order->status === 'approved') {
            return view('supplier.approval', [
                'order' => $order,
                'message' => 'This order has already been approved.',
                'status' => 'already_approved'
            ]);
        }

        if ($order->status === 'delivered') {
            return view('supplier.approval', [
                'order' => $order,
                'message' => 'This order has already been delivered.',
                'status' => 'already_delivered'
            ]);
        }

        $order->update([
            'status' => 'approved',
            'approved_at' => now()
        ]);

        return view('supplier.approval', [
            'order' => $order,
            'message' => '✅ Order approved successfully! Stock will be updated upon delivery.',
            'status' => 'approved'
        ]);
    }

    public function reject($token)
    {
        $order = RestockOrder::where('approved_token', $token)->firstOrFail();

        if ($order->status === 'approved' || $order->status === 'delivered') {
            return view('supplier.approval', [
                'order' => $order,
                'message' => 'Cannot reject this order as it is already ' . $order->status . '.',
                'status' => 'error'
            ]);
        }

        $order->update([
            'status' => 'rejected'
        ]);

        return view('supplier.approval', [
            'order' => $order,
            'message' => '❌ Order rejected. Please contact the branch for more details.',
            'status' => 'rejected'
        ]);
    }

    public function view($token)
    {
        $order = RestockOrder::where('approved_token', $token)->firstOrFail();
        
        return view('supplier.view', compact('order'));
    }
}