<?php

namespace App\Traits;

use App\Models\LogAktifitas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Log an activity.
     *
     * @param string $activity Description of the activity
     * @param string|null $type Type of activity (transaksi, slot, auth, etc.)
     * @param mixed|null $model The model being affected
     * @param array|null $metadata Additional data
     * @return void
     */
    public function logActivity($activity, $type = null, $model = null, $metadata = null)
    {
        $userId = Auth::id();

        // If no authenticated user, but the model is a User, use its ID
        if (!$userId && $model instanceof \App\Models\User) {
            $userId = $model->id;
        }

        LogAktifitas::create([
            'id_user' => $userId,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'aktivitas' => $activity,
            'tipe_aktivitas' => $type,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->getKey() : null,
            'metadata' => $metadata,
            'waktu_aktivitas' => now(),
        ]);
    }
}
