<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $appends = ['image_url'];
    protected $fillable = [
        'name',
        'category_id',
        'subcategory_id',
        'number',
        'size',
        'hole_size',
        'gross_weight',
        'less_weight',
        'weight',
        'quantity',
        'gallery',
        'label_product',
        'color',
        'charge',
        'bg_color',
        'order_confirm'
    ];

    protected $casts = [
        'gallery' => 'array',
        'order_confirm' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }
    public function getImageUrlAttribute()
    {
        if (empty($this->gallery) || !is_array($this->gallery)) {
            return [];
        }

        return array_map(function ($image) {
            return asset('uploads/products/' . $image);
        }, $this->gallery);
    }
}
