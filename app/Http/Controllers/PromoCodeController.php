<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromoCodeController extends Controller
{
    public function index()
    {
        $promoCodes = PromoCode::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.promo-codes', compact('promoCodes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1'
        ]);

        $code = strtoupper(Str::random(8));
        
        PromoCode::create([
            'code' => $code,
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'min_purchase' => $request->min_purchase ?? 0,
            'usage_limit' => $request->usage_limit,
            'is_active' => true
        ]);

        return redirect()->back()->with('success', 'Promo code created: ' . $code);
    }

    public function validateCode(Request $request)
    {
        $code = strtoupper($request->code);
        $promo = PromoCode::where('code', $code)->where('is_active', true)->first();

        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'Invalid promo code']);
        }

        return response()->json(['success' => true, 'promo' => $promo]);
    }

    public function destroy($id)
    {
        PromoCode::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
