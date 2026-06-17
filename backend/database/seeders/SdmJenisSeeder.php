<?php

namespace Database\Seeders;

use App\Models\SdmJenis;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SdmJenisSeeder extends Seeder
{
    public function run(): void
    {
        $jenisList = [
            "organik",
            "bakat",
            "tenaga alih daya",
            "pengurus",
            "dewan komite",
            "dewan pengawas syariah",
            "staf khusus"
        ];

        foreach ($jenisList as $j) {
            SdmJenis::firstOrCreate(
                ['jenis' => $j],
                [
                    'id' => Str::orderedUuid()->toString(),
                    'created_by' => 'system',
                    'updated_by' => 'system'
                ]
            );
        }
    }
}
