<?php

namespace App\Http\Controllers;

use App\Models\CustomerFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerFeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sale_id' => 'required|exists:sales,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $feedback = CustomerFeedback::create([
                'sale_id' => $request->sale_id,
                'customer_id' => Auth::guard('customer')->id(),
                'rating' => $request->rating,
                'comment' => $request->comment,
                'type' => $request->type ?? 'delivery'
            ]);

            return response()->json(['success' => true, 'message' => 'Thank you for your feedback!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
