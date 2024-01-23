<?php

namespace App\Imports;

use App\Models\ProgramacionPresupuesto;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Storage;

class ProgramacionPresupuestosImport implements ToModel, WithProgressBar, SkipsEmptyRows, WithEvents, WithCalculatedFormulas
{
    use Importable;

        public $file_content="<?php\n
        namespace Database\Seeders;\n
        use Illuminate\Database\Console\Seeds\WithoutModelEvents;\n
        use Illuminate\Support\Facades\DB;\n
        use Illuminate\Database\Seeder;\n
        class ProgramacionPresupuestoHistSeeder extends Seeder\n
        {\n
            /**\n
             * Run the database seeds.\n
             *\n
             * @return void\n
             */\n
            public function run()\n
            {\n";

    //public $file_content="insert into adm_users (clv_upp, id_grupo, nombre, p_apellido, s_apellido, email, celular, username) values \n";


    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $tipo;
        $row[17]=='UUU' ? $tipo = 'RH' : $tipo = 'Operativo';
        
        $this->file_content .= "DB::unprepared(\"INSERT INTO `programacion_presupuesto_hist` (version,clasificacion_administrativa,entidad_federativa,region,municipio,localidad, upp, subsecretaria, ur, finalidad, funcion, subfuncion, eje, linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio, etiquetado, fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra,ejercicio,enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre, noviembre,diciembre,total,estado,tipo,created_user) values (".
        "1,".$row[1].",".$row[2].",".$row[3].",".$row[4].",".$row[5].",'".$row[6]."',".$row[7].",".$row[8].",".$row[9].",".$row[10].",".$row[11].",".$row[12].",'".$row[13]."','".$row[14]."','".$row[15]."','".$row[16]."','".$row[17]."','".$row[18]."','".$row[19]."',".$row[34].",".$row[21].",".$row[22].",".$row[23].",".$row[24].",".$row[25].",'".$row[26]."',".$row[27].",".$row[31].","."2023".",".$row[36].",".$row[37].",".$row[38].",".$row[39].",".$row[40].",".$row[41].",".$row[42].",".$row[43].",".$row[44].",".$row[45].",".$row[46].",".$row[47].",".$row[35].", 0 ,'".$tipo."', 'SEEDER'".")\");\n";
        
        //$this->file_content .= "('".$row[0]."',".'4'.",'".$row[3]."','".$row[4]."','".$row[5]."','".$row[7]."','".$row[6]."','".$row[2]."'),\n";

    }

    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            AfterImport::class => function(AfterImport $event) {
                $creator = $event->reader->getProperties()->getCreator();
                $this->file_content .= "}\n}\n";
                Storage::disk('public')->put("ProgramacionPresupuestoHistSeeder.php", $this->file_content);
                //Storage::disk('public')->put("users.sql", $this->file_content);
            },
			
                        
        ];
    }
}
