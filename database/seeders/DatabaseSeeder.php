<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            user::class,
            ModulosSeeder::class,
            FuncionesSeeder::class,
            PermissionsSeeder::class,
            PerfilesSeeder::class,
            RolesSeeder::class,
            RoleHasPermissionsSeeder::class,
            ConfiguracionSeeder::class,
            asignacionderoles::class, 
            cat_aseguradoras::class, 
            ConcesionesBloqueadasSeeder::class,
            ConfiguracionSAPSeeder::class,
        ]);
    }
}
