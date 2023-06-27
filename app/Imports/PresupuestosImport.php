<?php

namespace App\Imports;

use App\Models\ProgramacionPresupuesto;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Storage;

class PresupuestosImport implements ToModel, WithProgressBar, SkipsEmptyRows, WithEvents
{
    use Importable;

    public $file_content="<?php\n
    namespace Database\Seeders;\n
    use Illuminate\Database\Console\Seeds\WithoutModelEvents;\n
    use Illuminate\Support\Facades\DB;\n
    use Illuminate\Database\Seeder;\n
    class PruebaSeeder extends Seeder\n
    {\n
        /**\n
         * Run the database seeds.\n
         *\n
         * @return void\n
         */\n
        public function run()\n
        {\n";
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $tipo;
        $row[17]=='UUU' ? $tipo = 'RH' : $tipo = 'Operativo';
        
        $this->file_content .= "DB::unprepared(\"INSERT INTO `programacion_presupuesto` (clasificacion_administrativa,entidad_federativa,region,municipio,localidad, upp, subsecretaria, ur, finalidad, funcion, subfuncion, eje, linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio, etiquetado, fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra,ejercicio,enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre, noviembre,diciembre,total,estado,tipo,created_user) values (".
        $row[1].",".$row[2].",".$row[3].",".$row[4].",".$row[5].",'".$row[6]."',".$row[7].",".$row[8].",".$row[9].",".$row[10].",".$row[11].",".$row[12].",'".$row[13]."','".$row[14]."','".$row[15]."','".$row[16]."','".$row[17]."','".$row[18]."','".$row[19]."',".$row[34].",".$row[21].",".$row[22].",".$row[23].",".$row[24].",".$row[25].",'".$row[26]."',".$row[27].",".$row[31].","."2023".",".$row[36].",".$row[37].",".$row[38].",".$row[39].",".$row[40].",".$row[41].",".$row[42].",".$row[43].",".$row[44].",".$row[45].",".$row[46].",".$row[47].",".$row[35].", 0 ,'".$tipo."', 'SEEDER'".")\");\n";
        //$this->output->title('row '.$row[0]);
        /*return new ProgramacionPresupuesto([
            //
            'clasificacion_administrativa' => $row[1], 
            'entidad_federativa' => $row[2],
            'region' => $row[3],
            'municipio' => $row[4],
            'localidad' => $row[5],
            'upp' => $row[6],
            'subsecretaria' => $row[7],
            'ur' => $row[8],
            'finalidad' => $row[9],
            'funcion' => $row[10],
            'subfuncion' => $row[11],
            'eje' => $row[12],
            'linea_accion' => $row[13],
            'programa_sectorial' => $row[14],
            'tipologia_conac' => $row[15],
            'programa_presupuestario' => $row[16],
            'subprograma_presupuestario' => $row[17],
            'proyecto_presupuestario' => $row[18],
            'periodo_presupuestal' => $row[19],
            'posicion_presupuestaria' => $row[34],
            'tipo_gasto' => $row[21],
            'anio' => $row[22],
            'etiquetado' => $row[23],
            'fuente_financiamiento' => $row[24],
            'ramo' => $row[25],
            'fondo_ramo' => $row[26],
            'capital' => $row[27],
            'proyecto_obra' => $row[31],
            'ejercicio' => 2023,
            'enero' => $row[36],
            'febrero' => $row[37],
            'marzo' => $row[38],
            'abril' => $row[39],
            'mayo' => $row[40],
            'junio' => $row[41],
            'julio' => $row[42],
            'agosto' => $row[43],
            'septiembre' => $row[44],
            'octubre' => $row[45],
            'noviembre' => $row[46],
            'diciembre' => $row[47],
            'total' => $row[35],
            'estado' => 0,
            'tipo' => $tipo,
            'created_user' => 'SEEDER',
        ]);Storage::disk('public')->put("PruebaSeeder", $this->file_content);*/
    }

    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            AfterImport::class => function(AfterImport $event) {
                $creator = $event->reader->getProperties()->getCreator();
                $this->file_content .= "}\n}\n";
                Storage::disk('public')->put("PruebaSeeder.php", $this->file_content);
            },
			
                        
        ];
    }
}
