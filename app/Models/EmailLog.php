<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = ['message_id', 'event', 'payload'];
    protected $casts = ['payload' => 'array'];
}
