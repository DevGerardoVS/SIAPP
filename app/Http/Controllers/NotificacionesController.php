<?php

namespace App\Http\Controllers;

use App\Models\notificaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NotificacionesController extends Controller
{

    public function store(Request $request)
    {
        $notificaciones = json_decode($request->notificacion);
        $data = notificaciones::where('id', $notificaciones->id)->first();
        $payload = json_decode($data->payload);
        Session::put('mensaje', $payload->mensaje);
        Session::put('route', $payload->route);
        Session::put('TypeButton', $payload->TypeButton);
        Session::put('status', $data->status);
        Session::put('payload', $payload->payload);
        Session::put('status', $data->status);
        Session::put('blocked', $data->blocked);
        return true;

    }
}