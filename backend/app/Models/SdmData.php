<?php

namespace App\Models;

use App\Traits\AuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SdmData extends Model
{
    use HasUuids, SoftDeletes, AuditTrail;

    protected $table = 'sdm_data';
    public const DELETED_AT = 'delete_at';

    protected $fillable = [
        'email', 'nik', 'nama', 'jk', 'tempat_lahir', 'tanggal_lahir', 
        'agama', 'gol_darah', 'status_pernikahan', 'foto', 'spesimen_tanda_tangan', 
        'spesimen_paraf', 'npwp', 'nomor_telp', 'alamat_ktp', 'kota_kab_ktp', 
        'alamat_domisili', 'kota_kab_domisili'
    ];

    public function kotaKabKtp()
    {
        return $this->belongsTo(MsKotaKab::class, 'kota_kab_ktp', 'id');
    }

    public function kotaKabDomisili()
    {
        return $this->belongsTo(MsKotaKab::class, 'kota_kab_domisili', 'id');
    }
}
