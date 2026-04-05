<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'tb_messages';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'message',
    ];
}
