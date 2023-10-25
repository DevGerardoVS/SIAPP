<?php 	
	use App\Http\Controllers\Administracion\SistemasController;
	use App\Http\Controllers\Administracion\UsuarioController;
	use App\Http\Controllers\Administracion\GrupoController;
	use App\Http\Controllers\Administracion\PermisoController;
	use App\Http\Controllers\Administracion\BitacoraController;
	use App\Http\Controllers\ReporteController;
	use App\Http\Controllers\AdmonCapturaController;
	use App\Http\Controllers\Administracion\InicioController;
	use App\Http\Controllers\Administracion\ConfiguracionesController;

	Route::controller(SistemasController::class)->group(function () {
		Route::get('/sistemas/panel', 'getPanel');
	});

Route::controller(UsuarioController::class)->group(function () {
	Route::get('adm-usuarios', 'getIndex')->name('index_usuario');
	Route::get('adm-usuarios/data', 'getData')->name('getdata');
	Route::get('adm-usuarios/indexPermiso', 'getIndexUP')->name('index_up');
	Route::get('adm-usuarios/permisos', 'getDataUP')->name('getdataUserPerm');
	Route::post('adm-usuarios/status', 'postStatus');
	Route::get('adm-usuarios/create', 'getCreate');
	Route::post('adm-usuarios/store', 'postStore');
	Route::get('adm-usuarios/update/{id?}', 'getUpdate');
	Route::post('adm-usuarios/put-usuario', 'postUpdate');
	Route::get('adm-usuarios/grupos/{idUsuario?}', 'getGrupos');
	Route::post('adm-usuarios/eliminar', 'postDelete');
	Route::post('adm-usuarios/grupos', 'postGrupos');
	Route::get('grupos', 'grupos');
	Route::get('/upp/get', 'getUpp');
	Route::get('/users/permissos', 'getUsers');
	Route::get('/users/menu', 'getModulos');
	Route::post('/users/permissos/create', 'createPermisson');
	Route::post('/users/permissos/assign', 'assignPermisson');
	Route::get('/users/permissos/get', 'getPermisson');
	Route::get('/adm-usuarios-exportExcel', 'exportExecel');
	Route::get('/adm-usuarios-exportPdf', 'exportPdf');
});

	Route::controller(InicioController::class)->group(function (){
		Route::post('adm-inicio/a', 'GetInicioA')->name('inicio_a');
		Route::post('adm-inicio/b', 'GetInicioB')->name('inicio_b');
		Route::post('/fondos/inicio', 'getFondos')->name('get_fondos');
		Route::get('/export-Excel', 'exportExcel');
		Route::get('/export-Pdf', 'exportPdf');
	});

	Route::controller(ConfiguracionesController::class)->group(function (){
		Route::get('/adm-configuracion', 'getIndex')->name('index_configuraciones');
		Route::post('/amd-configuracion/data', 'GetConfiguraciones')->name('configuraciones');
		Route::post('/amd-configuracion/upps', 'GetUpps')->name('getUpps');
		Route::post('/amd-configuracion/update', 'updateUpps')->name('updateUpps');
		Route::post('/amd-configuracion/upps-auto', 'GetUppsAuto')->name('getUppsAuto');
		Route::post('/amd-configuracion/data-auto', 'GetAutorizadas')->name('uppsAutorizadas');
		Route::post('/amd-configuracion/update-auto', 'updateAutoUpps')->name('updateAutoUpps');
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
		Route::get('/adm-bitacora/data/{anio?}/{mes?}', 'getBitacora');
		Route::get('/adm-bitacora/dataAnios', 'getBitAnios');
		Route::get('/adm-bitacora/exportExcelBitacora/{anio?}/{mes?}', 'exportBitacora');
	});

	Route::controller(ReporteController::class)->group(function(){
		Route::get('/Reportes/ley-planeacion','indexPlaneacion')->name('index_planeacion');
		Route::get('/Reportes/administrativos', 'indexAdministrativo')->name('index_administrativo');
		Route::get('/Reportes/analisis-informativo-mml', 'indexAnalisisMML')->name('index_analisis_mml');
		
		// Reportes administrativos
		Route::post('/Reportes/administrativos/calendarioFondoMensual', 'calendarioFondoMensual')->name('calendario_fondo_mensual');
    	Route::post('/Reportes/administrativos/resumenCapituloPartida', 'resumenCapituloPartida')->name('resumen_capitulo_partida');
    	Route::post('/Reportes/administrativos/proyectoCalendarioGeneral', 'proyectoCalendarioGeneral')->name('proyecto_calendario_general');
    	Route::post('/Reportes/administrativos/proyectoAvanceGeneral', 'proyectoAvanceGeneral')->name('proyecto_avance_general');
    	Route::post('/Reportes/administrativos/proyectoCalendarioGeneralActividad', 'proyectoCalendarioGeneralActividad')->name('proyecto_calendario_general_actividad');
    	Route::post('/Reportes/administrativos/avanceProyectoActividadUPP', 'avanceProyectoActividadUPP')->name('avance_proyecto_actividad_upp');
		
    	Route::post('/Reportes/analisis-informativo-mml/avance-mir', 'getAvanceMIR')->name('get_avance_mir'); // Ruta avance_mir
    	Route::post('/Reportes/analisis-informativo-mml/mir', 'getMIR')->name('get_mir'); // Ruta mir
    	Route::post('/Reportes/analisis-informativo-mml/proyecto-presupuestal', 'getProyectoPresupuestal')->name('get_proyecto_presupuestal'); // Ruta proyecto presupuestal

		Route::post('/Reportes/data-fecha-corte/{ejercicio?}','getFechaCorte')->name('get_fecha_corte'); // Obtener fecha de acuerdo al año
		Route::post('/Reportes/analisis-informativo-mml/data-upp/{ejercicio?}','getUPP')->name('get_UPP'); // Obtener UPP de acuerdo a la UPP
		Route::post('/Reportes/analisis-informativo-mml/data-programa/{clv_upp?}','getPrograma')->name('get_programa'); // Obtener programa de acuerdo a la UPP
		Route::post('/Reportes/download/{nombre}', 'downloadReport')->name('downloadReport'); // Descargar reportes
	});

	Route::controller(AdmonCapturaController::class)->group(function(){
		Route::get('/admon-capturas','index')->name('index');
		Route::post('/admon-capturas/clavesPresupuestarias', 'clavesPresupuestarias')->name('claves_presupuestarias');
		Route::post('/admon-capturas/metasActividades', 'metasActividades')->name('metas_actividades');
		Route::put('/admon-capturas/update', 'update')->name('admon_capturas_update');
		Route::put('/admon-capturas/update-PPH', 'updateProgramacionPH')->name('pph_update');

	});
?>