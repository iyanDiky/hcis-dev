<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait AuditTrail
{
    protected static function bootAuditTrail()
    {
        static::creating(function ($model) {
            $model->created_by = self::getAuditUser();
        });

        static::updating(function ($model) {
            $model->updated_by = self::getAuditUser();
        });

        static::deleting(function ($model) {
            $model->delete_by = self::getAuditUser();
            // Since we use soft deletes with custom 'delete_at', we need to save the delete_by before soft deleting
            // But if it's a hard delete, it doesn't matter.
            // If soft deleting, the 'deleting' event fires, we set delete_by, and then the 'runSoftDelete' actually saves it.
            // Wait, Laravel's SoftDeletes overrides runSoftDelete to update deleted_at and save. 
            // We just need to set the property here so it gets saved along with delete_at.
        });
    }

    protected static function getAuditUser()
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Expected format: id_sdm-bagian_seksi-unit_kerja
            // Since we don't have bagian_seksi-unit_kerja implemented yet, we just use sdm ID or username
            $sdmId = $user->sdm ?? $user->username;
            return $sdmId . '-unknown-unknown'; 
        }

        return 'System';
    }
}
