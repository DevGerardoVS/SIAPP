<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificacionCreateEdit implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $notificacion;
    public function __construct($notificacion)
    {
    $this->notificacion = $notificacion;
/*     $this->notificacion = json_decode($notificacion, true); */
    }


    public function broadcastOn()
    {
         return new Channel('notificacion'.$this->notificacion['id']);
     

}
    
}
