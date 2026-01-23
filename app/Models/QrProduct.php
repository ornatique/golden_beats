<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrProduct extends Model
{
    protected $fillable = [
        'customer_id',
        'product_id',
        'scanned_name',
        'is_save',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
