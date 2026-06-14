<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MsProvinsi extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'ms_provinsi';

    protected $fillable = [
        'provinsi',
        'created_by',
        'updated_by',
        'delete_at',
        'delete_by'
    ];

    const DELETED_AT = 'delete_at';

    /**
     * Override the newUniqueId method to use UUID v7 if using Laravel 11.
     * Str::orderedUuid() or Str::uuidV7() can be used. We'll use orderedUuid() for v7-like behavior or uuid() for v4 if not available.
     * Actually, Laravel 11 uses v4 by default for HasUuids, but we can override it:
     */
    public function newUniqueId()
    {
        return (string) Str::orderedUuid();
    }
}
