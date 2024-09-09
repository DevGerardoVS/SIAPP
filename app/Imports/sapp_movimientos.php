<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Storage;
class sapp_movimientos implements ToCollection, WithProgressBar, SkipsEmptyRows, WithEvents, WithStartRow
{
    use Importable;

    public $file_content="insert into sapp_movimientos (ejercicio, mes, dia, clv_upp, clv_ur, clv_programa,
    clv_subprograma,clv_proyecto,fondo,partida,area_funcional,centro_gestor,clasificacion_administrativa,proyecto_obra,original_sapp,
     ampliacion,reduccion,traspaso,modificado,apartado,comprometido,comprometido_cp,devengado,devengado_cp,ejercido,ejercido_cp,pagado,disponible,created_user,updated_user,created_at) values \n";

     public function collection(Collection $rows)
     {
         $split_row = [];
         $upp = "";
         $ur = "";
         $clv_programa = "";
         $clv_subprograma = "";
         $clv_proyecto = "";
         $numeroMes = "";
         $proyecto_obra = "";
         foreach ($rows as $row) 
         {
             $split_row = explode('-', $row[1]);
             $upp = substr($row[5], 10, 3);
             $ur = substr($row[5], -2);
             $clv_programa = substr($row[4], 8, 2);
             $clv_subprograma = substr($row[4], 10, 3);
             $clv_proyecto = substr($row[4], -3);
             $numeroMes = $this->obtenerNumeroMes($split_row[1]);
             $proyecto_obra = !empty($row[6]) ? $row[6] : "000000";
             $this->file_content .= "(".$row[0].",".$numeroMes.",".$split_row[0].",'".$upp."','".$ur."','".$clv_programa."','".$clv_subprograma."','".$clv_proyecto."','".$row[2]."','".$row[3]."','".$row[4]."','".$row[5]."','".$row[7]."','".$proyecto_obra."',".$row[8].",".$row[9].",".$row[10].",".$row[11].",".$row[12].",".$row[13].",".$row[14].",".$row[15].",".$row[16].",".$row[17].",".$row[18].",".$row[19].",".$row[20].",".$row[21].","."'seeder'".","."'seeder'".",".'now()'."),\n";
            }
     }
    public function obtenerNumeroMes($mesAbreviado)
    {
        $meses = array(
            "ene" => 1,
            "feb" => 2,
            "mar" => 3,
            "abr" => 4,
            "may" => 5,
            "jun" => 6,
            "jul" => 7,
            "ago" => 8,
            "sep" => 9,
            "oct" => 10,
            "nov" => 11,
            "dic" => 12,
        );

        // return $meses[strtolower($mesAbreviado)];
        return isset($meses[strtolower($mesAbreviado)]) ? $meses[strtolower($mesAbreviado)] : 'A';

    }

    public function startRow(): int
    {
        return 2;
    }

    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            AfterImport::class => function(AfterImport $event) {
                $creator = $event->reader->getProperties()->getCreator();
                //$this->file_content .= "}\n}\n";
                //Storage::disk('public')->put("PruebaSeeder.php", $this->file_content);
                Storage::disk('public')->put("agosto.sql", $this->file_content);
            },
			
                        
        ];
    }
}
