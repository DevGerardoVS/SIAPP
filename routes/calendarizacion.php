<?php 	

    use App\Http\Controllers\Calendarizacion\ClavePreController;
	use App\Http\Controllers\Calendarización\MetasController;
	use App\Http\Controllers\Calendarización\TechosController;
	use App\Http\Controllers\Calendarización\CalendarizacionCargaMasivaController;

	Route::controller(MetasController::class)->group(function () {
		Route::get('/calendarizacion/metas', 'getIndex')->name('index_metas');
	 	Route::get('/calendarizacion/data', 'getMetas')->name('getMetas');
        Route::get('/calendarizacion/proyecto', 'getProyecto')->name('proyecto');
	});

    Route::controller(TechosController::class)->group(function () {
        Route::get('/calendarizacion/techos', 'getIndex')->name('index_techos');
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
		Route::get('/cat-linea-accion/{id?}', 'getLineaAccion');
		Route::get('/cat-partidas/{id?}', 'getPartidas');
		
		
		
		
	});

	Route::controller(CalendarizacionCargaMasivaController::class)->group(function () {
		Route::get('/calendarizacion/get-plantilla', 'getExcel')->name('get-plantilla');
    });
	

?>