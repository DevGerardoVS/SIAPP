<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::unprepared('INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES ("1", "Super Usuario", "web", NOW(), NOW())');
        // DB::unprepared('INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES ("2", "Usuario Municipal Agua", "web", NOW(), NOW())');
        // DB::unprepared('INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES ("3", "Usuario Municipal Predial", "web", NOW(), NOW())');
        // DB::unprepared('INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES ("4", "Control", "web", NOW(), NOW())');
        // DB::unprepared('INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES ("5", "Auditor", "web", NOW(), NOW())');
    
        $role = Role::create(['name'=>'Super Usuario']);
        $role = Role::create(['name'=>'Monitor']);
        $role = Role::create(['name'=>'Analista']);
        $role = Role::create(['name'=>'Control']);
        $role = Role::create(['name'=>'Ventanilla']);
    }
}
