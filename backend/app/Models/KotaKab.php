<?php

namespace App\Models;

use App\Traits\AuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KotaKab extends Model
{
    use HasUuids, SoftDeletes, AuditTrail;

    protected $table = 'ms_kota_kab';
    public const DELETED_AT = 'delete_at';

    protected $fillable = ['kota_kabupaten', 'provinsi'];

    public function provinsiRelation()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi', 'id');
    }
}
