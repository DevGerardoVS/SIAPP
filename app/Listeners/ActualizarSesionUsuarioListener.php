<?php

namespace App\Listeners;
use App\Events\ActualizarSesionUsuario;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Session;
class ActualizarSesionUsuarioListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ActualizarSesionUsuario $event)
    {
        $usuario = $event->usuario;
        $datos = $event->cargapayload;
        $cargaMasClav=$event->cargaMasClav;
        \Log::debug("Datos DEl usuario en el listener ". $usuario);
        if (auth()->check() && $usuario && $usuario->id === auth()->id()) {
            // Verificar que el usuario esté autenticado y que el usuario del evento coincida con el usuario autenticado
            Session::put('cargapayload', $datos);
            Session::put('cargaMasClav',$cargaMasClav);
            \Log::debug("puse datos al usuario");
        }
    }

    
}
