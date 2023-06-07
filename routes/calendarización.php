<?php 	
	use App\Http\Controllers\Calendarización\MetasController;

	Route::controller(MetasController::class)->group(function () {
		Route::get('/calendarizacion/metas', 'getIndex')->name('index_metas');
	 	Route::get('/calendarizacion/data', 'getMetas')->name('getMetas');
        Route::get('/calendarizacion/proyecto', 'getProyecto')->name('proyecto');
	});
?>