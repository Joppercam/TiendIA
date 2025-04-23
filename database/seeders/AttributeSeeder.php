<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Models\AttributeValue;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            [
                'name' => 'Color',
                'code' => 'color',
                'type' => 'select',
                'is_filterable' => true,
                'is_required' => true,
                'values' => ['Negro', 'Blanco', 'Rojo', 'Azul', 'Verde', 'Amarillo', 'Gris', 'Morado', 'Rosa', 'Marrón']
            ],
            [
                'name' => 'Tamaño',
                'code' => 'size',
                'type' => 'select',
                'is_filterable' => true,
                'is_required' => true,
                'values' => ['XS', 'S', 'M', 'L', 'XL', 'XXL']
            ],
            [
                'name' => 'Material',
                'code' => 'material',
                'type' => 'select',
                'is_filterable' => true,
                'is_required' => false,
                'values' => ['Algodón', 'Poliéster', 'Cuero', 'Metal', 'Madera', 'Plástico', 'Vidrio', 'Cerámica']
            ],
            [
                'name' => 'Capacidad de Almacenamiento',
                'code' => 'storage',
                'type' => 'select',
                'is_filterable' => true,
                'is_required' => false,
                'values' => ['16GB', '32GB', '64GB', '128GB', '256GB', '512GB', '1TB']
            ],
            [
                'name' => 'RAM',
                'code' => 'ram',
                'type' => 'select',
                'is_filterable' => true,
                'is_required' => false,
                'values' => ['2GB', '4GB', '6GB', '8GB', '12GB', '16GB', '32GB']
            ],
            [
                'name' => 'Procesador',
                'code' => 'processor',
                'type' => 'text',
                'is_filterable' => false,
                'is_required' => false,
                'values' => []
            ],
        ];

        foreach ($attributes as $attribute) {
            $values = $attribute['values'];
            unset($attribute['values']);
            
            $attributeModel = Attribute::create($attribute);
            
            foreach ($values as $value) {
                AttributeValue::create([
                    'attribute_id' => $attributeModel->id,
                    'value' => $value
                ]);
            }
        }
    }
}
