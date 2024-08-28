<?php 	
	use App\Http\Controllers\Calendarizacion\MetasController;
	use App\Http\Controllers\Calendarizacion\MetasDelController;

	Route::controller(MetasController::class)->group(function () {
		Route::get('/calendarizacion/metas', 'getIndex')->name('index_metas');
		Route::get('/calendarizacion/metas-delegacion', 'getIndex')->name('index_metas');
		Route::get('/calendarizacion/pdf/{upp?}/{anio?}', 'pdfView');
		Route::get('/calendarizacion/data/{upp_filter?}/{ur_filter?}', 'getMetasP');
		Route::get('/actividades/data/{upp?}/{anio?}', 'getActiv')->name('actividades');
		Route::get('/actividades/proyecto_calendario/{upp?}', 'proyExcel')->name('ProyExcel');
		Route::get('/actividades/exportExcel/{upp?}/{anio?}', 'exportExcel');
		Route::get('/actividades/exportPdf/{upp?}/{anio?}',  'exportPdf');
		Route::post('/actividades/import', 'importPlantilla');
		Route::get('/actividades/jasper/{upp?}/{anio?}/{tipo?}', 'downloadActividades')->name('exportjasper');
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
		Route::get('/actividades/jasper-metas/{upp?}/{anio?}/{tipo?}', 'jasperMetas');
		Route::get('/actividades/cierre-metas/{upp?}', 'checkGoals');
		Route::get('/actividades/rev-confirmar-metas/{upp?}/{anio?}', 'cmetas');
		Route::get('/agregar-actividades/confirmacion-metas-upp/{upp?}/{anio?}', 'cmetasadd');
		Route::get('/actividades/rev-confirmar-metas-upp/{upp?}/{anio?}', 'cmetasUpp');
		Route::get('/actividades/confirmar-metas/{upp?}/{anio?}', 'confirmar');
		Route::get('/actividades/desconfirmar-metas/{upp?}/{anio?}', 'desconfirmar');
		Route::get('/actividades/anios-metas', 'getAnios');
		Route::get('/actividades/meses-activos/{mir?}/{fondo?}', 'getMeses');
		Route::get('/actividades/meses/error/{upp?}/{anio?}', 'exportExcelErr')->name('exportError');
		Route::get('/prueba-total/{anio?}', 'exportExcelErrTotal');
		Route::get('/carga-masiva/manual-usuario', 'getManual')->name('Manual_Carga_Masiva_metas');
		Route::get('/actividades/metas/actividades-mir/{area?}/{enti?}/{fondo?}', 'getActividMir');
		Route::get('metas/errores/carga-masiva', 'erooresCargaMasiva')->name('ErrCmAct');


	});

	Route::controller(MetasDelController::class)->group(function () {
		Route::get('/calendarizacion/metas-delegacion', 'getMetasDelegacion')->name('index_metas_del');
		Route::get('/actividades/plantilla/metas-delegacion', 'getPlantillaExcel')->name('PlantillaExcel');
		Route::post('/actividades/import/metas-delegacion', 'importPlantilla');
		Route::get('/calendarizacion/proyecto/metas-delegacion', 'getProyecto')->name('proyectoDelegacio');
		Route::get('/actividades/data/metas-delegacion/{upp?}/{anio?}', 'getActivDelegacion');
		Route::post('/calendarizacion/put/metas-delegacion', 'putMeta');
		Route::get('/actividades/flag-confirmar-metas/{anio?}', 'cmetasdel');
		Route::get('/actividades/confirmar-metas/delegacion/{anio?}', 'confirmardel');
		Route::get('/actividades/check-metas/delegacion/{anio?}', 'checkConfirmadas');
		Route::get('/calendarizacion/upps-delegacion', 'getUpps');


	});
?>

	
	
