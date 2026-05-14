<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactList extends Model
{
    protected $fillable = ['user_id', 'name', 'total_contacts'];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_list_mapping', 'contact_list_id', 'contact_id');
    }
}
