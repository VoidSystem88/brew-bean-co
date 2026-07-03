<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class QrScannerController extends Controller
{
    public function index()
    {
        return view('qr.scanner');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string'
        ]);

        try {
            $data = json_decode($request->qr_data, true);
            
            if (!$data || !isset($data['id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code data.'
                ], 400);
            }

            $customer = Customer::with('sales')->find($data['id']);
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'loyalty_points' => $customer->loyalty_points,
                    'total_purchases' => $customer->sales->count(),
                    'total_spent' => $customer->sales->sum('total_amount')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    public function viewCustomer($id)
    {
        $customer = Customer::with(['sales.items.product'])->findOrFail($id);
        return view('qr.customer-details', compact('customer'));
    }
}