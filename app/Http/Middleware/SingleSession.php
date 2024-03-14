<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\notificaciones;
use Illuminate\Support\Facades\Redirect;
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
            $data = \DB::table('notificaciones')
            ->select('*')
         
            ->where('id_usuario','=',$userId)
            ->where('id_sistema','=',1)
            ->first();
           
           
            if(isset($data->status)){
                $payload = json_decode($data->payload);
                session::put('mensaje',$payload->mensaje);
                session::put('route',$payload->route);
                session::put('TypeButton',$payload->TypeButton);
                session::put('blocked',$payload->blocked);
                Session::put('payload', $payload->payload);
                Session::put('status',$data->status);
                session(['payload' => $payload->payload]);
                session(['status' => $data->status]);
            }
            else{
                Session::put('payload','');
                session::put('mensaje','');
                session::put('blocked',3);
                session::put('route','');
                Session::put('status',3);
                Session::put('TypeButton','');

                session(['payload' =>'']);
                session(['status' => 3]);
            }
            
         
                 
    
    
        }else{
           
            // Auth::logout();
            // return Redirect::route('login')->with('error', 'Tu sesión ha caducado. Por favor, inicia sesión de nuevo.');
       
            // session()->invalidate();
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
