<?php

namespace App\Models;

use App\Traits\AuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sdm extends Model
{
    use HasUuids, SoftDeletes, AuditTrail;

    protected $table = 'sdm';
    public const DELETED_AT = 'delete_at';

    protected $fillable = ['sdm_data', 'jenis', 'nomor_rekening'];

    public function data()
    {
        return $this->belongsTo(SdmData::class, 'sdm_data', 'id');
    }

    public function jenisSdm()
    {
        return $this->belongsTo(SdmJenis::class, 'jenis', 'id');
    }
}
