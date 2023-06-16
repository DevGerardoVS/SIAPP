<?php 	

    use App\Http\Controllers\Calendarizacion\ClavePreController;
	use App\Http\Controllers\Calendarización\MetasController;
	use App\Http\Controllers\Calendarización\TechosController;
	use App\Http\Controllers\Calendarización\CalendarizacionCargaMasivaController;

	Route::controller(MetasController::class)->group(function () {
		Route::get('/calendarizacion/metas', 'getIndex')->name('index_metas');
	 	Route::post('/calendarizacion/data', 'getMetasP')->name('getMetasP');
        Route::get('/calendarizacion/proyecto/{ur?}', 'getProyecto')->name('proyecto');
		Route::get('/nombres/{id?}', 'getNames');
		Route::get('/calendarizacion/selects', 'getSelects');
		Route::get('/calendarizacion/urs', 'getUrs');
		Route::get('/calendarizacion/programas/{ur?}', 'getProgramas');
		Route::get('/calendarizacion/subprog/{ur?}', 'getSubProg');
		Route::post('/calendarizacion/create', 'createMeta');
		Route::post('/calendarizacion/detelet', 'deleteMeta');
		Route::get('/calendarizacion/update/{id?}','updateMeta');
		Route::get('/calendarizacion/metasXproyecto', 'getMetasXp')->name('proyectos');
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
		
		
	});

	Route::controller(CalendarizacionCargaMasivaController::class)->group(function () {
		Route::get('/calendarizacion/get-plantilla', 'getExcel')->name('getplantilla');
		Route::post('/calendarizacion/load-Data-Plantilla', 'loadDataPlantilla')->name('load_data_plantilla');

    });
	

?>

	
	
