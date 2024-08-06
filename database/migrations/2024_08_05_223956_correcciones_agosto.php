<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\UppExtras;
use App\Models\Catalogo;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("truncate v_epp;");
        DB::unprepared("insert into v_epp(
        	id,ejercicio,mes_i,mes_f,clv_sector_publico,sector_publico,clv_sector_publico_f,sector_publico_f,clv_sector_economia,sector_economia,clv_subsector_economia,subsector_economia,clv_ente_publico,ente_publico,
        	clv_upp,upp,clv_subsecretaria,subsecretaria,clv_ur,ur,clv_finalidad,finalidad,clv_funcion,funcion,clv_subfuncion,subfuncion,clv_eje,eje,clv_linea_accion,linea_accion,clv_programa_sectorial,programa_sectorial,
        	clv_tipologia_conac,tipologia_conac,clv_programa,programa,clv_subprograma,subprograma,clv_proyecto,proyecto,estatus,presupuestable,con_mir,confirmado,tipo_presupuesto,created_at,deleted_at,created_user,updated_user,deleted_user
        )
        select
        	e.id,e.ejercicio,e.mes_i,e.mes_f,
        	c01.clave clv_sector_publico,c01.descripcion sector_publico,
        	c02.clave clv_sector_publico_f,c02.descripcion sector_publico_f,
        	c03.clave clv_sector_economia,c03.descripcion sector_economia,
        	c04.clave clv_subsector_economia,c04.descripcion subsector_economia,
        	c05.clave clv_ente_publico,c05.descripcion ente_publico,
        	c06.clave clv_upp,c06.descripcion upp,
        	c07.clave clv_subsecretaria,c07.descripcion subsecretaria,
        	c08.clave clv_ur,c08.descripcion ur,
        	c09.clave clv_finalidad,c09.descripcion finalidad,
        	c10.clave clv_funcion,c10.descripcion funcion,
        	c11.clave clv_subfuncion,c11.descripcion subfuncion,
        	c12.clave clv_eje,c12.descripcion eje,
        	c13.clave clv_linea_accion,c13.descripcion linea_accion,
        	c14.clave clv_programa_sectorial,c14.descripcion programa_sectorial,
        	case
        		when c.tipologia_conac_id is null then c16.clave
        		else c15.clave
        	end clv_tipologia_conac,
        	case
        		when c.tipologia_conac_id is null then c16.descripcion
        		else c15.descripcion
        	end tipologia_conac,
        	c17.clave clv_programa,c17.descripcion programa,
        	c18.clave clv_subprograma,c18.descripcion subprograma,
        	c19.clave clv_proyecto,c19.descripcion proyecto,
        	e.estatus,e.presupuestable,e.con_mir,e.confirmado,e.tipo_presupuesto,
        	e.created_at,e.deleted_at,
        	e.created_user,e.updated_user,e.deleted_user
        from epp e
        join clasificacion_administrativa ca on e.clasificacion_administrativa_id = ca.id
        join entidad_ejecutora ee on e.entidad_ejecutora_id = ee.id
        join clasificacion_funcional cf on e.clasificacion_funcional_id = cf.id
        join pladiem p on e.pladiem_id = p.id
        join conac c on e.conac_id = c.id
        join catalogo c01 on ca.sector_publico_id = c01.id
        join catalogo c02 on ca.sector_publico_f_id = c02.id
        join catalogo c03 on ca.sector_economia_id = c03.id
        join catalogo c04 on ca.subsector_economia_id = c04.id
        join catalogo c05 on ca.ente_publico_id = c05.id
        join catalogo c06 on ee.upp_id = c06.id
        join catalogo c07 on ee.subsecretaria_id = c07.id
        join catalogo c08 on ee.ur_id = c08.id
        join catalogo c09 on cf.finalidad_id = c09.id
        join catalogo c10 on cf.funcion_id = c10.id
        join catalogo c11 on cf.subfuncion_id = c11.id
        join catalogo c12 on p.eje_id = c12.id
        join catalogo c13 on p.linea_accion_id = c13.id
        join catalogo c14 on p.programa_sectorial_id = c14.id
        left join catalogo c15 on c.tipologia_conac_id = c15.id
        left join catalogo c16 on c.padre_id = c16.id
        join catalogo c17 on e.programa_id = c17.id
        join catalogo c18 on e.subprograma_id = c18.id
        join catalogo c19 on e.proyecto_id = c19.id;");
        $upps_ids =Catalogo::select('id','clave')
        ->where(['grupo_id'=>6,'ejercicio'=>2025])
        ->whereIn('clave',['009','014','078','082','083','031'])->get();

        foreach ($upps_ids as $key) {
           $uppExt= UppExtras::where(['ejercicio' => 2025, 'upp_id' => $key->id])->first();
           if(isset($uppExt)){
                $uppExt->estatus_epp = 0;
                $uppExt->save();

           }
           if($key->clave=='083' || $key->clave=='031'){
                DB::table('archivos_epp')
                ->where([
                'ejercicio'=>2025,
                'upp_id'=>$key->id,
                'acciones'=>null,
                ])->delete();

           }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
