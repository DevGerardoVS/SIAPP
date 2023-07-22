<?php 	

    use App\Http\Controllers\Calendarizacion\ClavePreController;
	use App\Http\Controllers\Calendarizacion\MetasController;
	use App\Http\Controllers\Calendarizacion\TechosController;
	use App\Http\Controllers\Calendarizacion\CalendarizacionCargaMasivaController;

	include('metas.php');

	Route::controller(TechosController::class)->group(function () {
        Route::get('/calendarizacion/techos', 'getIndex')->name('index_techos');
        Route::post('/calendarizacion/techos/get-techos', 'getTechos')->name('getTechos');
        Route::get('/calendarizacion/techos/get-fondos', 'getFondos')->name('getFondos');
        Route::post('/calendarizacion/techos/add-techo', 'addTecho')->name('addTecho');
        Route::get('/plantillaCargaTechos', 'exportView');
        Route::get('/exportPlantilla', 'exportPlantilla')->name('exportPlantilla');
        Route::post('/import-Plantilla', 'importPlantilla')->name('importPlantilla');
        Route::get('/calendarizacion/techos/export-excel', 'exportExcel')->name('exportExcel');
        Route::get('/calendarizacion/techos/export-pdf', 'exportPDF')->name('exportPDF');
        Route::get('/calendarizacion/techos/export-presupuestos', 'exportPresupuestos')->name('exportPresupuestos');
        Route::post('/calendarizacion/techos/eliminar', 'eliminar')->name('eliminar');
    });

	Route::controller(ClavePreController::class)->group(function () {
		Route::get('/calendarizacion/claves', 'getPanel');
		Route::get('/calendarizacion-claves-create/{ejercicio?}', 'getCreate');
		Route::post('/calendarizacion-claves-get', 'getClaves');
		Route::get('/cat-regiones', 'getRegiones');
		Route::get('/cat-municipios/{id?}', 'getMunicipios');
		Route::get('/cat-localidad/{id?}', 'getLocalidades');		
		Route::get('/cat-upp/{ejercicio?}', 'getUpp');
		Route::get('/cat-unidad-responsable/{id?}/{ejercicio?}', 'getUnidadesResponsables');
		Route::get('/cat-programa-presupuestario/{upp?}/{id?}/{ejercicio?}', 'getProgramaPresupuestarios');
		Route::get('/cat-subprograma-presupuesto/{ur?}/{id?}/{upp?}/{ejercicio?}', 'getSubProgramas');
		Route::get('/cat-proyecyo/{programa?}/{id?}/{upp?}/{ur?}/{ejercicio?}', 'getProyectos');
		Route::get('/cat-linea-accion/{uppId?}/{id?}/{ejercicio?}', 'getLineaAccion');
		Route::get('/get-presupuesto-asignado/{ejercicio?}/{upp?}', 'getPresupuestoAsignado');
		Route::get('/calendarizacion-claves-presupuesto-fondo/{ejercicio?}/{clvUpp?}', 'getPanelPresupuestoFondo');
		Route::post('/calendarizacion-eliminar-clave', 'postEliminarClave');
		Route::post('/calendarizacion-guardar-clave', 'postGuardarClave');
		Route::get('/calendarizacion/get-calendarizacion-panel', 'getPanelCalendarizacion');
		Route::get('/cat-subSecretaria/{upp?}/{ur?}/{ejercicio?}', 'getSubSecretaria');
		Route::get('/cat-area-funcional/{uppId?}/{id?}/{ejercicio?}', 'getAreaFuncional');
		Route::get('/cat-partidas/{clasificacion?}', 'getPartidas');
		Route::get('/cat-fondos/{id?}/{subP?}/{ejercicio?}', 'getFondos');
		Route::get('/cat-clasificacion-administrativa/{upp?}/{ur?}', 'getClasificacionAdmin');
		Route::get('/presupuesto-upp-asignado/{upp?}/{fonfo?}/{subPrograma?}/{ejercicio?}', 'getPresupuestoPorUpp');
		Route::get('/presupuesto-upp-asignado-edit/{upp?}/{fonfo?}/{subPrograma?}/{ejercicio?}/{id?}', 'getPresupuestoPorUppEdit');
		Route::get('/ver-detalle/{clave?}/{anioFondo?}', 'getConceptosClave')->name('detalle');
		Route::get('/clave-update/{id?}', 'getPanelUpdate');
		Route::post('/calendarizacion-editar-clave', 'postEditarClave');
		Route::post('/calendarizacion-confirmar-claves', 'postConfirmarClaves');
		Route::get('/calendarizacion-get-sector/{clave?}', 'getSector');
		Route::get('/cat-obras/{val?}', 'getObras');
		Route::get('/get-ejercicios','getEjercicios');
		
	});

	Route::controller(CalendarizacionCargaMasivaController::class)->group(function () {
		Route::get('/calendarizacion/get-plantilla', 'getExcel')->name('getplantilla');
		Route::post('/calendarizacion/download-errors-excel', 'DownloadErrors')->name('SaveErrors');
		Route::post('/calendarizacion/load-Data-Plantilla', 'loadDataPlantilla')->name('load_data_plantilla');


    });
	
/* Ricardo
 */
?>

	
	
