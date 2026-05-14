<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'template_id', 
        'contact_list_id', 
        'name', 
        'status', 
        'scheduled_at', 
        'total_recipients', 
        'sent_count',
        'failed_count',
        'batch_id'
    ];

    public function stats()
    {
        return $this->hasOne(CampaignStat::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function contactList()
    {
        return $this->belongsTo(ContactList::class);
    }
}
