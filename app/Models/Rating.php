<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'review_id',
        'score',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    // Scopes
    public function scopeWithReview($query)
    {
        return $query->whereNotNull('review_id');
    }

    public function scopeWithoutReview($query)
    {
        return $query->whereNull('review_id');
    }
}