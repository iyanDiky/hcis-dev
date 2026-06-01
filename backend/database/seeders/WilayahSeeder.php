<?php

namespace Database\Seeders;

use App\Models\KotaKab;
use App\Models\Provinsi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('data/wilayah.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->error('File wilayah.json tidak ditemukan!');
            return;
        }

        $data = json_decode(file_get_contents($jsonPath), true);
        
        $provinsiMap = []; // cache to avoid multiple queries
        
        foreach ($data as $row) {
            $namaProvinsi = trim($row['nama_provinsi']);
            $namaKotaKab = trim($row['nama_kabupaten_kota']);
            
            // 1. Get or Create Provinsi
            if (!isset($provinsiMap[$namaProvinsi])) {
                $provinsi = Provinsi::firstOrCreate(
                    ['provinsi' => $namaProvinsi],
                    [
                        'id' => Str::uuid(),
                        'created_by' => 'System',
                        'updated_by' => 'System'
                    ]
                );
                $provinsiMap[$namaProvinsi] = $provinsi->id;
            }
            
            $provinsiId = $provinsiMap[$namaProvinsi];
            
            // 2. Get or Create Kota/Kab
            KotaKab::firstOrCreate(
                [
                    'kota_kabupaten' => $namaKotaKab,
                    'provinsi' => $provinsiId
                ],
                [
                    'id' => Str::uuid(),
                    'created_by' => 'System',
                    'updated_by' => 'System'
                ]
            );
        }
        
        $this->command->info('Berhasil seeding wilayah (Provinsi & Kota/Kab)');
    }
}
