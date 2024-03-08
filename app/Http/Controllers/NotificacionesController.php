<?php

namespace App\Http\Controllers;

use App\Models\notificaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Auth;

class NotificacionesController extends Controller
{

    public function store(Request $request)
    {
        $notificaciones = json_decode($request->notificacion);
        $data = notificaciones::where('id', $notificaciones->id)->first();
        if ($data->id_usuario === Auth::user()->id && $data->id_sistema === 1) {
            $payload = json_decode($data->payload);
            Session::put('mensaje', $payload->mensaje);
            Session::put('route', $payload->route);
            Session::put('TypeButton', $payload->TypeButton);
            Session::put('status', $data->status);
            Session::put('payload', $payload->payload);
            Session::put('blocked', $payload->blocked);
            return response('ok', 200)
                ->header('Content-Type', 'text/plain');
        } else {
            return response('Forbidden', 403)
                ->header('Content-Type', 'text/plain');
        }

    }
}