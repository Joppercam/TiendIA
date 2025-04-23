<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductImage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Productos de ejemplo
        $products = [
            [
                'name' => 'iPhone 13 Pro',
                'short_description' => 'El iPhone más avanzado con una experiencia Pro',
                'description' => 'El iPhone 13 Pro presenta una pantalla Super Retina XDR con ProMotion que brinda una experiencia de respuesta más rápida y fluida. El sistema de cámara Pro ofrece nuevas posibilidades fotográficas con mejoras en cada una de las tres cámaras.',
                'price' => 999.00,
                'special_price' => 899.00,
                'special_price_from' => now(),
                'special_price_to' => now()->addMonths(1),
                'sku' => 'APPL-IP13-PRO',
                'category_name' => 'Smartphones',
                'brand_name' => 'Apple',
                'status' => 'active',
                'featured' => true,
                'quantity' => 50,
                'weight' => 0.240,
                'attributes' => [
                    'color' => 'Negro',
                    'storage' => '256GB',
                    'ram' => '6GB',
                    'processor' => 'Apple A15 Bionic'
                ]
            ],
            [
                'name' => 'Samsung Galaxy S21',
                'short_description' => 'Cámara de nivel profesional y rendimiento excepcional',
                'description' => 'El Galaxy S21 está diseñado para revolucionar la videografía y la fotografía con su sistema de cámara profesional. Capture videos cinematográficos de 8K y fotos épicas hasta 64 MP. Todo alimentado por el procesador más rápido de Galaxy.',
                'price' => 799.00,
                'special_price' => null,
                'special_price_from' => null,
                'special_price_to' => null,
                'sku' => 'SAMS-S21-BLK',
                'category_name' => 'Smartphones',
                'brand_name' => 'Samsung',
                'status' => 'active',
                'featured' => true,
                'quantity' => 35,
                'weight' => 0.171,
                'attributes' => [
                    'color' => 'Negro',
                    'storage' => '128GB',
                    'ram' => '8GB',
                    'processor' => 'Exynos 2100'
                ]
            ],
            [
                'name' => 'MacBook Pro 14"',
                'short_description' => 'Potencia y rendimiento revolucionarios para profesionales',
                'description' => 'El MacBook Pro lleva el rendimiento y la portabilidad a un nivel superior. El chip M1 Pro revoluciona significativamente el rendimiento de la CPU, GPU y aprendizaje automático, brindando hasta 17 horas de duración de la batería.',
                'price' => 1999.00,
                'special_price' => null,
                'special_price_from' => null,
                'special_price_to' => null,
                'sku' => 'APPL-MP14-SLV',
                'category_name' => 'Laptops',
                'brand_name' => 'Apple',
                'status' => 'active',
                'featured' => true,
                'quantity' => 20,
                'weight' => 1.600,
                'attributes' => [
                    'color' => 'Gris',
                    'storage' => '512GB',
                    'ram' => '16GB',
                    'processor' => 'Apple M1 Pro'
                ]
            ],
            [
                'name' => 'Sony WH-1000XM4',
                'short_description' => 'Auriculares inalámbricos con cancelación de ruido',
                'description' => 'Los auriculares WH-1000XM4 ofrecen la mejor tecnología de cancelación de ruido de su clase, sonido de alta calidad y llamadas manos libres óptimas. Disfruta de hasta 30 horas de duración de la batería y carga rápida.',
                'price' => 349.00,
                'special_price' => 299.00,
                'special_price_from' => now(),
                'special_price_to' => now()->addWeeks(2),
                'sku' => 'SNY-WH1000-BLK',
                'category_name' => 'Accesorios',
                'brand_name' => 'Sony',
                'status' => 'active',
                'featured' => false,
                'quantity' => 45,
                'weight' => 0.254,
                'attributes' => [
                    'color' => 'Negro',
                    'material' => 'Plástico'
                ]
            ],
            [
                'name' => 'Nike Air Zoom Pegasus 38',
                'short_description' => 'Zapatillas de running para hombre',
                'description' => 'Las zapatillas Nike Air Zoom Pegasus 38 ofrecen un ajuste más personalizable, con la malla transpirable en la parte superior. La espuma Nike React es suave y receptiva, y la unidad Zoom Air proporciona más impulso en cada paso.',
                'price' => 120.00,
                'special_price' => null,
                'special_price_from' => null,
                'special_price_to' => null,
                'sku' => 'NIKE-AZP38-BLK',
                'category_name' => 'Hombre',
                'brand_name' => 'Nike',
                'status' => 'active',
                'featured' => false,
                'quantity' => 30,
                'weight' => 0.285,
                'attributes' => [
                    'color' => 'Negro',
                    'tamaño' => 'M',
                    'material' => 'Sintético'
                ]
            ],
            [
                'name' => 'IKEA BILLY Librería',
                'short_description' => 'Librería versátil para cualquier espacio',
                'description' => 'La librería BILLY es un clásico versátil que puede adaptarse a tu espacio y necesidades. Sus baldas ajustables te permiten personalizar el almacenaje y adaptarse a diferentes tamaños de objetos. Puedes combinar varias unidades para crear una solución a medida.',
                'price' => 69.99,
                'special_price' => null,
                'special_price_from' => null,
                'special_price_to' => null,
                'sku' => 'IKEA-BILLY-WHT',
                'category_name' => 'Muebles',
                'brand_name' => 'IKEA',
                'status' => 'active',
                'featured' => false,
                'quantity' => 25,
                'weight' => 29.00,
                'attributes' => [
                    'color' => 'Blanco',
                    'material' => 'Madera'
                ]
            ],
        ];

        foreach ($products as $productData) {
            // Obtener la categoría y marca
            $category = Category::where('name', $productData['category_name'])->first();
            $brand = Brand::where('name', $productData['brand_name'])->first();
            
            if (!$category || !$brand) {
                continue;
            }
            
            // Crear el producto
            $product = Product::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'short_description' => $productData['short_description'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'special_price' => $productData['special_price'],
                'special_price_from' => $productData['special_price_from'],
                'special_price_to' => $productData['special_price_to'],
                'sku' => $productData['sku'],
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'status' => $productData['status'],
                'featured' => $productData['featured'],
                'quantity' => $productData['quantity'],
                'weight' => $productData['weight'],
            ]);
            
            // Asignar atributos
            foreach ($productData['attributes'] as $attributeName => $value) {
                $attribute = Attribute::where('code', strtolower($attributeName))->first();
                
                if (!$attribute) {
                    continue;
                }
                
                if (in_array($attribute->type, ['select', 'radio', 'checkbox'])) {
                    $attributeValue = AttributeValue::where('attribute_id', $attribute->id)
                        ->where('value', $value)
                        ->first();
                    
                    if ($attributeValue) {
                        $product->attributes()->attach($attribute->id, [
                            'attribute_value_id' => $attributeValue->id,
                            'custom_value' => null
                        ]);
                    }
                } else {
                    $product->attributes()->attach($attribute->id, [
                        'attribute_value_id' => null,
                        'custom_value' => $value
                    ]);
                }
            }
            
            // Crear una imagen de prueba para el producto
            ProductImage::create([
                'product_id' => $product->id,
                'image' => 'placeholder.jpg', // Imagen de marcador de posición
                'is_primary' => true,
                'order' => 0,
                'alt_text' => $product->name
            ]);
        }
    }
}
