<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ConfiguracionSAPSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::unprepared('INSERT INTO configuracion (`descripcion`,`valor`) 
        // VALUES("QrRefrendo","{\"UrlQrRefrendo\":\'https://sfa.michoacan.gob.mx/tvehicularqa/refrendo/validacion/ws/servicioEncripta.php?wsdl\',\"UserQrRefrendo\":\"gobdigital\",\"PasswordQrRefrendo\":\"gobdigitalMich.2022\"}" )');

        // DB::unprepared('INSERT INTO configuracion (`descripcion`,`valor`) 
        // VALUES("SapConceptosConcesion","{\"UrlRefrendo\":\'http://gemnwpiq.michoacan.gob.mx:51000/dir/wsdl?p=ic/3a3aa38db3c530178d976137b09a1d77\',\"UserRefrendo\":\"PI_CONNECTQ\",\"PasswordRefrendo\":\"PIQ_M1ch_23\"}" )');

        DB::unprepared('INSERT INTO configuracion (`descripcion`,`valor`) VALUES("QrRefrendo",\'{"UrlRefrendo": "https://sfa.michoacan.gob.mx/tvehicularqa/refrendo/validacion/ws/servicioEncripta.php?wsdl", "UserRefrendo": "gobdigital\", "PasswordRefrendo": "gobdigitalMich.2022"}\' )');
        DB::unprepared('INSERT INTO configuracion (`descripcion`,`valor`) VALUES("SapConceptosConcesion",\'{"UrlRefrendo": "http://gemnwpiq.michoacan.gob.mx:51000/dir/wsdl?p=ic/3a3aa38db3c530178d976137b09a1d77", "UserRefrendo": "PI_CONNECTQ\", "PasswordRefrendo": "PIQ_M1ch_23"}\' )');



    //     DB::unprepared('INSERT INTO configuracion (`descripcion`,`valor`) VALUES("años","{\"años\": [\"2022\", \"2021\"]}" )');
    
    //     DB::unprepared('INSERT INTO configuracion (`descripcion`,`valor`) 
    //     VALUES("UrlQrRefrendo","https://sfa.michoacan.gob.mx/tvehicularqa/refrendo/validacion/ws/servicioEncripta.php?wsdl" )');
    
    //      DB::unprepared('INSERT INTO configuracion (`descripcion`,`valor`) 
    //     VALUES("UrlQrRefrendo","https://sfa.michoacan.gob.mx/tvehicularqa/refrendo/validacion/ws/servicioEncripta.php?wsdl" )');
    
    // DB::unprepared('INSERT INTO configuracion (`descripcion`,`valor`) 
    // VALUES("UrlQrRefrendo","https://sfa.michoacan.gob.mx/tvehicularqa/refrendo/validacion/ws/servicioEncripta.php?wsdl" )');

    // DB::unprepared('INSERT INTO configuracion (`descripcion`,`valor`) 
    // VALUES("UserQrRefrendo","gobdigital" )');

    // DB::unprepared('INSERT INTO configuracion (`descripcion`,`valor`) 
    // VALUES("PasswordQrRefrendo","gobdigitalMich.2022" )');

    // DB::unprepared('INSERT INTO configuracion (`descripcion`,`valor`) 
    // VALUES("PasswordQrRefrendo","gobdigitalMich.2022" )');
    
    }
}
