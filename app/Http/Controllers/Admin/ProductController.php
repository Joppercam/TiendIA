<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $products = Product::with(['category', 'brand'])->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $attributes = Attribute::with('values')->get();
        
        return view('admin.products.create', compact('categories', 'brands', 'attributes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'special_price' => 'nullable|numeric|min:0',
            'special_price_from' => 'nullable|date',
            'special_price_to' => 'nullable|date',
            'sku' => 'required|string|unique:products',
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,active,inactive',
            'featured' => 'boolean',
            'quantity' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'images' => 'nullable|array',
            'images.*.file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*.alt_text' => 'nullable|string|max:255',
            'images.*.is_primary' => 'boolean',
            'attributes' => 'nullable|array',
        ]);

        try {
            $product = $this->productService->createProduct($request->all());
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Producto creado correctamente');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Error al crear el producto: ' . $e->getMessage()]);
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'images', 'attributes.values']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['category', 'brand', 'images', 'attributes']);
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $attributes = Attribute::with('values')->get();
        
        return view('admin.products.edit', compact('product', 'categories', 'brands', 'attributes'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'special_price' => 'nullable|numeric|min:0',
            'special_price_from' => 'nullable|date',
            'special_price_to' => 'nullable|date',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,active,inactive',
            'featured' => 'boolean',
            'quantity' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'images' => 'nullable|array',
            'images.*.file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*.alt_text' => 'nullable|string|max:255',
            'images.*.is_primary' => 'boolean',
            'attributes' => 'nullable|array',
        ]);

        try {
            $product = $this->productService->updateProduct($product->id, $request->all());
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Producto actualizado correctamente');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Error al actualizar el producto: ' . $e->getMessage()]);
        }
    }

    public function destroy(Product $product)
    {
        try {
            $this->productService->deleteProduct($product->id);
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Producto eliminado correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar el producto: ' . $e->getMessage()]);
        }
    }
}
