<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Helpers\BitacoraHelper;

use App\Models\notificaciones;
use Illuminate\Support\Facades\Session;
use App\Events\ActualizarSesionUsuario;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        log::channel('daily')->debug('esta activo '.Auth::user()->username);
        BitacoraHelper::saveBitacora(BitacoraHelper::getIp(),"Login","Acceso sistema","Acceso exitoso, usuario activo ".Auth::user()->username);
        return view('home');
    }


    public function borrarsesionnotificacion()
    {
        $deleted = notificaciones::where('id_usuario','=',Auth::user()->id)
        ->where('id_sistema','=',1)
        ->forceDelete();
         session::put('status',3);
         session::put('blocked',3);
         session()->forget(['payload','mensaje','route']);
        return redirect()->back();
    }




    
}
