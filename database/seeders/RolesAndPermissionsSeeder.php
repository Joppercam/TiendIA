<?php

// database/seeders/RolesAndPermissionsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Resetear roles y permisos en caché
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Permisos de usuarios
            'view users',
            'manage users',
            
            // Permisos de productos
            'view products',
            'create products',
            'edit products',
            'delete products',
            
            // Permisos de categorías
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            // Permisos de pedidos
            'view orders',
            'manage orders',
            
            // Permisos de inventario
            'view inventory',
            'manage inventory',
            
            // Permisos del panel admin
            'access admin panel',
            'view reports',
            
            // Otros permisos
            'manage promotions',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles y asignar permisos
        
        // Role Super Admin - todos los permisos
        $roleSuperAdmin = Role::create(['name' => 'super-admin']);
        $roleSuperAdmin->givePermissionTo(Permission::all());

        // Role Admin - la mayoría de permisos excepto algunos críticos
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleAdmin->givePermissionTo([
            'view users',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            'view orders',
            'manage orders',
            'view inventory',
            'manage inventory',
            'access admin panel',
            'view reports',
            'manage promotions',
        ]);

        // Role Editor - puede gestionar productos y contenido
        $roleEditor = Role::create(['name' => 'editor']);
        $roleEditor->givePermissionTo([
            'view products',
            'create products',
            'edit products',
            'view categories',
            'create categories',
            'edit categories',
            'access admin panel',
        ]);

        // Role Vendedor - gestión de pedidos
        $roleSeller = Role::create(['name' => 'seller']);
        $roleSeller->givePermissionTo([
            'view products',
            'view orders',
            'manage orders',
            'view inventory',
            'access admin panel',
        ]);

        // Role Cliente - permisos básicos (se asignará automáticamente al registrarse)
        Role::create(['name' => 'customer']);
    }
}