<?php 	

    use App\Http\Controllers\Calendarizacion\ClavePreController;
	use App\Http\Controllers\Calendarizacion\MetasController;
	use App\Http\Controllers\Calendarizacion\TechosController;
	use App\Http\Controllers\Calendarizacion\CalendarizacionCargaMasivaController;

	include('metas.php');

	Route::controller(TechosController::class)->group(function () {
        Route::get('/calendarizacion/techos', 'getIndex')->name('index_techos');
        Route::get('/calendarizacion/techos/get-techos', 'getTechos')->name('getTechos');
        Route::get('/calendarizacion/techos/get-fondos', 'getFondos')->name('getFondos');
        Route::post('/calendarizacion/techos/add-techo', 'addTecho')->name('addTecho');
    });
	Route::controller(ClavePreController::class)->group(function () {
		Route::get('/calendarizacion/claves', 'getPanel');
		Route::get('/calendarizacion/claves-get', 'getClaves');
		Route::get('/cat-regiones', 'getRegiones');
		Route::get('/cat-municipios/{id?}', 'getMunicipios');
		Route::get('/cat-localidad/{id?}', 'getLocalidades');		
		Route::get('/cat-upp', 'getUpp');
		Route::get('/cat-unidad-responsable/{id?}', 'getUnidadesResponsables');
		Route::get('/cat-programa-presupuestario/{id?}', 'getProgramaPresupuestarios');
		Route::get('/cat-subprograma-presupuesto/{id?}', 'getSubProgramas');
		Route::get('/cat-proyecyo/{id?}', 'getProyectos');
		
		
	});

	Route::controller(CalendarizacionCargaMasivaController::class)->group(function () {
		Route::get('/calendarizacion/get-plantilla', 'getExcel')->name('getplantilla');
		Route::post('/calendarizacion/load-Data-Plantilla', 'loadDataPlantilla')->name('load_data_plantilla');

    });
	

?>

	
	
