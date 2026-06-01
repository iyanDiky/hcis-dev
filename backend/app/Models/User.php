<?php

namespace App\Models;

use App\Traits\AuditTrail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

#[Fillable(['sdm', 'username', 'password', 'password_expired_at', 'status', 'error_login'])]
#[Hidden(['password'])]
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, SoftDeletes, AuditTrail;

    public const DELETED_AT = 'delete_at';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password_expired_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sdmRelation()
    {
        return $this->belongsTo(Sdm::class, 'sdm', 'id');
    }
}
