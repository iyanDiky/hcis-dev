<?php

namespace Database\Seeders;

use App\Models\Sdm;
use App\Models\SdmData;
use App\Models\SdmJenis;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminJenis = SdmJenis::where('jenis', 'Admin')->first();
        
        if (!$adminJenis) {
            $this->command->error('Jenis SDM Admin belum ada. Harap jalankan SdmJenisSeeder terlebih dahulu.');
            return;
        }

        // 1. Buat SDM Data
        $sdmDataId = Str::uuid();
        $sdmData = SdmData::create([
            'id' => $sdmDataId,
            'email' => 'hcis@bankkalsel.co.id',
            'nik' => '1234567890123456',
            'nama' => 'Super Admin HCIS',
            'jk' => 'L',
            'tempat_lahir' => 'Banjarmasin',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Islam',
            'status_pernikahan' => 'B',
            'nomor_telp' => '081234567890',
            'alamat_ktp' => 'Jl. Lambung Mangkurat',
            'alamat_domisili' => 'Jl. Lambung Mangkurat'
        ]);

        // 2. Buat SDM
        $sdmId = Str::uuid();
        $sdm = Sdm::create([
            'id' => $sdmId,
            'sdm_data' => $sdmData->id,
            'jenis' => $adminJenis->id
        ]);

        // 3. Buat User
        $userId = Str::uuid();
        $user = User::create([
            'id' => $userId,
            'sdm' => $sdm->id,
            'username' => 'admin',
            'password' => Hash::make('password'),
            'status' => 1,
            'error_login' => 0
        ]);
        
        // 4. Update Audit Trail by passing eloquent events
        $auditName = $sdm->id . '-unknown-unknown';
        
        DB::table('sdm_data')->where('id', $sdmData->id)->update([
            'created_by' => $auditName,
            'updated_by' => $auditName
        ]);
        
        DB::table('sdm')->where('id', $sdm->id)->update([
            'created_by' => $auditName,
            'updated_by' => $auditName
        ]);
        
        DB::table('users')->where('id', $user->id)->update([
            'created_by' => $auditName,
            'updated_by' => $auditName
        ]);

        $this->command->info('Berhasil membuat Super Admin HCIS');
    }
}
