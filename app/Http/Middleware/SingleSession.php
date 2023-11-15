<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\carga_masiva_estatus;
class SingleSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
      // Verificar si el usuario está autenticado
     
       
        // Obtener el ID de sesión actual
        if (auth()->check()) {
            // User is authenticated, so you can access their ID
            $userId = auth()->user()->id;
    
            // Continue with your middleware logic here
            $data = \DB::table('carga_masiva_estatus')
            ->select('*')
         
            ->where('id_usuario','=',$userId)
            ->first();
           
           
            if(isset($data->cargapayload)){
                // Log::debug('si entro');
                Session::put('cargapayload', $data->cargapayload);
                Session::put('cargaMasClav',$data->cargaMasClav);
                session(['cargapayload' => $data->cargapayload]);
                session(['cargaMasClav' => $data->cargaMasClav]);
            }
            
         
                 
    
    
        }


       

        // $currentSessionId = session()->getId();

    

        // // Obtener el ID de sesión almacenado en la base de datos
        // $storedSessionId = Auth::user()->session_id;

        // Log::info("tu sesssion es : ");
        // Log::debug($currentSessionId);
        // Log::info("y tu usuario es");
        // Log::debug($storedSessionId);
        // // Verificar si el ID de sesión actual es diferente al almacenado
        // if ($currentSessionId !== $storedSessionId) {
        //     // Cerrar la sesión actual
        //     Auth::logout();
        //     session()->invalidate();

        //     // Redirigir al usuario a la página de inicio de sesión con un mensaje de error
        //     return redirect()->route('login')->with('error', 'Tu sesión ha sido iniciada en otro dispositivo.');
        // }
    

    return $next($request);

    }
}
