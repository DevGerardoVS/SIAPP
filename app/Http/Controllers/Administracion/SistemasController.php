<?php

namespace App\Http\Controllers\Administracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\administracion\Sistema;
use DB;
use Auth;

class SistemasController extends Controller
{
    public function getPanel() {
    	$sistemas = Sistema::all();
    	foreach($sistemas as $sistema) {
    		if(Auth::user()->sudo == 1)
    			$sistema->estado = 1;
    		else {
    			$permisos = DB::select('SELECT p.id FROM adm_rel_sistema_grupo p INNER JOIN adm_rel_user_grupo u ON u.id_grupo = p.id_grupo WHERE u.id_usuario = ? AND p.id_sistema = ?', [Auth::user()->id, $sistema->id]);
    			if(!$permisos && $sistema->estado == 1)
    				$sistema->estado = 2;
    		}
    	}
    	return $sistemas;
    }
}
