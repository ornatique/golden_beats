<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'customer_id',
        'title',
        'message',
        'type',
        'reference_id',
        'is_read',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

