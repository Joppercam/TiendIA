<?php

// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);
        
        // Crear un usuario super-admin
        $user = \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@tiendia.com',
            'password' => bcrypt('password'),
        ]);
        
        $user->assignRole('super-admin');
        $user->profile()->create();
    }
}
