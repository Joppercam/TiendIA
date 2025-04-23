<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Apple',
                'description' => 'Productos premium de tecnología e innovación',
                'is_active' => true,
            ],
            [
                'name' => 'Samsung',
                'description' => 'Electrónica y tecnología de alta calidad',
                'is_active' => true,
            ],
            [
                'name' => 'Nike',
                'description' => 'Productos deportivos de calidad superior',
                'is_active' => true,
            ],
            [
                'name' => 'Adidas',
                'description' => 'Equipamiento deportivo innovador',
                'is_active' => true,
            ],
            [
                'name' => 'IKEA',
                'description' => 'Muebles y accesorios para el hogar',
                'is_active' => true,
            ],
            [
                'name' => 'Sony',
                'description' => 'Productos electrónicos y de entretenimiento',
                'is_active' => true,
            ],
            [
                'name' => 'LG',
                'description' => 'Electrodomésticos y electrónica de consumo',
                'is_active' => true,
            ],
        ];

        foreach ($brands as $brand) {
            $brand['slug'] = Str::slug($brand['name']);
            Brand::create($brand);
        }
    }
}
