<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;

class SearchService
{
    public function search($query, $options = [])
    {
        $productsQuery = Product::query()->with(['category', 'brand', 'images' => function($q) {
            $q->where('is_primary', true);
        }]);
        
        // Búsqueda por texto
        if ($query) {
            $productsQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('short_description', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            });
        }
        
        // Filtrar por categoría
        if (isset($options['category_id']) && $options['category_id']) {
            $category = Category::find($options['category_id']);
            
            if ($category) {
                // Incluir todas las subcategorías
                $categoryIds = [$category->id];
                $this->addChildCategoryIds($category, $categoryIds);
                
                $productsQuery->whereIn('category_id', $categoryIds);
            }
        }
        
        // Filtrar por marca
        if (isset($options['brand_id']) && $options['brand_id']) {
            $productsQuery->where('brand_id', $options['brand_id']);
        }
        
        // Filtrar por precio
        if (isset($options['price_min'])) {
            $productsQuery->where(function($q) use ($options) {
                $q->where('price', '>=', $options['price_min'])
                  ->orWhere(function($sq) use ($options) {
                      $sq->whereNotNull('special_price')
                         ->where('special_price', '>=', $options['price_min']);
                  });
            });
        }
        
        if (isset($options['price_max'])) {
            $productsQuery->where(function($q) use ($options) {
                $q->where('price', '<=', $options['price_max'])
                  ->orWhere(function($sq) use ($options) {
                      $sq->whereNotNull('special_price')
                         ->where('special_price', '<=', $options['price_max']);
                  });
            });
        }
        
        // Filtrar por atributos
        if (isset($options['attributes']) && is_array($options['attributes'])) {
            foreach ($options['attributes'] as $attributeId => $valueIds) {
                if (!empty($valueIds)) {
                    $productsQuery->whereHas('attributes', function($q) use ($attributeId, $valueIds) {
                        $q->where('attributes.id', $attributeId)
                          ->whereIn('attribute_value_id', (array) $valueIds);
                    });
                }
            }
        }
        
        // Ordenar productos
        if (isset($options['sort_by'])) {
            switch ($options['sort_by']) {
                case 'price_asc':
                    $productsQuery->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $productsQuery->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $productsQuery->orderBy('created_at', 'desc');
                    break;
                case 'name_asc':
                    $productsQuery->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $productsQuery->orderBy('name', 'desc');
                    break;
                default:
                    $productsQuery->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $productsQuery->orderBy('created_at', 'desc');
        }
        
        // Productos activos por defecto
        if (!isset($options['include_inactive']) || !$options['include_inactive']) {
            $productsQuery->where('status', 'active');
        }
        
        // Paginación
        $perPage = $options['per_page'] ?? 12;
        
        return $productsQuery->paginate($perPage);
    }
    
    public function getAvailableFilters($categoryId = null)
    {
        $filters = [
            'categories' => Category::where('is_active', true)->get(),
            'brands' => Brand::where('is_active', true)->get(),
            'price' => [
                'min' => Product::where('status', 'active')->min('price'),
                'max' => Product::where('status', 'active')->max('price')
            ]
        ];
        
        // Atributos filtrables
        $filterableAttributes = Attribute::where('is_filterable', true)
            ->with('values')
            ->get();
            
        if ($filterableAttributes->count() > 0) {
            $filters['attributes'] = $filterableAttributes;
        }
        
        return $filters;
    }
    
    protected function addChildCategoryIds(Category $category, &$categoryIds)
    {
        foreach ($category->children as $child) {
            $categoryIds[] = $child->id;
            $this->addChildCategoryIds($child, $categoryIds);
        }
    }
}