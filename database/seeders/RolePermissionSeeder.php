<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'user-view','user-create','user-edit','user-delete',
            'category-view','category-create','category-edit','category-delete',
            'subcategory-view','subcategory-create','subcategory-edit','subcategory-delete',
            'product-view','product-create','product-edit','product-delete',
            'order-view','order-edit','order-delete',
            'custom-order-view','custom-order-edit',
            'banner-ad-view','banner-ad-edit','banner-ad-delete',
            'popup-ad-view','popup-ad-edit',
            'reel-view','reel-create','reel-edit','reel-delete',
            'social-media-edit'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
