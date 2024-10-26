<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends Controller
{
    public function login(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'password' => 'required|string',
    ]);

    $user = User::where('name', $request->name)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'name' => ['The provided credentials are incorrect.'],
        ]);
    }

    $otp = rand(100000, 999999);  
    $user->otp_code = $otp;
    $user->otp_expires_at = now()->addMinutes(10); 
    $user->save();

  
    Log::info('Generated OTP for user ' . $user->email . ': ' . $otp);
    
    return response()->json([
        'message' => 'the OTP has been sent.',
    ], 200);
}
}
