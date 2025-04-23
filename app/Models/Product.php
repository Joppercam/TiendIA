<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
