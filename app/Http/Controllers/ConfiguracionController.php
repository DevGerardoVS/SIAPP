<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Helpers\RegistroModulosHelper;
use App\Helpers\QueryHelper;
use Carbon\Carbon;

class ConfiguracionController extends Controller{
    /**
     * Constructor de la clase controlador
     * @version 1.0
     * @author Luis Fernando Zavala
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Función para mostrar la vista
     */
    public function index(){
        return view('configuracion.index');
    }
}