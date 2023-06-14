<?php 	
	use App\Http\Controllers\Administracion\SistemasController;
	use App\Http\Controllers\Administracion\UsuarioController;
	use App\Http\Controllers\Administracion\GrupoController;
	use App\Http\Controllers\Administracion\PermisoController;
	use App\Http\Controllers\Administracion\BitacoraController;
	use App\Http\Controllers\Administracion\InicioController;

	Route::controller(SistemasController::class)->group(function () {
		Route::get('/sistemas/panel', 'getPanel');
	});

	Route::controller(UsuarioController::class)->group(function () {
		Route::get('adm-usuarios', 'getIndex')->name('index_usuario');
		Route::get('adm-usuarios/data', 'getData')->name('getdata');
		Route::post('adm-usuarios/status', 'postStatus');
		Route::get('adm-usuarios/create', 'getCreate');
		Route::post('adm-usuarios/store', 'postStore');
		Route::get('adm-usuarios/update/{id?}', 'getUpdate');
		Route::post('adm-usuarios/put-usuario', 'postUpdate');
		Route::get('adm-usuarios/grupos/{idUsuario?}', 'getGrupos');
		Route::post('adm-usuarios/eliminar', 'postDelete');
		Route::post('adm-usuarios/grupos', 'postGrupos');
		Route::get('grupos', 'grupos');
	});

	Route::controller(InicioController::class)->group(function (){
		Route::post('adm-inicio/a', 'GetInicioA')->name('inicio_a');
		Route::post('adm-inicio/b', 'GetInicioB')->name('inicio_b');
	});

	Route::controller(GrupoController::class)->group(function () {
		Route::get('/adm-grupos', 'getIndex')->name('index_grupo');
		Route::post('adm-grupos/dataGroups', 'getData')->name('getGroups');
		Route::get('/adm-grupos/create', 'getCreate');
		Route::post('/adm-grupos/store', 'postStore')->name('postStore');
		Route::get('/adm-grupos/update/{id?}', 'getGrupo');
		Route::post('/adm-grupos/put-grupo', 'postUpdate')->name('postUpdate');
		Route::post('/adm-grupos/eliminar', 'postDelete')->name('postDelete');
	});

	Route::controller(PermisoController::class)->group(function () {
		Route::get('/adm-permisos/grupo/{idGrupo?}', 'getGrupo');
		Route::post('/adm-permisos/asigna', 'postAsigna');
		Route::post('/adm-permisos/remueve', 'postRemueve');
		Route::post('/adm-permisos/sasigna', 'postSasigna');
		Route::post('/adm-permisos/sremueve', 'postSremueve');
		Route::post('/adm-permisos/masigna', 'postMasigna');
		Route::post('/adm-permisos/mremueve', 'postMremueve');
		Route::post('/adm-permisos/all-permission', 'postAllPermission');
	});

	Route::controller(BitacoraController::class)->group(function () {
		Route::get('/adm-bitacora', 'getIndex');
		Route::post('/adm-bitacora/data/{fecha?}', 'getBitacora')->name('getBitacora');
	});
?>