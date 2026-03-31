<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'name',
        'email',
        'password',
        'mobile',
        'dob',
        'gender',
        'status',
        'wallet1',
        'wallet2',
        'wallet3'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    public function business()
    {
        return $this->hasOne(Business::class);
    }

    // Optional (if sponsor system)
    // public function sponsoredBusinesses()
    // {
    //     return $this->hasMany(Business::class, 'sponsor_id');
    // }
}
