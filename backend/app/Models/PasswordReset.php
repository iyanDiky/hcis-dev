<?php

namespace App\Models;

use App\Traits\AuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PasswordReset extends Model
{
    use HasUuids, SoftDeletes, AuditTrail;

    protected $table = 'password_reset';
    public const DELETED_AT = 'delete_at';

    protected $fillable = ['user', 'token'];

    public function userRelation()
    {
        return $this->belongsTo(User::class, 'user', 'id');
    }
}
