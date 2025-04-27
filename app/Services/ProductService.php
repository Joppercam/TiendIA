<?php
namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

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


    // -------------------------------------------------------------------

    protected function processProductImages(Product $product, array $images)
    {
        $imageManager = new ImageManager(new Driver());
        $order = 0;

        foreach ($images as $index => $imageData) {
            if (isset($imageData['file']) && $imageData['file'] instanceof UploadedFile && $imageData['file']->isValid()) {
                $file = $imageData['file'];
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();

                try {
                    // 1. Leer la imagen subida DIRECTAMENTE para procesarla
                    Log::info("Leyendo contenido del archivo subido: " . $file->getClientOriginalName());
                    $image = $imageManager->read($file); // Lee directamente del archivo temporal subido

                    // 2. Guardar la imagen original en el disco 'public'
                    // Usamos el contenido codificado de la imagen leída para asegurar consistencia
                    // Storage::disk('public')->put(...) guarda relativo a storage/app/public
                    Storage::disk('public')->put('products/' . $fileName, (string) $image->encode());
                    Log::info("Archivo original guardado en: storage/app/public/products/" . $fileName);

                    // 3. Crear y guardar la miniatura en el disco 'public'
                    $thumbnailImage = clone $image; // Clonamos la imagen ya leída
                    $thumbnailImage->scale(width: 300);
                    $thumbnailContent = $thumbnailImage->encode();
                    Storage::disk('public')->put('products/thumbnails/' . $fileName, (string) $thumbnailContent);
                    Log::info("Miniatura creada y guardada en: storage/app/public/products/thumbnails/" . $fileName);

                    // 4. Guardar registro en la base de datos
                    $isPrimary = isset($imageData['is_primary']) ? (bool)$imageData['is_primary'] : ($index === 0);
                    if ($isPrimary) {
                        ProductImage::where('product_id', $product->id)
                            ->where('is_primary', true)
                            ->update(['is_primary' => false]);
                    }
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $fileName, // Solo el nombre del archivo
                        'is_primary' => $isPrimary,
                        'order' => $order++,
                        'alt_text' => $imageData['alt_text'] ?? $product->name
                    ]);

                } catch (\Intervention\Image\Exceptions\DecoderException $e) {
                    Log::error("Error al DECODIFICAR imagen {$fileName}: " . $e->getMessage());
                    // Saltar esta imagen si no se puede decodificar
                    continue;
                } catch (\Exception $e) {
                    Log::error("Error procesando imagen {$fileName}: " . $e->getMessage());
                    Log::error($e->getTraceAsString());
                    // Saltar esta imagen si ocurre otro error
                    continue;
                }
            } elseif (isset($imageData['file']) && $imageData['file']) {
                Log::warning("Archivo de imagen inválido o no cargado correctamente para producto ID {$product->id}, índice {$index}");
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