<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
        'postal_code',
        'address',
        'building',
        'uuid',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'id',
    ];


    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sellItems()
    {
        return $this->hasMany(Item::class, 'user_id');
    }

    public function likes()
    {
        return $this->belongsToMany(Item::class, 'likes');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function buyItems()
    {
        return $this->hasMany(Purchase::class, 'buyer_id');
    }
}
