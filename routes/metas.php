<?php 	
	use App\Http\Controllers\Calendarizacion\MetasController;

	Route::controller(MetasController::class)->group(function () {
		Route::get('/calendarizacion/metas', 'getIndex')->name('index_metas');
		Route::get('/calendarizacion/pdf/{upp?}', 'pdfView');
		Route::post('/calendarizacion/data/', 'getMetasP')->name('metasP');
		Route::get('/actividades/data/{upp?}', 'getActiv')->name('actividades');
		Route::get('/actividades/proyecto_calendario', 'proyExcel')->name('ProyExcel');
		Route::get('/actividades/exportExcel/{upp?}', 'exportExcel')->name('ExportExcel');
		Route::get('/actividades/exportPdf/{upp?}',  'exportPdf')->name('exportPdf');
		Route::post('/actividades/import', 'importPlantilla');
		Route::get('/actividades/jasper/{upp?}', 'downloadActividades')->name('exportjasper');
        Route::get('/calendarizacion/proyecto', 'getProyecto')->name('proyecto');
		Route::get('/calendarizacion/selects', 'getSelects');
		Route::get('/calendarizacion/urs/{upp?}', 'getUrs');
		Route::get('/calendarizacion/fondos/{clave?}', 'getFyA');
		Route::get('/calendarizacion/upps', 'getUpps');
		Route::get('/calendarizacion/subprog/{ur?}', 'getSubProg');
		Route::post('/calendarizacion/create', 'createMeta');
		Route::post('/calendarizacion/detelet', 'deleteMeta');
		Route::get('/calendarizacion/update/{id?}','updateMeta');


		
		Route::post('/calendarizacion-metas-reporte', 'descargaReporteFirma');
	});
?>

	
	
