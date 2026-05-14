<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BounceLog extends Model
{
    protected $fillable = ['email', 'type', 'sub_type', 'message_id'];
}
