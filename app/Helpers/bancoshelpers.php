<?php // Code within app\Helpers\BitacoraHelper.php

namespace App\Helpers;

class bancoshelpers
{

    public static function getlistabancos($TB_BANCOS)
    {
        foreach ($TB_BANCOS as $banco) {

           
            
            switch ($banco->BANCO) {



                case 'AFIRME':
                    # code...
                   
                    $banco->imagen="AFIRME.png";
                    break;
                case 'BANCO AZTECA':
                    $banco->imagen="BANCO AZTECA.png";
                    break;
                    case 'BANCOMER':
                    # code...
                    $banco->imagen="BBVA.png";
                    break;
                case 'BAJIO':
                    # code...
                    $banco->imagen="BAJIO.png";
                    break;
                case 'HSBC':
                    # code...
                    $banco->imagen="HSBC.svg";
                    break;
                case 'SANTANDER':
                    $banco->imagen="SANTANDER.png";
                   
                    # code...
                    break;


               
                default:
                    # code...
                    break;
            }
        }
        return (array)($TB_BANCOS);
    }

}
