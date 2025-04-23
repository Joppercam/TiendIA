<?php
namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProductService
{
    public function getAllProducts($perPage = 10)
    {
        return Product::with(['category', 'brand', 'images' => function($query) {
            $query->where('is_primary', true);
        }])->paginate($perPage);
    }

    public function getProductById($id)
    {
        return Product::with(['category', 'brand', 'images', 'attributes.values'])->findOrFail($id);
    }

    public function getProductBySlug($slug)
    {
        return Product::with(['category', 'brand', 'images', 'attributes.values'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function createProduct(array $data)
    {
        DB::beginTransaction();

        try {
            // Crear slug
            $data['slug'] = Str::slug($data['name']);
            
            // Crear producto
            $product = Product::create($data);
            
            // Procesar imágenes si existen
            if (isset($data['images']) && count($data['images']) > 0) {
                $this->processProductImages($product, $data['images']);
            }
            
            // Procesar atributos si existen
            if (isset($data['attributes']) && count($data['attributes']) > 0) {
                $this->processProductAttributes($product, $data['attributes']);
            }
            
            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updateProduct($id, array $data)
    {
        DB::beginTransaction();

        try {
            $product = Product::findOrFail($id);
            
            // Actualizar slug si cambió el nombre
            if (isset($data['name'])) {
                $data['slug'] = Str::slug($data['name']);
            }
            
            // Actualizar producto
            $product->update($data);
            
            // Procesar imágenes si existen
            if (isset($data['images']) && count($data['images']) > 0) {
                $this->processProductImages($product, $data['images']);
            }
            
            // Procesar atributos si existen
            if (isset($data['attributes']) && count($data['attributes']) > 0) {
                // Eliminar atributos existentes
                $product->attributes()->detach();
                $this->processProductAttributes($product, $data['attributes']);
            }
            
            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        
        // Eliminar imágenes del producto
        foreach ($product->images as $image) {
            Storage::delete('public/products/' . $image->image);
            $image->delete();
        }
        
        return $product->delete();
    }

    protected function processProductImages(Product $product, array $images)
    {
        $order = 0;
        
        foreach ($images as $index => $imageData) {
            if (isset($imageData['file']) && $imageData['file']) {
                $file = $imageData['file'];
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                
                // Guardar imagen original
                $file->storeAs('public/products', $fileName);
                
                // Crear miniatura
                $thumbnail = Image::make($file)
                    ->resize(300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                
                Storage::put('public/products/thumbnails/' . $fileName, $thumbnail->stream());
                
                $isPrimary = isset($imageData['is_primary']) ? $imageData['is_primary'] : ($index === 0);
                
                // Si esta imagen es primaria, desmarcar otras primarias
                if ($isPrimary) {
                    ProductImage::where('product_id', $product->id)
                        ->where('is_primary', true)
                        ->update(['is_primary' => false]);
                }
                
                // Guardar registro de imagen
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $fileName,
                    'is_primary' => $isPrimary,
                    'order' => $order++,
                    'alt_text' => $imageData['alt_text'] ?? $product->name
                ]);
            }
        }
    }

    protected function processProductAttributes(Product $product, array $attributes)
    {
        foreach ($attributes as $attributeId => $data) {
            if (isset($data['attribute_value_id'])) {
                // Atributo con valor predefinido
                $product->attributes()->attach($attributeId, [
                    'attribute_value_id' => $data['attribute_value_id'],
                    'custom_value' => null
                ]);
            } elseif (isset($data['custom_value'])) {
                // Atributo con valor personalizado
                $product->attributes()->attach($attributeId, [
                    'attribute_value_id' => null,
                    'custom_value' => $data['custom_value']
                ]);
            }
        }
    }
}