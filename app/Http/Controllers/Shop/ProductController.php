<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Services\SearchService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(Request $request)
    {
        $searchQuery = $request->input('q');
        $categoryId = $request->input('category');
        $brandId = $request->input('brand');
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');
        $sortBy = $request->input('sort_by', 'newest');
        $attributes = $request->input('attributes', []);
        
        $searchOptions = [
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'price_min' => $priceMin,
            'price_max' => $priceMax,
            'sort_by' => $sortBy,
            'attributes' => $attributes,
            'per_page' => 12
        ];
        
        $products = $this->searchService->search($searchQuery, $searchOptions);
        $filters = $this->searchService->getAvailableFilters($categoryId);
        $currentCategory = $categoryId ? Category::find($categoryId) : null;
        
        return view('shop.products.index', compact(
            'products', 
            'filters', 
            'searchQuery', 
            'currentCategory', 
            'sortBy',
            'priceMin',
            'priceMax'
        ));
    }

    public function show($slug)
    {
        $product = Product::with([
                'category', 
                'brand', 
                'images', 
                'attributes.values'
            ])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();
            
        // Obtener productos relacionados por la misma categoría
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->inRandomOrder()
            ->limit(4)
            ->get();
            
        return view('shop.products.show', compact('product', 'relatedProducts'));
    }

    public function byCategory($categorySlug)
    {
        $category = Category::where('slug', $categorySlug)
            ->where('is_active', true)
            ->firstOrFail();
            
        // Obtener IDs de subcategorías
        $categoryIds = [$category->id];
        $this->getChildCategoryIds($category, $categoryIds);
        
        $products = Product::whereIn('category_id', $categoryIds)
            ->where('status', 'active')
            ->paginate(12);
            
        $filters = $this->searchService->getAvailableFilters($category->id);
        
        return view('shop.products.category', compact('category', 'products', 'filters'));
    }

    public function byBrand($brandSlug)
    {
        $brand = Brand::where('slug', $brandSlug)
            ->where('is_active', true)
            ->firstOrFail();
            
        $products = Product::where('brand_id', $brand->id)
            ->where('status', 'active')
            ->paginate(12);
            
        return view('shop.products.brand', compact('brand', 'products'));
    }

    private function getChildCategoryIds($category, &$categoryIds)
    {
        foreach ($category->children as $child) {
            $categoryIds[] = $child->id;
            $this->getChildCategoryIds($child, $categoryIds);
        }
    }
}
