<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // For PoC, let's auto-create if admin@admin.com doesn't exist
            if ($request->email === 'admin@admin.com' && $request->password === 'password') {
                $user = User::create([
                    'name' => 'Admin HCIS',
                    'email' => 'admin@admin.com',
                    'password' => Hash::make('password')
                ]);
            } else {
                return response()->json([
                    'message' => 'Kredensial tidak valid'
                ], 401);
            }
        }

        // Dummy token for PoC
        $token = 'dummy-token-'.time();

        return response()->json([
            'message' => 'Login sukses',
            'token' => $token,
            'user' => $user
        ]);
    }
}
