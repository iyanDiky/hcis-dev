<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MsKotaKab extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'ms_kota_kab';

    // Override deleted_at column name
    const DELETED_AT = 'delete_at';

    protected $fillable = [
        'kota_kabupaten',
        'provinsi',
        'created_by',
        'updated_by',
        'delete_by'
    ];

    /**
     * Generate a new UUID for the model.
     */
    public function newUniqueId()
    {
        // Gunakan orderedUuid untuk performa indexing lebih baik (mirip UUIDv7)
        return (string) Str::orderedUuid();
    }

    /**
     * Relasi ke MsProvinsi
     */
    public function provinsiRel()
    {
        return $this->belongsTo(MsProvinsi::class, 'provinsi', 'id');
    }
}
