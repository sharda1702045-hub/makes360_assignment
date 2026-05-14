<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignEmail extends Model
{
    protected $fillable = ['campaign_id', 'contact_id', 'status', 'message_id', 'error_message'];
}
