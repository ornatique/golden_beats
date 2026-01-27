<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'event_date',
        'description',
        'location',
        'image',
        'map_link',
        'event_type',
    ];
    protected $casts = [
        'image' => 'array',
    ];
}

