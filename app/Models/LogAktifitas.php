<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktifitas extends Model
{
    protected $table = 'tb_log_aktivitas';
    protected $primaryKey = 'id_log';
    protected $fillable = [
        'id_user',
        'aktivitas',
        'waktu_aktivitas',
    ];
}
