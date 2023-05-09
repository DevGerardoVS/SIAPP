<?php

namespace App\Exports;

use App\Models\Bitacora;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class BitacoraExport implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths
{
    /**
* @return \Illuminate\Support\Collection
    
*/
    protected $request;
    
    function __construct($request){
        $this->request = $request;
    }

    public function collection()
    {
        $fecha_inicio = $this->request-> input('anio_filter');
        $fecha_fin = $this->request-> input('anio_filter_fin');
        $usua = $this->request->input('usuario_filter');
        $accion = $this->request->input('accion_filter');
        $array_where=[];

        if( $accion != null ){
            array_push($array_where,['bitacora.accion','=',$accion]);
        }
        if($usua!=null){
            array_push($array_where,['bitacora.usuario','=',$usua]);
        }
        if ($fecha_inicio != null) {
            array_push($array_where, ['bitacora.created_at', '>=', $fecha_inicio]);
        }
        if ($fecha_fin != null) {
            array_push($array_where, ['bitacora.created_at', '<=', $fecha_fin]);
        }

        $catalogo = Bitacora::select('bitacora.usuario',
            'bitacora.host','bitacora.modulo','bitacora.accion',
            'bitacora.datos',
            'bitacora.created_at')
            ->where($array_where)
            ->orderBy('bitacora.created_at','desc')
            ->get();

        foreach($catalogo as $k=>$d){
            $format_date= date("d/m/Y H:i:s", strtotime($d->created_at));
            $new_array = array('created_at'=>$format_date);
            $d = collect(array_replace($d->toArray(),$new_array));
            $old_collect = $catalogo->toArray();
            $new_array_collect = array($k=>$d);
            $catalogo = collect(array_replace($old_collect,$new_array_collect));
        }
        
        return $catalogo;
    }

    /**
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    public function headings(): array
    {
        return ["Usuario","Host","Modulo", "Accion", "Datos","Fecha de creacion"];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 15,
            'C' => 25,
            'D' => 10,
            'E' => 15,
            'F' => 20
        ];
    }
    
    
    
    
   
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

}
