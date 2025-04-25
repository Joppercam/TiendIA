<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ImageService
{
    public function saveProductImage(UploadedFile $image, int $productId): string
    {
        $filename = $productId . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        
        // Guardar imagen original
        $originalPath = storage_path('app/public/products/original');
        $image->move($originalPath, $filename);
        
        // Crear miniatura
        $thumbnailPath = storage_path('app/public/products/thumbnails');
        $img = Image::make($originalPath . '/' . $filename);
        $img->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        
        if (!file_exists($thumbnailPath)) {
            mkdir($thumbnailPath, 0755, true);
        }
        
        $img->save($thumbnailPath . '/' . $filename);
        
        return $filename;
    }
    
    public function deleteProductImage(string $filename): bool
    {
        try {
            Storage::disk('products')->delete('original/' . $filename);
            Storage::disk('products')->delete('thumbnails/' . $filename);
            return true;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }
    
    // Más métodos para otros tipos de imágenes...
}