<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'first_name',
        'last_name',
        'status',
        'attributes'
    ];

    protected $casts = [
        'attributes' => 'array'
    ];

    public function lists()
    {
        return $this->belongsToMany(ContactList::class, 'contact_list_mapping', 'contact_id', 'contact_list_id');
    }
}
