<?php 	
	use App\Http\Controllers\Calendarizacion\MetasController;

	Route::controller(MetasController::class)->group(function () {
		Route::get('/calendarizacion/metas', 'getIndex')->name('index_metas');
		Route::get('/calendarizacion/pdf/{upp?}/{anio?}', 'pdfView');
		Route::post('/calendarizacion/data/', 'getMetasP')->name('metasP');
		Route::get('/actividades/data/{upp?}/{anio?}', 'getActiv')->name('actividades');
		Route::get('/actividades/proyecto_calendario', 'proyExcel')->name('ProyExcel');
		Route::get('/actividades/exportExcel/{upp?}/{anio?}', 'exportExcel');
		Route::get('/actividades/exportPdf/{upp?}/{anio?}',  'exportPdf');
		Route::post('/actividades/import', 'importPlantilla');
		Route::get('/actividades/jasper/{upp?}/{anio?}', 'downloadActividades')->name('exportjasper');
        Route::get('/calendarizacion/proyecto', 'getProyecto')->name('proyecto');
		Route::get('/calendarizacion/selects', 'getSelects');
		Route::get('/calendarizacion/tcalendario/{upp?}', 'getTcalendar');
		Route::get('/calendarizacion/urs/{upp?}', 'getUrs');
		Route::get('/calendarizacion/fondos/{area?}/{enti?}', 'getFyA');
		Route::get('/calendarizacion/upps', 'getUpps');
		Route::get('/calendarizacion/subprog/{ur?}', 'getSubProg');
		Route::post('/calendarizacion/create', 'createMeta');
		Route::post('/calendarizacion/put', 'putMeta');
		Route::post('/calendarizacion/detelet', 'deleteMeta');
		Route::get('/calendarizacion/update/{id?}','updateMeta');
		Route::get('/calendarizacion/check/{upp?}','checkCombination');
		Route::post('/calendarizacion-metas-reporte', 'descargaReporteFirma');
		Route::get('/actividades/jasper-metas/{upp?}/{anio?}', 'jasperMetas');
	});
?>

	
	
