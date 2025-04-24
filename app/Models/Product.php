<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @OA\Schema(
 *     schema="Product",
 *     title="Product",
 *     description="Modelo de producto para la tienda",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Smartphone XYZ"),
 *     @OA\Property(property="description", type="string", example="Smartphone de última generación"),
 *     @OA\Property(property="price", type="number", format="float", example=599.99),
 *     @OA\Property(property="stock", type="integer", example=10),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="brand_id", type="integer", example=2),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'short_description', 'price',
        'special_price', 'special_price_from', 'special_price_to',
        'sku', 'brand_id', 'category_id', 'status', 'featured',
        'quantity', 'weight', 'meta_data'
    ];

    protected $casts = [
        'meta_data' => 'array',
        'special_price_from' => 'date',
        'special_price_to' => 'date',
        'featured' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class)
            ->withPivot('attribute_value_id', 'custom_value')
            ->withTimestamps();
    }

    public function getPrimaryImageAttribute()
    {
        return $this->images()->where('is_primary', true)->first();
    }

    public function getCurrentPriceAttribute()
    {
        if ($this->special_price && $this->isSpecialPriceValid()) {
            return $this->special_price;
        }
        return $this->price;
    }

    public  function isSpecialPriceValid()
    {
        $now = now();
        $validFrom = $this->special_price_from ? $this->special_price_from <= $now : true;
        $validTo = $this->special_price_to ? $this->special_price_to >= $now : true;
        
        return $validFrom && $validTo;
    }

    // En app/Models/Product.php añadir estas relaciones

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    // Métodos útiles para calcular valoraciones medias
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('score') ?: 0;
    }

    public function getRatingCountAttribute()
    {
        return $this->ratings()->count();
    }

    public function getReviewCountAttribute()
    {
        return $this->reviews()->approved()->count();
    }

    public function getQuestionsCountAttribute()
    {
        return $this->questions()->approved()->count();
    }
}
