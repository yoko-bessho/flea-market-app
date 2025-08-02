<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ItemCondition;
use Illuminate\Support\Facades\Auth;


class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'brand',
        'condition',
        'price',
        'is_sold',
        'image_path',
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_categories');
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    public function scopeKeywordSearch($query, array $filters)
    {
        if (!empty($filters['keyword'])) {
            $query->where(function($subquery) use ($filters) {
                $subquery->where('title', 'like', '%' . $filters['keyword' ] . '%');
            });
        }

        return $query;
    }

}