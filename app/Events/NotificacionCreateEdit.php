<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificacionCreateEdit implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $datos;
    public function __construct($datos)
    {
    $this->datos = $datos;
    }


    public function broadcastOn()
    {
        return new Channel('Notificaciones');
    }

    public function broadcastWith()
{
    return ['data' => $this->datos];
}
}
