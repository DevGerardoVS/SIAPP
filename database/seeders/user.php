<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class user extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::unprepared('INSERT INTO users (`id`,`nombre`,`apellidoP`,`apellidoM`,`username`, `email`, `password`,`telefono`,`remember_token`,`usuario_creacion`,`usuario_modificacion`,`perfil_id`,`estatus`, `created_at`,`updated_at`
        ) VALUES(1,"Admin",NULL,NULL,"admin", "admin@admin.com", "$2y$10$CRJ4b07j75k5qyNeFoI.O.KEkJ7/BtXvb/cux5aM5m2SXFnw.1Dv6",NULL,NULL,"admin",NULL,1,1, NOW(),NULL)');

        DB::unprepared('INSERT INTO users (`id`,`nombre`,`apellidoP`,`apellidoM`,`username`, `email`, `password`,`telefono`,`remember_token`,`usuario_creacion`,`usuario_modificacion`,`perfil_id`,`estatus`, `created_at`,`updated_at`
        ) VALUES(2,"Enrique",NULL,NULL,"user", "isc.enriquegt@gmail.com", "$2y$10$b59CNFkqfaJadnt.IeJYKeaU0nQlffTYsVoJHf7mEu0ctBOjuDlpi",NULL,NULL,"admin",NULL,1,1, NOW(), NULL)');

        
    }
}
