<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomNotification extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'category_id',
        'subcategory_id',
        'product_id',
        'state',
        'city',
        'customer_id',
    ];
protected $casts = [
    'customer_id' => 'array',
];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function subcategory() {
        return $this->belongsTo(Subcategory::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function getImageUrlAttribute() {
        return $this->image
            ? asset('uploads/notifications/'.$this->image)
            : null;
    }
}
