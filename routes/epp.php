<?php 	
	use App\Http\Controllers\EppController;

	Route::controller(EppController::class)->group(function () {
		Route::get('/epp', 'index')->name('epp');
		Route::post('/get-epp/{anio}/{upp}/{ur}', 'getEpp')->name('get-epp');
		Route::post('/get-ur', 'getUR')->name('get-ur');
		Route::get('/epp-exportExcel/{anio}', 'exportExcelEPP');
		Route::get('/epp-exportPdf/{anio}', 'exportPdfEPP');
	});
?>

	
	
