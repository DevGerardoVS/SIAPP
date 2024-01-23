<?php 	
	use App\Http\Controllers\EppController;

	Route::controller(EppController::class)->group(function () {
		Route::get('/epp', 'index')->name('epp');
		Route::post('/get-epp/{anio}/{upp}/{ur}', 'getEpp')->name('get-epp');
		Route::post('/get-ur', 'getUR')->name('get-ur');
		Route::post('/get-upp/{anio}', 'getUPP')->name('get-upp');
		Route::get('/epp-exportExcel/{anio}/{upp}/{ur}', 'exportExcelEPP');
		Route::get('/epp-exportPdf/{anio}', 'exportPdfEPP');
	});
?>

	
	
