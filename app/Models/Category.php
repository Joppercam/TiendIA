<?php

namespace App\Models;

use App\Traits\HasSeo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'parent_id', 'is_active', 'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

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

        // Generamos datos estructurados básicos para la categoría
        // En este caso, usamos BreadcrumbList para representar la jerarquía de categorías
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => []
        ];

        $position = 1;
        
        // Agregamos inicio
        $data['itemListElement'][] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Inicio',
            'item' => url('/')
        ];

        // Si tiene padre, agregamos la jerarquía
        if ($this->parent) {
            $data['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $this->parent->name,
                'item' => route('shop.categories.show', $this->parent->slug)
            ];
        }

        // Agregamos la categoría actual
        $data['itemListElement'][] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $this->name,
            'item' => route('shop.categories.show', $this->slug)
        ];

        return $data;
    }
}
