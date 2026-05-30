<?php

namespace App\Models;

use App\Traits\AuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SdmJenis extends Model
{
    use HasUuids, SoftDeletes, AuditTrail;

    protected $table = 'sdm_jenis';
    public const DELETED_AT = 'delete_at';

    protected $fillable = ['jenis'];
}
