<?php

namespace App\Models;

use App\Traits\HasSeo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes, HasSeo;

    protected $fillable = [
        'name', 'slug', 'description', 'logo', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    /**
     * Obtiene todos los productos de la marca.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
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

        // Generamos datos estructurados básicos para la marca
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Brand',
            'name' => $this->name,
            'description' => $this->description ?? '',
            'url' => route('shop.brands.show', $this->slug)
        ];

        // Agregamos logo si existe
        if ($this->logo) {
            $data['logo'] = url(Storage::url($this->logo));
        }

        return $data;
    }
}
