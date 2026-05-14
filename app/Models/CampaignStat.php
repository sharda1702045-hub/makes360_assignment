<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignStat extends Model
{
    protected $fillable = ['campaign_id', 'opens', 'clicks', 'bounces', 'complaints', 'unsubscribes'];
}
