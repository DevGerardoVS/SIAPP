<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DB;
use App\Http\Controllers\Controller;
use App\Imports\utils\FunFormats;
use Log;
use App\Models\notificaciones;


class InsertCMActividades implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $arr;
    protected $user;
    protected $id;
    protected $upp;
    public function __construct($arreglo,$user,$upp,$id)
    {
        
        $this->arr = $arreglo;
        $this->user = $user;
        $this->id = $id;
        $this->upp = $upp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    /*     DB::beginTransaction();
		try { */
            Log::debug("InsertCMActividades");
            Log::debug( $this->arr);
        $datos = json_decode($this->arr);
            $meta = 0;
                foreach ($datos as $key) {
                $key->entidad_ejecutora = getEntidadEje($key->clv_upp, $key->clv_ur, $key->area_funcional);
                Log::debug(json_encode($key));
                    switch ($key->tipoMeta) {
                        case 'C':
                            $key->nombre_actividad = null;
                            $act = FunFormats::createMml_Ac($key);
                            $key->actividad_id = $act;
                            break;
                        case 'O':
                            $act = FunFormats::createMml_Ac($key);
                            $key->actividad_id = $act;
                            break;
                        default:
                        $key->actividad_id = null;
                            break;
                    }
                   FunFormats::guardarMeta($key);
                $meta++;
                }
				
			if ($meta) {
				$b = array(
                    "username" =>  $this->user->username,
                    "accion" => 'Carga masiva metas',
                    "modulo" => 'Metas'
                );
                Controller::bitacora($b);
                $payload = json_encode($meta);
                $payloadsent = json_encode(
                    array(
                        "TypeButton" => 0,
                        "route" => "'/metas/inicio'",
                        'blocked' => 4,
                        "mensaje" => "Cargo correctamente la carga masiva UPP:".$this->upp,
                        "payload" => $payload
                    )
        
                );
                notificaciones::where('id',  $this->id)
                ->update([
                    'payload' => $payloadsent,
                    'status' => 4,
                    'updated_user' => $this->user->username
                ]);
				
			} else {
				$res = ["status" => false, "mensaje" => ["icon" => 'error', "text" => 'Hubo un problema al querer realizar la acción, contacte a soporte', "title" => "Error!"]];
                Log::debug(json_encode($res));
                //return response()->json($res, 200);
			}

	/* 	} catch (\Exception $e) {
			DB::rollback();
		} */

    }
}
