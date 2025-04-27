<?php

namespace App\Models;

use App\Traits\HasSeo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;


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
    use HasFactory, SoftDeletes, HasSeo;

    protected $fillable = [
        'name', 
        'slug', 
        'description', 
        'short_description', 
        'price',
        'special_price', 
        'special_price_from', 
        'special_price_to',
        'sku', 
        'brand_id', 
        'category_id', 
        'status', 
        'featured',
        'quantity', 
        'weight', 
        'meta_data'
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

    /**
     * Determina si un producto tiene un precio especial activo.
     *
     * @return bool
     */
    public function hasValidSpecialPrice()
    {
        if (!$this->special_price) {
            return false;
        }

        $now = now();
        $from = $this->special_price_from;
        $to = $this->special_price_to;

        if ($from && $now->lt($from)) {
            return false;
        }

        if ($to && $now->gt($to)) {
            return false;
        }

        return true;
    }


    /**
     * Obtiene el precio actual del producto considerando descuentos.
     *
     * @return float
     */
    public function getCurrentPrice()
    {
        return $this->hasValidSpecialPrice() ? $this->special_price : $this->price;
    }

    /**
     * Obtiene datos estructurados para SEO.
     * 
     * @return array
     */
    public function getStructuredData()
    {
        // Si hay datos personalizados, los usamos
        if ($this->seoMetadata && $this->seoMetadata->structured_data) {
            return $this->seoMetadata->structured_data;
        }

        // Generamos datos estructurados básicos para el producto
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $this->name,
            'description' => $this->short_description ?? substr(strip_tags($this->description), 0, 160),
            'sku' => $this->sku,
            'image' => $this->images()->where('is_primary', true)->first() 
                ? url(Storage::url($this->images()->where('is_primary', true)->first()->image))
                : ($this->images()->first() ? url(Storage::url($this->images()->first()->image)) : null),
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'CLP', // Ajustar según la moneda de la tienda
                'price' => $this->getCurrentPrice(),
                'availability' => $this->quantity > 0 
                    ? 'https://schema.org/InStock' 
                    : 'https://schema.org/OutOfStock',
                'url' => route('shop.products.show', $this->slug)
            ]
        ];

        // Agregamos marca si existe
        if ($this->brand) {
            $data['brand'] = [
                '@type' => 'Brand',
                'name' => $this->brand->name
            ];
        }

        // Agregamos categoría si existe
        if ($this->category) {
            $data['category'] = $this->category->name;
        }

        // Agregamos valoraciones si existen
        if ($this->ratings()->count() > 0) {
            $avgRating = $this->ratings()->avg('rating');
            $reviewCount = $this->ratings()->count();
            
            $data['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => number_format($avgRating, 1),
                'reviewCount' => $reviewCount
            ];
        }
        

        return $data;
    }

    // --- AÑADE ESTE MÉTODO ---
    /**
     * Get the inventory record associated with the product.
     */
    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }
}
