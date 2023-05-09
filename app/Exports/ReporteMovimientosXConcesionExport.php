<?php

namespace App\Exports;

use App\Helpers\ReportesHelper;
use App\Models\Aseguradoras;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ReporteMovimientosXConcesionExport implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths
{
    protected $request;

    function __construct($request) { 
        $this->request = $request;
    }

    public function collection() {
        $data = ReportesHelper::getReporteMovimientosXConcesionQuery($this->request,true);
        $dataSet = array();
        foreach ($data as $d) {
            $json = json_decode($d->datos);
            $no_concesion = "";
            $aseguradora = "";
            $id_aseguradora = "";
            $no_poliza = "";
            $vencimiento = "";
            
            switch($d->modulo){
                case 'Detalle de pago': case 'Poliza seguro':
                    $datos = $json->nuevo;
                    $no_concesion = $datos->no_concesion;
                    $no_poliza = $datos->no_poliza;
                    $id_aseguradora = isset($datos->id_aseguradora) ? $datos->id_aseguradora : '';
                    if($id_aseguradora == 11){
                        $aseguradora = $datos->otro_aseguradora;
                    }
                    $vencimiento = isset($datos->fecha_vencimiento) ? date("d/m/Y", strtotime($datos->fecha_vencimiento)) : '';
                    break;
                case 'Nueva poliza seguro': case 'Polizas seguro historico':
                    $datos = $json->nuevo;
                    $no_poliza = $datos->no_poliza;
                    $id_aseguradora = $datos->id_aseguradora;
                    if($id_aseguradora == 11){
                        $aseguradora = $datos->otro_aseguradora;
                    }
                    $vencimiento = isset($datos->fecha_vencimiento) ? date("d/m/Y", strtotime($datos->fecha_vencimiento)) : '';
                    $no_concesion = isset($datos->update_detalle_concesion) ? $datos->update_detalle_concesion->no_concesion : '';
                    break;
                /*case 'getpagos':
                    $no_concesion = $json->no_concesion;
                    break;*/
            }

            if($aseguradora == "" && $id_aseguradora != ""){
                $qaseguradora = Aseguradoras::select('id','nombre')->where('id',$id_aseguradora)->first();
                $aseguradora = $qaseguradora->nombre;
            }

            $dts = 'Aseguradora: '.$aseguradora.'; Num. de póliza: '.$no_poliza.'; Vencimiento: '.$vencimiento.';';

            $ds = array($no_concesion, $d->usuario, $d->accion, $dts, date("d/m/Y H:i:s", strtotime($d->created_at)));
            $dataSet[] = $ds;
        }
        return collect($dataSet);
    }

    /**
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    public function headings(): array {
        return ["Número de concesión", "Usuario", "Movimiento", "Datos", "Fecha y hora"];
    }

    public function columnWidths(): array {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 15,
            'D' => 15,
            'E' => 20,
        ];
    }
}