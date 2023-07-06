<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TipoActividadUppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::unprepared("INSERT INTO tipo_actividad_upp (clv_upp,Acumulativa,Continua,Especial,created_user,created_at,updated_user,updated_at,deleted_user,deleted_at) values
            ('001',1,0,0,'SEEDER',now(),null,null,null,null),
            ('002',1,0,0,'SEEDER',now(),null,null,null,null),
            ('003',1,0,0,'SEEDER',now(),null,null,null,null),
            ('006',1,0,0,'SEEDER',now(),null,null,null,null),
            ('007',1,0,0,'SEEDER',now(),null,null,null,null),
            ('008',1,0,0,'SEEDER',now(),null,null,null,null),
            ('009',1,0,0,'SEEDER',now(),null,null,null,null),
            ('010',1,0,0,'SEEDER',now(),null,null,null,null),
            ('011',1,0,0,'SEEDER',now(),null,null,null,null),
            ('012',0,1,1,'SEEDER',now(),null,null,null,null),
            ('014',1,0,0,'SEEDER',now(),null,null,null,null),
            ('016',1,0,0,'SEEDER',now(),null,null,null,null),
            ('017',0,1,1,'SEEDER',now(),null,null,null,null),
            ('019',1,0,0,'SEEDER',now(),null,null,null,null),
            ('020',1,0,0,'SEEDER',now(),null,null,null,null),
            ('021',1,0,0,'SEEDER',now(),null,null,null,null),
            ('022',1,0,0,'SEEDER',now(),null,null,null,null),
            ('023',1,0,0,'SEEDER',now(),null,null,null,null),
            ('024',1,0,0,'SEEDER',now(),null,null,null,null),
            ('025',1,0,0,'SEEDER',now(),null,null,null,null),
            ('031',1,0,0,'SEEDER',now(),null,null,null,null),
            ('032',1,0,0,'SEEDER',now(),null,null,null,null),
            ('033',1,0,0,'SEEDER',now(),null,null,null,null),
            ('035',1,0,0,'SEEDER',now(),null,null,null,null),
            ('036',1,0,0,'SEEDER',now(),null,null,null,null),
            ('037',1,0,0,'SEEDER',now(),null,null,null,null),
            ('038',1,0,0,'SEEDER',now(),null,null,null,null),
            ('040',1,0,0,'SEEDER',now(),null,null,null,null),
            ('041',1,0,0,'SEEDER',now(),null,null,null,null),
            ('042',1,0,0,'SEEDER',now(),null,null,null,null),
            ('044',1,0,0,'SEEDER',now(),null,null,null,null),
            ('045',1,0,0,'SEEDER',now(),null,null,null,null),
            ('046',1,0,0,'SEEDER',now(),null,null,null,null),
            ('047',1,0,0,'SEEDER',now(),null,null,null,null),
            ('048',1,0,0,'SEEDER',now(),null,null,null,null),
            ('049',1,0,0,'SEEDER',now(),null,null,null,null),
            ('050',1,0,0,'SEEDER',now(),null,null,null,null),
            ('051',1,0,0,'SEEDER',now(),null,null,null,null),
            ('052',1,0,0,'SEEDER',now(),null,null,null,null),
            ('053',1,0,0,'SEEDER',now(),null,null,null,null),
            ('054',1,0,0,'SEEDER',now(),null,null,null,null),
            ('055',1,0,0,'SEEDER',now(),null,null,null,null),
            ('060',1,0,0,'SEEDER',now(),null,null,null,null),
            ('063',1,0,0,'SEEDER',now(),null,null,null,null),
            ('068',1,0,0,'SEEDER',now(),null,null,null,null),
            ('069',1,0,0,'SEEDER',now(),null,null,null,null),
            ('070',1,0,0,'SEEDER',now(),null,null,null,null),
            ('071',1,0,0,'SEEDER',now(),null,null,null,null),
            ('073',1,0,0,'SEEDER',now(),null,null,null,null),
            ('074',1,0,0,'SEEDER',now(),null,null,null,null),
            ('075',1,0,0,'SEEDER',now(),null,null,null,null),
            ('078',1,0,0,'SEEDER',now(),null,null,null,null),
            ('079',1,0,0,'SEEDER',now(),null,null,null,null),
            ('080',1,0,0,'SEEDER',now(),null,null,null,null),
            ('081',1,0,0,'SEEDER',now(),null,null,null,null),
            ('082',1,0,0,'SEEDER',now(),null,null,null,null),
            ('083',1,0,0,'SEEDER',now(),null,null,null,null),
            ('084',1,0,0,'SEEDER',now(),null,null,null,null),
            ('085',1,0,0,'SEEDER',now(),null,null,null,null),
            ('087',1,0,0,'SEEDER',now(),null,null,null,null),
            ('088',1,0,0,'SEEDER',now(),null,null,null,null),
            ('089',1,0,0,'SEEDER',now(),null,null,null,null),
            ('093',1,0,0,'SEEDER',now(),null,null,null,null),
            ('094',1,0,0,'SEEDER',now(),null,null,null,null),
            ('095',1,0,0,'SEEDER',now(),null,null,null,null),
            ('096',1,0,0,'SEEDER',now(),null,null,null,null),
            ('098',1,0,0,'SEEDER',now(),null,null,null,null),
            ('099',1,0,0,'SEEDER',now(),null,null,null,null),
            ('100',1,0,0,'SEEDER',now(),null,null,null,null),
            ('101',1,0,0,'SEEDER',now(),null,null,null,null),
            ('102',1,0,0,'SEEDER',now(),null,null,null,null),
            ('103',1,0,0,'SEEDER',now(),null,null,null,null),
            ('104',1,0,0,'SEEDER',now(),null,null,null,null),
            ('105',1,0,0,'SEEDER',now(),null,null,null,null),
            ('106',1,0,0,'SEEDER',now(),null,null,null,null),
            ('107',1,0,0,'SEEDER',now(),null,null,null,null),
            ('108',1,0,0,'SEEDER',now(),null,null,null,null),
            ('109',1,0,0,'SEEDER',now(),null,null,null,null),
            ('110',1,0,0,'SEEDER',now(),null,null,null,null),
            ('A13',1,0,0,'SEEDER',now(),null,null,null,null)
        ");
    }
}
