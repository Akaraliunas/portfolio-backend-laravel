<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'is_read', // If you added this in the migration
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];
}
