<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    public function generateCode()
    {
        $customer = Auth::guard('customer')->user();
        $code = 'REF-' . strtoupper(Str::random(6));
        
        return response()->json(['success' => true, 'code' => $code]);
    }

    public function useReferral(Request $request)
    {
        $code = $request->code;
        $referral = Referral::where('referral_code', $code)->where('is_used', false)->first();

        if (!$referral) {
            return response()->json(['success' => false, 'message' => 'Invalid referral code']);
        }

        $referral->update(['is_used' => true, 'used_at' => now()]);
        
        $referrer = Customer::find($referral->referrer_id);
        $referrer->increment('loyalty_points', 50);

        return response()->json(['success' => true, 'message' => 'Referral applied!']);
    }
}
