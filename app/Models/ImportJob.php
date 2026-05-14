<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportJob extends Model
{
    protected $fillable = [
        'user_id',
        'filename',
        'type',
        'status',
        'total_rows',
        'processed_rows',
        'failed_rows',
        'duplicate_rows'
    ];

    protected $casts = [
        'error_log' => 'array',
    ];
}
