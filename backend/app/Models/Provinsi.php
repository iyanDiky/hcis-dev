<?php

namespace App\Models;

use App\Traits\AuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provinsi extends Model
{
    use HasUuids, SoftDeletes, AuditTrail;

    protected $table = 'ms_provinsi';
    public const DELETED_AT = 'delete_at';

    protected $fillable = ['provinsi'];
}
