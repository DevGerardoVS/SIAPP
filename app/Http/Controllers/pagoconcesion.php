<?php

namespace App\Http\Controllers;

use App\Helpers\AdminPolizasConcesionesHelper;
use App\Helpers\bancoshelpers;
use App\Helpers\BitacoraHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorPNG;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class pagoconcesion extends Controller
{

    //
    private function client($service, $username, $password, $parametros, $funcion)
    {
        try {
            $soapclient = new \nusoap_client ($service, true);
            $soapclient->setCredentials($username, $password, 'basic');
            $soapclient->decode_utf8 = false;
            $soapclient->timeout = 10;
            $soapclient->response_timeout = 10;

            $result = $soapclient->call($funcion, $parametros);
            // \Log::debug($soapclient->request);
            // \Log::debug($soapclient->response);
            if ($soapclient->fault) {
                return ["ERROR" => "Error al consumir servicio"];
            }

            return $result;
        } catch (Exception $e) {
            Log::debug($e->getMessage());
            return ["ERROR" => "Error al consumir servicio"];
        }
    }
    public function index()
    {

        // $perfiles = DB::table('perfiles')
        //     ->select('id', 'nombre', 'tipo_perfil')
        //     ->where('estatus', 1)
        //     ->orderBy('nombre')
        //     ->get();

        $dataSet = array();

        return view('pagoconcesion.home', ['dataSet' => $dataSet]);
    }

    public function imprimirdatoss(Request $request)
    {

        try {
            DB::beginTransaction();
            $tipo = 'a';
            if (isset(\Auth::user()->username)) {
                $tipo = 'b';
            }
            $returnData = AdminPolizasConcesionesHelper::agregarPolizaSeguro($request, $tipo);
            DB::commit();
            return $returnData;
            if ($returnData['status'] == 'error') {
                return response()->json($returnData, 500);
            }
        } catch (\Exception $exp) {
            DB::rollBack();
            \Log::channel('daily')->debug('Excepcion ' . $exp->getMessage());
            return $exp->getMessage();
            $returnData = array(
                'status' => 'error',
                'title' => 'Error',
                'message' => 'Hubo un error, no se pudo agregar la póliza.',
            );
            return response()->json($returnData, 500);
        }

        $returnData = array(
            'status' => 'success',
            'title' => 'Éxito',
            'message' => 'Se cambió el estatus de la póliza con éxito',
        );

        return response()->json($returnData, 200);

        //         $detellesconcesion = \DB::table('spcl_polizas_seguro')
        //         ->where('no_concesion','=',$request->No_Consesion)->first();

        // $polizaexixtente = \DB::table('spcl_polizas_seguro')
        //     ->where('no_concesion', '!=', $request->No_Consesion)
        //     ->where('no_poliza', '=', $request->num_poliz)
        //     ->count();
        // //  return $polizaexixtente;
        // if ($polizaexixtente > 0) {
        //     $returnData = array(
        //         'status' => 'error',
        //         'title' => 'Poliza ya existe',
        //         'message' => 'la poliza ingresada ya existe',

        //     );
        //     // return redirect()->back()->withErrors("Error al consumir servicio")->withInput($request->input());
        //     return response()->json($returnData);
        // }

        // if ((isset($detellesconcesion->fecha_vencimiento) && $detellesconcesion->fecha_vencimiento < date("Y-m-d")) || (isset($detellesconcesion->verificado) && $detellesconcesion->verificado == '1')) {

        //     $pizza = $detellesconcesion->archivo_poliza;
        //     $pieces = explode(".", $pizza);
        //     DB::table('spcl_polizas_seguro_historico')->updateOrInsert([
        //         'no_concesion' => $detellesconcesion->no_concesion,
        //         'fecha_vencimiento' => $detellesconcesion->fecha_vencimiento,
        //     ], [
        //         'id_aseguradora' => $detellesconcesion->id_aseguradora,
        //         'no_poliza' => $detellesconcesion->no_poliza,
        //         'otro_aseguradora' => $detellesconcesion->otro_aseguradora,
        //         'archivo_poliza' => $pieces[0] . $detellesconcesion->fecha_vencimiento . '.' . $pieces[1],
        //         'verificado' => $detellesconcesion->verificado,
        //         'observaciones' => $detellesconcesion->observaciones,
        //         "created_by" => $detellesconcesion->created_by,
        //         'Extension_archivo_poliza' => $pieces[1],

        //     ]);
        //     $array_databitcacora = [
        //         'lieacodigo' => "🚀 ~ file: pagoconcesion.php:81 ~ metodo :imprimirdatoss",
        //         'no_concesion' => $detellesconcesion->no_concesion,
        //         'fecha_vencimiento' => $detellesconcesion->fecha_vencimiento,

        //         'id_aseguradora' => $detellesconcesion->id_aseguradora,
        //         'no_poliza' => $detellesconcesion->no_poliza,
        //         'otro_aseguradora' => $detellesconcesion->otro_aseguradora,
        //         'archivo_poliza' => $pieces[0] . $detellesconcesion->fecha_vencimiento . '.' . $pieces[1],
        //         'verificado' => $detellesconcesion->verificado,
        //         'observaciones' => $detellesconcesion->observaciones,
        //         "created_by" => $detellesconcesion->created_by,
        //         'Extension_archivo_poliza' => $pieces[1],

        //     ];
        //     BitacoraHelper::saveBitacoracont(BitacoraHelper::getIp(), "spcl_polizas_seguro_historico", "creteorupdate", json_encode($array_databitcacora), $request->No_Consesion);

        //     $archivo = $detellesconcesion->archivo_poliza;

        //     if (\Storage::disk('s3')->exists($detellesconcesion->archivo_poliza)) {
        //         \Storage::disk('s3')->move($detellesconcesion->archivo_poliza, $pieces[0] . $detellesconcesion->fecha_vencimiento . '.' . $pieces[1]);
        //     }

        // }

        // $fileExt = $request->archivo2->getClientOriginalExtension();
        // $archivo = $request->file('archivo2')->storeAs('public/consesiones', $request->No_Consesion . "." . $fileExt);
        // $exist = \Storage::disk('s3')->has('public/consesiones/' . $request->No_Consesion . "." . $fileExt);

        // if ($exist == 1 && $exist != null && $exist != '') {
        //     try {
        //         DB::table('spcl_polizas_seguro')->updateOrInsert([
        //             'no_concesion' => $request->No_Consesion], [
        //             'id_aseguradora' => $request->aseg,
        //             'no_poliza' => $request->num_poliz,
        //             'otro_aseguradora' => $request->asegotro == null ? "NO" : $request->asegotro,
        //             'fecha_vencimiento' => $request->aseg_vencim,
        //             'archivo_poliza' => $archivo,
        //             'verificado' => "1",
        //             'observaciones' => "",
        //             "created_by" => $request->nombconses,
        //             'Extension_archivo_poliza' => $fileExt,
        //         ]);

        //         \DB::table('spcl_detalle_concesion')
        //         ->where('no_concesion','=',$request->No_Consesion)
        //         ->limit(1)
        //         ->update([
        //             'telefono' => $request->telefono,
        //             'email' => $request->email,
        //              ]);

        //         // $update = \DB::table('student') ->where('id', $data['id']) ->limit(1) ->update( [ 'name' => $data['name'], 'address' => $data['address'], 'email' => $data['email'], 'contactno' => $data['contactno'] ]);

        //         $array_databitcacora = [
        //             'lineacodigo' => '🚀 ~ file: pagoconcesion.php:128 ~ metodo: imprimirdatoss',
        //             'no_concesion' => $request->No_Consesion,
        //             'id_aseguradora' => $request->aseg,
        //             'no_poliza' => $request->num_poliz,
        //             'otro_aseguradora' => $request->asegotro == null ? "NO" : $request->asegotro,
        //             'fecha_vencimiento' => $request->aseg_vencim,
        //             'archivo_poliza' => $archivo,
        //             'verificado' => "1",
        //             'observaciones' => "",
        //             "created_by" => $request->nombconses,
        //             'Extension_archivo_poliza' => $fileExt,
        //         ];
        //         BitacoraHelper::saveBitacoracont(BitacoraHelper::getIp(), "spcl_polizas_seguro", "creteorupdate", json_encode($array_databitcacora), $request->No_Consesion);

        //     } catch (Exception $e) {
        //         \Log::channel('daily')->debug('Excepcion ' . $exp->getMessage());

        //         $returnData = array(
        //             'status' => 'error',
        //             'title' => 'Error',
        //             'message' => 'Error al insertar datos',
        //         );
        //         // return redirect()->back()->withErrors("Error al consumir servicio")->withInput($request->input());
        //         return response()->json($returnData);
        //     }

        //     $returnData = array(
        //         'status' => 'success',
        //         'title' => 'Correcto',
        //         'message' => 'Póliza guardada con éxito',
        //     );
        //     return response()->json($returnData);

        // }

    }
    public function descargarformato(Request $request)
    {
        $stringurl = (string) $request->urladeudo;
        $string = str_replace("\"", '', json_decode($stringurl));

        $generatorHTML = new BarcodeGeneratorPNG();
        $qr = base64_encode(QrCode::format('svg')->size(180)->errorCorrection('H')->generate("$string"));

        $bancos = bancoshelpers::getlistabancos(json_decode($request->tbconvenio));

        $array_data = array(

            'datos' => ['lcaptura' => $request->lcaptura,
                'lineadecodigo' => "🚀 ~ file: pagoconcesion.php:181 ~ descargarformato ~ metodo: descargarformato",
                'totalapagar' => $request->totalapagar,
                'TBconceptos' => json_decode($request->TBconceptos),
                'nombconses' => $request->nombconses,
                'barcode' => $generatorHTML,
                'qrcode' => $stringurl,
                'fech_ven' => $request->fech_ven,
                'tbconvenio' => ($bancos),

            ],

        );
        BitacoraHelper::saveBitacoracont(BitacoraHelper::getIp(), "cedulaimpresion", "descarga", json_encode($array_data), $request->No_Consesion);

        return $pdf = \PDF::loadView('pagoconcesion.cedulaimpresion',
            ['lcaptura' => $request->lcaptura,
                'totalapagar' => $request->totalapagar,
                'TBconceptos' => json_decode($request->TBconceptos),
                'nombconses' => $request->nombconses,
                'barcode' => $generatorHTML,
                'qrcode' => $qr,
                'fech_ven' => $request->fech_ven,
                'tbconvenio' => ($bancos),

            ]
        )->download('cedula_de_pago.pdf');

        return $pdf;
        return $pdf->download('efe.pdf');
        return $request->all();
    }

    public function guardarpoliza(Request $request)
    {

        try {
            DB::beginTransaction();
            $tipo = 'a';
            if (isset(\Auth::user()->username)) {
                $tipo = 'b';
            }
            $returnData = AdminPolizasConcesionesHelper::agregarPolizaSeguro($request, $tipo);
            DB::commit();
            return $returnData;
            if ($returnData['status'] == 'error') {
                return response()->json($returnData, 500);
            }
        } catch (\Exception $exp) {
            DB::rollBack();
            \Log::channel('daily')->debug('Excepcion ' . $exp->getMessage());
            return $exp->getMessage();
            $returnData = array(
                'status' => 'error',
                'title' => 'Error',
                'message' => 'Hubo un error, no se pudo agregar la póliza.',
            );
            return response()->json($returnData, 500);
        }

        $returnData = array(
            'status' => 'success',
            'title' => 'Éxito',
            'message' => 'Se cambió el estatus de la póliza con éxito',
        );

        return response()->json($returnData, 200);

        // $detellesconcesion = \DB::table('spcl_polizas_seguro')
        //     ->where('no_concesion', '=', $request->No_Consesion)->first();
        // if ((isset($detellesconcesion->fecha_vencimiento) && $detellesconcesion->fecha_vencimiento < date("Y-m-d")) || (isset($detellesconcesion->verificado) && $detellesconcesion->verificado == '1')) {

        //     $pizza = $detellesconcesion->archivo_poliza;
        //     $pieces = explode(".", $pizza);
        //     DB::table('spcl_polizas_seguro_historico')->updateOrInsert([
        //         'no_concesion' => $detellesconcesion->no_concesion,
        //         'fecha_vencimiento' => $detellesconcesion->fecha_vencimiento,
        //     ], [
        //         'id_aseguradora' => $detellesconcesion->id_aseguradora,
        //         'no_poliza' => $detellesconcesion->no_poliza,
        //         'otro_aseguradora' => $detellesconcesion->otro_aseguradora,
        //         'archivo_poliza' => $pieces[0] . $detellesconcesion->fecha_vencimiento . '.' . $pieces[1],
        //         'verificado' => $detellesconcesion->verificado,
        //         'observaciones' => $detellesconcesion->observaciones,
        //         "created_by" => $detellesconcesion->created_by,
        //         'Extension_archivo_poliza' => $pieces[1],

        //     ]);
        //     $archivo = $detellesconcesion->archivo_poliza;

        //     if (\Storage::disk('s3')->exists($detellesconcesion->archivo_poliza)) {
        //         \Storage::disk('s3')->move($detellesconcesion->archivo_poliza, $pieces[0] . $detellesconcesion->fecha_vencimiento . '.' . $pieces[1]);

        //     }

        //     // $returnData = array(
        //     //      'status' => 'success',
        //     //      'title' => 'la poliza vencio :',
        //     //      'message' => $detellesconcesion->fecha_vencimiento,
        //     //  );
        //     //  return response()->json($returnData);
        // }

        // $fileExt = $request->archivo->getClientOriginalExtension();
        // $archivo = $request->file('archivo')->storeAs('public/consesiones', $request->No_Consesion . "." . $fileExt);
        // $exist = \Storage::disk('s3')->has('public/consesiones/' . $request->No_Consesion . "." . $fileExt);

        // if ($exist == 1 && $exist != null && $exist != '') {
        //     try {
        //         DB::table('spcl_polizas_seguro')->updateOrInsert([
        //             'no_concesion' => $request->No_Consesion], [
        //             'id_aseguradora' => $request->aseg,
        //             'no_poliza' => $request->num_poliz,
        //             'otro_aseguradora' => $request->asegotro == null ? "NO" : $request->asegotro,
        //             'fecha_vencimiento' => $request->aseg_vencim,
        //             'archivo_poliza' => $archivo,
        //             'verificado' => "0",
        //             'observaciones' => "",
        //             'Extension_archivo_poliza' => $fileExt,
        //             "created_by" => $request->nombconses,

        //         ]);

        //         \DB::table('spcl_detalle_concesion')
        //         ->where('no_concesion','=',$request->No_Consesion)
        //         ->limit(1)
        //         ->update([
        //             'telefono' => $request->telefono,
        //             'email' => $request->email,
        //              ]);

        //     } catch (Exception $e) {
        //         \Log::channel('daily')->debug('Excepcion ' . $exp->getMessage());

        //         $returnData = array(
        //             'status' => 'error',
        //             'title' => 'Error',
        //             'message' => 'Error al insertar datos',
        //         );
        //         return response()->json($returnData);
        //     }
        //     $returnData = array(
        //         'status' => 'success',
        //         'title' => 'Correcto',
        //         'message' => 'Póliza guardada con éxito',
        //     );
        //     return response()->json($returnData);
        // } else {
        // }

    }

    public function encrypt_decrypt($action, $string)
    {

        $string = str_replace(' ', '', $string);
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'cocotra.2023@sfa.gob';
        $secret_iv = 'cocotra.2023@sfa.gob';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    public function getdatosconsesiones(Request $request)
    {
        //  return date("Y");
        // Creamos los datos de entrada

        $concesionblok = DB::table('spcl_concesiones_bloqueadas')
            ->where('no_concesion', '=', $request->No_Consesion)
            ->where('estatus', '=', 0)
            ->first();
        if (isset($concesionblok->Obsevaciones)) {
            $returnData = array(
                'status' => 'bloqueado',
                'icon' => 'warning',
                'title' => 'Aviso',
                'message' => __('messages.mensajedebloqueo'),
            );
            return redirect()->back()->withErrors($returnData)->withInput($request->input());
        }
        $cataseg = DB::table('cat_aseguradoras')
            ->where('status', '=', 1)
            ->get();
        $QRREFRENDO = \DB::table('configuracion')
            ->where('descripcion', '=', 'SapConceptosConcesion')
            ->first();

        $datosQRREFRENDO = json_decode($QRREFRENDO->valor);
        $parametros = array(
            "MT_RevalTransportistas_PI_Sender" => array(
                "TP_CONSULTA" => "2",
                "TP_BUSQUEDA" => "1",
                "CONCESION" => $request->No_Consesion,
                "PLACA" => $request->No_placa,
                "SERIE" => $request->No_serie,

            ),
        );

        //code...
        $data = $this->client(
            $datosQRREFRENDO->UrlRefrendo,
            $datosQRREFRENDO->UserRefrendo,
            $datosQRREFRENDO->PasswordRefrendo,
            //"M1cho@can",
            $parametros,
            'SI_RevalTransportistas_PI_Sender'
        );

        // \Log::info("esto es lo que trae data  ");
        // \Log::debug($data);

        if ($data == null) {

            //     \Log::info("no tiene adeudo ");
            //   return response()->json(array("mensaje" => "Error al consumir servicio"), 500);
            \Log::info("esto es lo que trae data  ");
            \Log::debug($data);

            $returnData = array(
                'status' => 'bloqueado',
                'icon' => 'warning',
                'title' => 'Aviso',
                'message' => __('Servicio saturado intente más tarde'),
            );
            return redirect()->back()->withErrors($returnData)->withInput($request->input());

            // return $data;
            return redirect()->back()->withErrors("Error al consumir servicio")->withInput($request->input());
            //
        } elseif (isset($data["ES_MSJ"])) {
            return redirect()->back()->withErrors($data["ES_MSJ"]["TP_MENS"])->withInput($request->input());

        } elseif (!isset($data["TB_OBJETO"]) && !isset($data["ERROR"])) {
            return redirect()->back()->withErrors($data["ES_MSJ"]["TP_MENS"])->withInput($request->input());

        } elseif (!isset($data["TB_OBJETO"]["TB_CONCEPTOS"]) && isset($data["ERROR"])) {
            $parametros = array(
                "MT_RevalTransportistas_PI_Sender" => array(
                    "TP_CONSULTA" => "1",
                    "TP_BUSQUEDA" => "1",
                    "CONCESION" => $request->No_Consesion,
                    "PLACA" => $request->No_placa,
                    "SERIE" => $request->No_serie,

                ),
            );

            $data = $this->client(
                $datosQRREFRENDO->UrlRefrendo,
                $datosQRREFRENDO->UserRefrendo,
                $datosQRREFRENDO->PasswordRefrendo,
                //"M1cho@can",
                $parametros,
                'SI_RevalTransportistas_PI_Sender'
            );

            $datosdelaconsesion = DB::table('spcl_detalle_concesion')
                ->updateOrInsert(
                    ['no_concesion' => $data["TB_OBJETO"]["CONCESION"]], [
                        'objeto_contrato' => $data["TB_OBJETO"]["OBJ_CONTRATO"],
                        'cuenta_contrato' => $data["TB_OBJETO"]["CTA_CONTRATO"],
                        'interlocutor' => $data["TB_OBJETO"]["INTERLOCUTOR"],
                        'rfc' => $data["TB_OBJETO"]["RFC"],
                        'propietario' => $data["TB_OBJETO"]["NOMBRE"],
                        'no_placas' => $data["TB_OBJETO"]["PLACA"],
                        'no_serie_vehiculo' => $data["TB_OBJETO"]["SERIE"],
                        'grupo' => $data["TB_OBJETO"]["GRUPO"],
                        'tipo_servicio' => $data["TB_OBJETO"]["TIPO_SERV"],
                        'estatus' => $data["TB_OBJETO"]["ESTATUS"],
                        'modalidad' => $data["TB_OBJETO"]["MODALIDAD"],
                        'email' => '',
                        'created_by' => BitacoraHelper::getIp(),
                    ]);

            $actualizaciopago = DB::table('spcl_detalle_pago')
                ->where('no_concesion', '=', $request->No_Consesion)
                ->where('ejercicio', '=', date("Y"))
                ->update(['estatus_pago' => 1]
                );

            \Log::info("no tiene conceptos ");
            \Log::debug($actualizaciopago);
            if ($actualizaciopago < 1) {
                $totalapagar = 0;
                foreach ($data["TB_OBJETO"]["TB_CONCEPTOS"] as $conceptos) {

                    $totalapagar += str_replace('-', '', $conceptos["IMPORTE"]);

                }
                DB::table('spcl_detalle_pago')->updateOrInsert(
                    ['no_concesion' => $data["TB_OBJETO"]["CONCESION"],
                        'ejercicio' => date("Y"),
                    ], [
                        'importe_total' => $totalapagar,
                        'detalle_conceptos' => json_encode($data["TB_OBJETO"]["TB_CONCEPTOS"]),
                        'convenio_bancos' => "N/A",
                        'linea_captura' => "N/A",
                        'orden_pago' => "N/A",
                        'fecha_vencimiento' => date("Y") . "-12-31",
                        'importe_concesion' => $totalapagar / 2,
                        'importe_refrendo' => $totalapagar / 2,
                        'estatus_pago' => 1,
                        'moneda' => 'mxn',

                        'created_by' => BitacoraHelper::getIp(),

                    ]);
            }

         $tknurl = $this->geturl_refrendo($data["TB_OBJETO"]["PLACA"], $data["TB_OBJETO"]["SERIE"]);
            \Log::info("información de la URL");
            \Log::debug($tknurl);
             $tknurl = $tknurl["url"];

            $array_data_bitacora = [
                'linea de codigo' => "🚀 ~ file: pagoconcesion.php:432 ~ BitacoraHelper:",
                'accion' => 'manda al front los datos de la concesion sin adeudos',
                'no_concesion' => $data["TB_OBJETO"]["CONCESION"],
                'objeto_contrato' => $data["TB_OBJETO"]["OBJ_CONTRATO"],
                'cuenta_contrato' => $data["TB_OBJETO"]["CTA_CONTRATO"],
                'interlocutor' => $data["TB_OBJETO"]["INTERLOCUTOR"],
                'rfc' => $data["TB_OBJETO"]["RFC"],
                'propietario' => $data["TB_OBJETO"]["NOMBRE"],
                'no_placas' => $data["TB_OBJETO"]["PLACA"],
                'no_serie_vehiculo' => $data["TB_OBJETO"]["SERIE"],
                'grupo' => $data["TB_OBJETO"]["GRUPO"],
                'tipo_servicio' => $data["TB_OBJETO"]["TIPO_SERV"],
                'estatus' => $data["TB_OBJETO"]["ESTATUS"],
                'modalidad' => $data["TB_OBJETO"]["MODALIDAD"],
                'created_by' => BitacoraHelper::getIp(),
                'url del adeudo' => $tknurl,
            ];

            BitacoraHelper::saveBitacoracont(BitacoraHelper::getIp(), "getpagos", "obtenerdatos", json_encode($array_data_bitacora), $request->No_Consesion);

            return view('pagoconcesion.GetPagossinadeudos', [
                "RFC" => $data["TB_OBJETO"]["RFC"],
                "No_Consesion" => $data["TB_OBJETO"]["CONCESION"],
                "No_serie" => $data["TB_OBJETO"]["SERIE"],
                "No_placa" => $data["TB_OBJETO"]["PLACA"],
                "grupo" => $data["TB_OBJETO"]["GRUPO"],
                "estatus" => $data["TB_OBJETO"]["ESTATUS"],
                "modalidad" => $data["TB_OBJETO"]["MODALIDAD"],
                "tpserv" => $data["TB_OBJETO"]["TIPO_SERV"],
                "nombconses" => $data["TB_OBJETO"]["NOMBRE"],
                "cataseg" => $cataseg,
                'urladeudo' => $tknurl,

            ]);

            return redirect()->back()->withErrors($data["ES_MSJ"]["TP_MENS"])->withInput($request->input());

        } elseif (isset($data["TB_OBJETO"]["TB_CONCEPTOS"])) {
            $totalapagar = $data["ES_ORD_PAGO"]["IMPORTE"];

            try {
                DB::beginTransaction();

                DB::table('spcl_detalle_concesion')
                    ->updateOrInsert(['no_concesion' => $data["TB_OBJETO"]["CONCESION"]], [
                        'objeto_contrato' => $data["TB_OBJETO"]["OBJ_CONTRATO"],
                        'cuenta_contrato' => $data["TB_OBJETO"]["CTA_CONTRATO"],
                        'interlocutor' => $data["TB_OBJETO"]["INTERLOCUTOR"],
                        'rfc' => $data["TB_OBJETO"]["RFC"],
                        'propietario' => $data["TB_OBJETO"]["NOMBRE"],
                        'no_placas' => $data["TB_OBJETO"]["PLACA"],
                        'no_serie_vehiculo' => $data["TB_OBJETO"]["SERIE"],
                        'grupo' => $data["TB_OBJETO"]["GRUPO"],
                        'tipo_servicio' => $data["TB_OBJETO"]["TIPO_SERV"],
                        'estatus' => $data["TB_OBJETO"]["ESTATUS"],
                        'modalidad' => $data["TB_OBJETO"]["MODALIDAD"],
                        'email' => '',
                        'created_by' => BitacoraHelper::getIp(),
                    ]);

                DB::table('spcl_detalle_pago')->updateOrInsert(
                    ['no_concesion' => $data["TB_OBJETO"]["CONCESION"],
                        'ejercicio' => date("Y"),
                    ], [
                        'importe_total' => $totalapagar,
                        'detalle_conceptos' => json_encode($data["TB_OBJETO"]["TB_CONCEPTOS"]),
                        'convenio_bancos' => json_encode($data["TB_BANCOS"]),
                        'linea_captura' => $data["ES_ORD_PAGO"]["LN_CAPTURA"],
                        'orden_pago' => $data["ES_ORD_PAGO"]["ORDEN_PAGO"],
                        'fecha_vencimiento' => $data["ES_ORD_PAGO"]["FEC_VENCIMIENTO"],
                        'importe_concesion' => $totalapagar / 2,
                        'importe_refrendo' => $totalapagar / 2,
                        'estatus_pago' => 0,
                        'moneda' => 'mxn',

                        'created_by' => BitacoraHelper::getIp(),

                    ]);
                DB::commit();
            } catch (\Exception $exp) {
                DB::rollBack();
                \Log::channel('daily')->debug('Excepcion ' . $exp->getMessage());
                $returnData = array(
                    'status' => 'error',
                    'title' => 'Error',
                    'message' => 'Hubo un error, no se pudo ',
                );
                return redirect()->back()->withErrors("")->withInput($request->input());
            }
            $fechavenlinea = date_format(date_create($data["ES_ORD_PAGO"]["FEC_VENCIMIENTO"]), 'd/m/Y ');
            $tkn = $this->encrypt_decrypt("encrypt", $data["ES_ORD_PAGO"]["LN_CAPTURA"] . $totalapagar . "COCOTRA" . $fechavenlinea);

            $tknurl = $this->geturl_refrendo($request->No_placa, $request->No_serie);

            $tknurl = $tknurl["url"];

            $array_data_bitacora = [
                'accion' => 'se obtienen datos de la concesion en la opcion si tiene adeudos y se guardan en base de datos',
                "lineadecodigo" => '🚀 ~ file: pagoconcesion.php:508 pagoconcesion.GetPagos metodo getdatosconsesiones',
                "RFC" => $data["TB_OBJETO"]["RFC"],
                "No_Consesion" => $data["TB_OBJETO"]["CONCESION"],
                "No_serie" => $data["TB_OBJETO"]["SERIE"],
                "No_placa" => $data["TB_OBJETO"]["PLACA"],
                "grupo" => $data["TB_OBJETO"]["GRUPO"],
                "estatus" => $data["TB_OBJETO"]["ESTATUS"],
                "modalidad" => $data["TB_OBJETO"]["MODALIDAD"],
                "tpserv" => $data["TB_OBJETO"]["TIPO_SERV"],
                "nombconses" => $data["TB_OBJETO"]["NOMBRE"],
                "totalapagar" => $totalapagar,
                'convenio_bancos' => $data["TB_BANCOS"],
                "TBconceptos" => $data["TB_OBJETO"]["TB_CONCEPTOS"],
                "Linea_captura" => $data["ES_ORD_PAGO"]["LN_CAPTURA"],
                "fech_ven" => $data["ES_ORD_PAGO"]["FEC_VENCIMIENTO"],
                "cataseg" => $cataseg,
                'tkn' => $tkn,
                'fechavenlinea' => $fechavenlinea,
                'urladeudo' => $tknurl,

            ];
            BitacoraHelper::saveBitacoracont(BitacoraHelper::getIp(), "getpagos", "obtenerdatos", json_encode($array_data_bitacora), $request->No_Consesion);

            return view('pagoconcesion.GetPagos', [

                "RFC" => $data["TB_OBJETO"]["RFC"],
                "No_Consesion" => $data["TB_OBJETO"]["CONCESION"],
                "No_serie" => $data["TB_OBJETO"]["SERIE"],
                "No_placa" => $data["TB_OBJETO"]["PLACA"],
                "grupo" => $data["TB_OBJETO"]["GRUPO"],
                "estatus" => $data["TB_OBJETO"]["ESTATUS"],
                "modalidad" => $data["TB_OBJETO"]["MODALIDAD"],
                "tpserv" => $data["TB_OBJETO"]["TIPO_SERV"],
                "nombconses" => $data["TB_OBJETO"]["NOMBRE"],
                "totalapagar" => $totalapagar,
                'convenio_bancos' => $data["TB_BANCOS"],
                "TBconceptos" => $data["TB_OBJETO"]["TB_CONCEPTOS"],
                "Linea_captura" => $data["ES_ORD_PAGO"]["LN_CAPTURA"],
                "fech_ven" => $data["ES_ORD_PAGO"]["FEC_VENCIMIENTO"],
                "cataseg" => $cataseg,
                'tkn' => $tkn,

                'fechavenlinea' => $fechavenlinea,

                'urladeudo' => $tknurl,

            ]);

            return redirect()->back()->withErrors("intente mas tarde")->withInput($request->input());

        } else {
            return redirect()->back()->withErrors("intente mas tarde")->withInput($request->input());
            return "no entro a nada";
        }

    }

    public function desbloqueoconcesionupdate(Request $request)
    {

        $concesionblok = DB::table('spcl_concesiones_bloqueadas')
            ->where('no_concesion', '=', $request->No_Consesion)
            ->count();
        switch ($concesionblok) {

            case 0:
                $returnData = array(
                    'status' => 'error',
                    'icon' => 'success',
                    'title' => 'Aviso',
                    'message' => __('La concesión ingresada no existe'),
                  
                );
                return redirect()->back()->withErrors($returnData)->withInput($request->input());

                break;
            case 1:
                $concesionblok = DB::table('spcl_concesiones_bloqueadas')
                    ->where('no_concesion', '=', $request->No_Consesion)
                    ->limit(1)
                    ->update(['estatus' => 1]);

                if ($concesionblok) {
                    $returnData = array(
                        'status' => 'correcto',
                        'icon' => 'success',
                        'title' => 'Correcto',
                        'message' => __('Concesión desbloqueada'),
                    );
                    return redirect()->back()->withErrors($returnData)->withInput($request->input());

                } else {
                    $returnData = array(
                        'status' => 'error',
                        'icon' => 'success',
                        'title' => 'Aviso',
                        'message' => __('La concesión ya fue desbloqueada'),
                     
                    );
                    return redirect()->back()->withErrors($returnData)->withInput($request->input());

                }

                break;
            default:
                # code...
                break;
        }

        $returnData = array(
            'status' => 'error',
            'icon' => 'success',
            'title' => 'Aviso',
            // 'message' => $concesionblok,

            'message' => __('No existe la concesion ingresada'),
        );
        return redirect()->back()->withErrors($returnData)->withInput($request->input());

    }
    public function desbloqueoconcesion(Request $request)
    {
        return view("pagoconcesion.Desbloqueoconcesion", [
        ]);
    }

    public function geturl_refrendo(string $placa, string $serie)
    {

        $QRREFRENDO = \DB::table('configuracion')
            ->where('descripcion', '=', 'QrRefrendo')
            ->first();

        $datosQRREFRENDO = json_decode($QRREFRENDO->valor);
        $parametros = array("serie" => $serie, "placa" => $placa);
        $data = $this->client(
            // "https://sfa.michoacan.gob.mx/tvehicularqa/refrendo/validacion/ws/servicioEncripta.php?wsdl",
            // "gobdigital",
            // "gobdigitalMich.2022",
            //"M1cho@can",
            $datosQRREFRENDO->UrlRefrendo,
            $datosQRREFRENDO->UserRefrendo,
            $datosQRREFRENDO->PasswordRefrendo,
            $parametros,
            'urlQrRefrendo'
        );

        if ($data == null) {
            \Log::info("fallo el servicio de urlrefrendo");
            return array("url" => "Error del servicio");

            return 'error';

        }

        //  return array("url" => "Error del servicio");
        // \Log::info("si jalo  el servicio de urlrefrendo");
        // \Log::debug($data);
        // dd("w");
        return $data;

        // $this->user = "gobdigital";
        // $this->pass = "gobdigitalMich.2022";
        // $this->wsdl = "https://sfa.michoacan.gob.mx/tvehicularqa/refrendo/validacion/ws/servicioEncripta.php?wsdl";
        // try {
        //     // $options = array(
        //     //     'login' => $this->user,
        //     //     'password' => $this->pass,
        //     //     "soap_version" => "SOAP_1_2",
        //     //     'trace' => 1,
        //     // );
        //     $options =array(
        //         'login' => $this->user,
        //         'password' => $this->pass,
        //         'exceptions' => 1,
        //         'trace' => 1,
        //         'verify_peer' => 0,
        //         'allow_self_signed' => 1,
        //         'soap_version' => 'SOAP_1_1', //<-- note change here

        //     );
        //     \Log::info("variables del soap");
        //     \Log::debug($options);
        //     $this->client = new \SoapClient($this->wsdl, );
        //     return $this->client->urlQrRefrendo(array("serie" => $serie, "placa" => $placa));

        // } catch (Exception $e) {
        //     echo $e->getMessage();
        // }

    }

}
