<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Helpers\BitacoraHelper;

use App\Models\carga_masiva_estatus;
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

    public function actualizarcarga()
    {
        log::channel('daily')->debug('ya puso la variable de session'.Auth::user()->username);
        Session::put('cargaMasClav',1);
        Session::put('cargapayload','El excel cargo con exito');
     
        return view('home');
    }
    public function actualizarcargfalla()
    {
        log::channel('daily')->debug('ya puso la variable de session'.Auth::user()->username);
        Session::put('cargaMasClav',2);
        Session::put('cargapayload',array (
            0 => 'El total presupuestado  no es igual al techo financiero en la upp: 012 fondo: 09 ',
            1 => 'El total presupuestado  no es igual al techo financiero en la upp: 012 fondo: 02 ',
            2 => 'El total presupuestado  no es igual al techo financiero en la upp: 012 fondo: 0L ',
            3 => 'El total presupuestado  no es igual al techo financiero en la upp: 012 fondo: 0K ',
          ));
      
        return view('home');
    }
    public function borrarsesionexcel()
    {
        $deleted = carga_masiva_estatus::where('id_usuario','=',Auth::user()->id)->forceDelete();
        // log::channel('daily')->debug('borro la variable de session'.Auth::user()->username);
        session()->forget(['cargapayload', 'cargaMasClav']);
        return back();
    }
    public function actualizarcargafin()
    {
        // log::channel('daily')->debug('ya quite la variable '.Auth::user()->username);
        Session::put('cargaMasClav',3);
      
        return view('home');
    }

    public function agregarcredenciales()
    {
        $fr=session::pull('cargaMasClav');
        session()->forget(['cargapayload', 'cargaMasClav']);
         
        
         $deleted = carga_masiva_estatus::where('id_usuario','=',Auth::user()->id)->forceDelete();
         dd(Auth::user()->id);
          return "ya cambie";
    }

    
}
