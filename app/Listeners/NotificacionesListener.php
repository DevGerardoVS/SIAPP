<?php

namespace App\Listeners;

use App\Events\NotificacionCreateEdit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Session;
use Log;

class NotificacionesListener
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
     * @param  \App\Events\NotificacionCreateEdit  $event
     * @return void
     */
    public function handle(NotificacionCreateEdit $event)
    {
        $isfront = session()->get('estatus',null);
        Log::debug($isfront);

        if($isfront != null){
            $data = $event->datos;
            $payload = json_decode($data->payload);
            session::put('mensaje', $payload->mensaje);
            session::put('route', $payload->route);
            Session::put('payload', $payload->payload);
            Session::put('status', $data->status);
            session(['payload' => $payload->payload]);
            session(['status' => $data->status]);
        }


    }
}
