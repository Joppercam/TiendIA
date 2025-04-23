<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electrónica',
                'description' => 'Productos electrónicos de alta calidad',
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Smartphones',
                        'description' => 'Teléfonos inteligentes de diversas marcas',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Tablets',
                        'description' => 'Tablets y accesorios',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Laptops',
                        'description' => 'Computadoras portátiles para todo uso',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Accesorios',
                        'description' => 'Accesorios electrónicos diversos',
                        'is_active' => true,
                    ],
                ]
            ],
            [
                'name' => 'Ropa',
                'description' => 'Prendas de vestir para toda ocasión',
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Hombre',
                        'description' => 'Ropa para caballeros',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Mujer',
                        'description' => 'Ropa para damas',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Niños',
                        'description' => 'Ropa para niños y bebés',
                        'is_active' => true,
                    ],
                ]
            ],
            [
                'name' => 'Hogar',
                'description' => 'Artículos para el hogar',
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Muebles',
                        'description' => 'Mobiliario para el hogar',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Decoración',
                        'description' => 'Artículos decorativos',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Cocina',
                        'description' => 'Utensilios y electrodomésticos de cocina',
                        'is_active' => true,
                    ],
                ]
            ],
        ];

        foreach ($categories as $category) {
            $children = $category['children'] ?? [];
            unset($category['children']);
            
            $category['slug'] = Str::slug($category['name']);
            $parent = Category::create($category);
            
            foreach ($children as $child) {
                $child['slug'] = Str::slug($child['name']);
                $child['parent_id'] = $parent->id;
                Category::create($child);
            }
        }
    }
}
