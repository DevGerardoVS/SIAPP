<?php 	

    use App\Http\Controllers\Calendarizacion\ClavePreController;
	Route::controller(ClavePreController::class)->group(function () {
		Route::get('/calendarizacion/claves', 'getPanel');
		Route::get('/calendarizacion/claves-get', 'getClaves');
		Route::get('/cat-regiones', 'getRegiones');

		
	});

	
	

?>