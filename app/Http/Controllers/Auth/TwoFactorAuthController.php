<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TwoFactorAuthController extends Controller
{
    public function verifyOtp(Request $request)
    {

        $request->validate([
            'otp_code' => 'required|integer',
        ]);
        $user = User::where('otp_code', $request->otp_code)->where('otp_expires_at', '>', now())->first();
        

        if (! $user) {
            return response()->json(['message' => 'Invalid or expired OTP'], 401);
        }

        // Clear OTP after verification
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }
}
