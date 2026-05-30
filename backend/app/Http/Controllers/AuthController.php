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

        // For PoC, let's auto-create if admin doesn't exist
        if (!$user && $request->username === 'admin' && $request->password === 'password') {
            // Create dummy SDM Data
            $sdmData = SdmData::create([
                'email' => 'admin@admin.com',
                'nik' => '1234567890123456',
                'nama' => 'Admin HCIS',
                'jk' => 'L',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1990-01-01',
                'agama' => 'Islam',
                'status_pernikahan' => 'B',
                'nomor_telp' => '081234567890',
                'alamat_ktp' => 'Jl. Admin No. 1',
                'alamat_domisili' => 'Jl. Admin No. 1'
            ]);

            // Create dummy SDM
            $sdm = Sdm::create([
                'sdm_data' => $sdmData->id
            ]);

            // Create admin user
            $user = User::create([
                'sdm' => $sdm->id,
                'username' => 'admin',
                'password' => Hash::make('password'),
                'status' => 1,
                'error_login' => 0
            ]);
        }

        if (!$user) {
            return response()->json([
                'message' => 'Username tidak ditemukan'
            ], 401);
        }

        if ($user->status == 0) {
            return response()->json([
                'message' => 'Akun tidak aktif'
            ], 403);
        }

        if (!Hash::check($request->password, $user->password)) {
            // Increment error_login
            $user->error_login += 1;
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

        // Load SDM Data relation
        $user->load('sdmRelation.data');

        // Dummy token for PoC
        $token = 'dummy-token-'.time();

        return response()->json([
            'message' => 'Login sukses',
            'token' => $token,
            'user' => $user
        ]);
    }
}
