<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Sdm;
use App\Models\SdmData;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Username tidak ditemukan'
            ], 401);
        }

        if ($user->status == 0 || $user->status == 2) {
            return response()->json([
                'message' => 'Akun tidak aktif atau diblokir'
            ], 403);
        }

        if (!Hash::check($request->password, $user->password)) {
            // Increment error_login
            $user->error_login += 1;
            
            if ($user->error_login >= 3) {
                $user->status = 2; // block user
            }
            
            $user->save();

            return response()->json([
                'message' => 'Password salah'
            ], 401);
        }

        // Reset error_login on successful auth
        if ($user->error_login > 0) {
            $user->error_login = 0;
            $user->save();
        }
        
        // Handle Single Device Login Limit
        if (env('SINGLE_DEVICE_LOGIN', true)) {
            $user->tokens()->delete();
        }

        // Load SDM Data relation
        $user->load('sdmRelation.data');

        // Create Sanctum Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login sukses',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout sukses'
        ]);
    }
}
