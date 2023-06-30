<?php 	

    use App\Http\Controllers\Calendarizacion\ClavePreController;
	use App\Http\Controllers\Calendarizacion\MetasController;
	use App\Http\Controllers\Calendarizacion\TechosController;
	use App\Http\Controllers\Calendarizacion\CalendarizacionCargaMasivaController;

	Route::controller(MetasController::class)->group(function () {
		Route::get('/calendarizacion/metas', 'getIndex')->name('index_metas');
		Route::get('/calendarizacion/pdf', 'pdfView');
		Route::post('/calendarizacion/data/', 'getMetasP')->name('metasP');
		Route::get('/actividades/data', 'getActiv')->name('actividades');
		Route::get('/actividades/proyecto_calendario', 'proyExcel')->name('ProyExcel');
		Route::get('/actividades/exportExcel', 'exportExcel')->name('ExportExcel');
		Route::get('/actividades/exportPdf', 'exportPdf')->name('exportPdf');
		Route::post('/actividades/import', 'importPlantilla');
		Route::get('/actividades/jasper', 'downloadActividades')->name('exportjasper');
        Route::get('/calendarizacion/proyecto', 'getProyecto')->name('proyecto');
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
        Route::get('/calendarizacion/techos/get-techos', 'getTechos')->name('getTechos');
        Route::get('/calendarizacion/techos/get-fondos', 'getFondos')->name('getFondos');
        Route::post('/calendarizacion/techos/add-techo', 'addTecho')->name('addTecho');
    });
	Route::controller(ClavePreController::class)->group(function () {
		Route::get('/calendarizacion/claves', 'getPanel');
		Route::get('/calendarizacion-claves-create/{ejercicio?}', 'getCreate');
		Route::get('/calendarizacion/claves-get/{ejercicio?}', 'getClaves');
		Route::get('/cat-regiones', 'getRegiones');
		Route::get('/cat-municipios/{id?}', 'getMunicipios');
		Route::get('/cat-localidad/{id?}', 'getLocalidades');		
		Route::get('/cat-upp', 'getUpp');
		Route::get('/cat-unidad-responsable/{id?}', 'getUnidadesResponsables');
		Route::get('/cat-programa-presupuestario/{upp?}/{id?}', 'getProgramaPresupuestarios');
		Route::get('/cat-subprograma-presupuesto/{ur?}/{id?}/{upp?}', 'getSubProgramas');
		Route::get('/cat-proyecyo/{programa?}/{id?}', 'getProyectos');
		Route::get('/cat-linea-accion/{uppId?}/{id?}', 'getLineaAccion');
		Route::get('/get-presupuesto-asignado/{ejercicio?}', 'getPresupuestoAsignado');
		Route::get('/calendarizacion-claves-presupuesto-fondo/{ejercicio?}', 'getPanelPresupuestoFondo');
		Route::post('/calendarizacion-eliminar-clave', 'postEliminarClave');
		Route::post('/calendarizacion-guardar-clave', 'postGuardarClave');
		Route::get('/calendarizacion/get-calendarizacion-panel', 'getPanelCalendarizacion');
		Route::get('/cat-subSecretaria/{upp?}/{ur?}', 'getSubSecretaria');
		Route::get('/cat-area-funcional/{uppId?}/{id?}', 'getAreaFuncional');
		Route::get('/cat-partidas', 'getPartidas');
		Route::get('/cat-fondos/{id?}/{subP?}/{ejercicio?}', 'getFondos');
		Route::get('/cat-clasificacion-administrativa/{upp?}/{ur?}', 'getClasificacionAdmin');
		Route::get('/presupuesto-upp-asignado/{upp?}/{fonfo?}/{subPrograma?}/{ejercicio?}', 'getPresupuestoPorUpp');
		Route::get('/ver-detalle/{clave?}', 'getConceptosClave')->name('detalle');
		Route::get('/clave-update/{id?}', 'getPanelUpdate');
		Route::post('/calendarizacion-editar-clave', 'postEditarClave');
		Route::post('/calendarizacion-confirmar-claves', 'postConfirmarClaves');
		Route::get('/calendarizacion-get-sector/{clave?}', 'getSector');
		

		

		
		
		
		
		
	});

	Route::controller(CalendarizacionCargaMasivaController::class)->group(function () {
		Route::get('/calendarizacion/get-plantilla', 'getExcel')->name('getplantilla');
		Route::post('/calendarizacion/load-Data-Plantilla', 'loadDataPlantilla')->name('load_data_plantilla');
/* 		Route::post('/calendarizacion/load-Data-Plantilla', 'getObra')->name('getObra'); */

    });
	

?>

	
	
