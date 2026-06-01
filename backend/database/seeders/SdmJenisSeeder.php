<?php

namespace Database\Seeders;

use App\Models\SdmJenis;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SdmJenisSeeder extends Seeder
{
    public function run(): void
    {
        $jenis = ['Admin', 'Pegawai', 'Pengurus', 'Bakat'];

        foreach ($jenis as $j) {
            SdmJenis::firstOrCreate(
                ['jenis' => $j],
                [
                    'id' => Str::uuid(),
                    'created_by' => 'System',
                    'updated_by' => 'System'
                ]
            );
        }
    }
}
