<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class CustomOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image',
        'description',
        'remarks',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id', 'id');
    }
}