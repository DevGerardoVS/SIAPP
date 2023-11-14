<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActualizarSesionUsuario
{
     use Dispatchable, InteractsWithSockets, SerializesModels;
    // use Dispatchable, SerializesModels;

    public $usuario;
    public $cargapayload;
    public $cargaMasClav;
    /**
     * Create a new event instance.
     *
     * @return void
     */
   
    public function __construct($usuario, $cargapayload,$cargaMasClav)
    {
        $this->usuario = $usuario;
        $this->cargapayload = $cargapayload;
        $this->cargaMasClav = $cargaMasClav;
        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    // public function broadcastOn()
    // {
    //     return new PrivateChannel('channel-name');
    // }
    
}
