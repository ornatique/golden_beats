<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'user_id',
        'quantity',
        'weight',
        'status',
        'remarks',
    ];

     public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
     public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

