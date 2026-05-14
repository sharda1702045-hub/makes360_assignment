<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = ['user_id', 'name', 'subject', 'body_html', 'body_text'];

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
}
