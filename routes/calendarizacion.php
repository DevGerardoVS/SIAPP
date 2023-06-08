<?php 	

    use App\Http\Controllers\Calendarizacion\ClavePreController;
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

	
	

?>