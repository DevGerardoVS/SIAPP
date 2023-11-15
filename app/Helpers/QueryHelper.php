<?php // Code within app\Helpers\BitacoraHelper.php

namespace App\Helpers;
// namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

class QueryHelper 
{


    private static function client($service, $username, $password, $parametros, $funcion)
    {
        try {
            $soapclient = new \nusoap_client($service, true);
            $soapclient->setCredentials($username, $password, 'basic');
            $soapclient->decode_utf8 = false;
            $soapclient->timeout = 10;
            $soapclient->response_timeout = 10;

            $result = $soapclient->call($funcion, $parametros);
            if ($soapclient->fault) {
                return ["ERROR" => "Error al consumir servicio"];
            }

            return $result;
        } catch (Exception $e) {
            Log::debug($e->getMessage());
            return ["ERROR" => "Error al consumir servicio"];
        }
    }

    public static function getConfiguracion($descripcion)
    {
        $data = DB::table('configuracion')
            ->select('id', 'descripcion', 'valor')
            ->where('descripcion', $descripcion)
            ->first();

        return $data;
    }

    public static function tieneadeudo(String $placa, String $concesion, String $numeroserie)
    {
        $parametros = array(
            "MT_RevalTransportistas_PI_Sender" => array(
                "TP_CONSULTA" => "1",
                "TP_BUSQUEDA" => "1",
                "CONCESION" => $concesion,
                "PLACA" => $placa,
                "SERIE" => $numeroserie,

            ),
        );

        $QRREFRENDO = \DB::table('configuracion')
        ->where('descripcion', '=', 'SapConceptosConcesion')
        ->first();

         $datosQRREFRENDO=json_decode($QRREFRENDO->valor);


        $data = QueryHelper::client(
            $datosQRREFRENDO->UrlRefrendo,
            $datosQRREFRENDO->UserRefrendo,
            $datosQRREFRENDO->PasswordRefrendo,
            //"M1cho@can",
            $parametros,
            'SI_RevalTransportistas_PI_Sender'
        );

        if (isset($data["TB_OBJETO"]["EST_PAGO"]) && $data["TB_OBJETO"]["EST_PAGO"] == 'SIN ADEUDO') {
            return 'SIN ADEUDO';
        }
        if (isset($data["TB_OBJETO"]["EST_PAGO"]) && $data["TB_OBJETO"]["EST_PAGO"] == 'CON ADEUDO') {
            return 'CON ADEUDO';
        } else {
            return $data;
        }

    }
}
