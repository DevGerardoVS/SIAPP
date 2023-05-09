<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
class asignacionderoles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {  
        $users = User::all();
        $num = sizeof($users);

        foreach ($users as $useridd) {
            $obj_user = User::where('id',$useridd->id)->firstOrFail();
            $rolId = $obj_user->perfil_id;
            $rol = Role::where('id','=',$rolId)->first();
            $rolType = $rol->name;
            if(!$obj_user->hasRole($rolType)){
                $obj_user->syncRoles([$rolType]);
            }
        }
    }    
}
