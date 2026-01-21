<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasApiTokens;
    use HasRoles;
    protected $table = 'customers';
    protected $appends = ['image_url'];

    // 🔥 REQUIRED for Spatie + Sanctum
  
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
    'name',
    'email',
    'password',
    'admin',
    'state',
    'city',
    'number',
    'image',
    'otp',
    'status',
    'device_token',
    'device_key',
    'fcm_token',
    'token',
    'role',
    'device_type',
    'company_name',
    'category_ids',
];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        return asset('uploads/customer/' . $this->image);
    }
}
