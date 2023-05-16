<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', function () {
    if (!Auth::guest()) {
        return view('home');
    } else {
        return view('auth.login');
    }
});

Route::get('', function () {
    if ((!Auth::check())) {
        return view('auth.login');
    }
    if (!Auth::guest()) {
        return view('home');
    } else {
        return view('auth.login');
    }
});

Route::get('/home', function () {
    if(!Auth::guest()){
        return view('home');
    } else {
        return view('auth.login');
    }
});


Route::get('/Inicio', [App\Http\Controllers\HomeController::class, 'index',])->name('Inicio');
Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => 'auth'], function () { //proteccion de rutas (AGREGAR AQUI DENTRO LAS RUTAS)
    // cambiar contraseña
    Route::get('/cambiar-contrasenia', [App\Http\Controllers\ChangePasswordController::class, 'index'])->name('cambiar_contrasenia');
    Route::post('/contrasenia-confirmada', [App\Http\Controllers\ChangePasswordController::class, 'store'])->name('change_password');
    //Usuarios
});

Route::middleware(['role:Monitor|Super Usuario'])->group(function () {
    Route::get('/users', [App\Http\Controllers\UsersController::class, 'index'])->name('users');
    Route::post('/get-users', [App\Http\Controllers\UsersController::class, 'getUsuarios'])->name('get-users');
    Route::post('/users-destroy', [App\Http\Controllers\UsersController::class, 'destroy'])->name('users-destroy');
    Route::post('/users-add', [App\Http\Controllers\UsersController::class, 'store'])->name('users-add');
    Route::post('/users-update', [App\Http\Controllers\UsersController::class, 'update'])->name('users-update');
    Route::post('/users-edit', [App\Http\Controllers\UsersController::class, 'show'])->name('users-edit');
    Route::get('/users-export', [App\Http\Controllers\UsersController::class, 'export'])->name('users-export');
    Route::get('/users/get-organismos', [App\Http\Controllers\UsersController::class, 'getOrganismosMunicipales'])->name('get-organismos-user');
});
//Configuracion
Route::middleware(['role:Control|Super Usuario'])->group(function () {
    Route::get('/configuraciones', [App\Http\Controllers\ConfiguracionController::class, 'index'])->name('configuraciones');
    //Permisos
    Route::get('/configuraciones/get-modulos-padre', [App\Http\Controllers\PermisosController::class, 'getModulosPadre'])->name('get_modulos_padre');
    Route::get('/configuraciones/get-modulos-sistema', [App\Http\Controllers\PermisosController::class, 'getModulosSistema'])->name('get_modulos_sistema');
    Route::get('/configuraciones/get-permisos-sistema', [App\Http\Controllers\PermisosController::class, 'getPermisosSistema'])->name('get_permisos_sistema');
    //Modulos
    Route::post('/configuraciones/get-modulos', [App\Http\Controllers\PermisosController::class, 'getModulos'])->name('get_modulos');
    Route::get('/configuraciones/modulos', [App\Http\Controllers\PermisosController::class, 'modulos'])->name('modulos');
    Route::post('/configuraciones/modulos/agregar', [App\Http\Controllers\PermisosController::class, 'agregarModulos'])->name('agregar_modulos');
    Route::get('/configuraciones/modulos/acciones/{action?}/{id?}', [App\Http\Controllers\PermisosController::class, 'actionsModulos'])->name('acciones_modulos');
    //Funciones
    Route::post('/configuraciones/get-funciones', [App\Http\Controllers\PermisosController::class, 'getFunciones'])->name('get_funciones');
    Route::get('/configuraciones/funciones', [App\Http\Controllers\PermisosController::class, 'funciones'])->name('funciones');
    Route::post('/configuraciones/funciones/agregar', [App\Http\Controllers\PermisosController::class, 'agregarFunciones'])->name('agregar_funciones');
    Route::get('/configuraciones/funciones/acciones/{action?}/{id?}', [App\Http\Controllers\PermisosController::class, 'actionsFunciones'])->name('acciones_funciones');
    //Perfiles
    Route::post('/configuraciones/get-perfiles', [App\Http\Controllers\PermisosController::class, 'getPerfiles'])->name('get_perfiles');
    Route::get('/configuraciones/perfiles', [App\Http\Controllers\PermisosController::class, 'perfiles'])->name('perfiles');
    Route::post('/configuraciones/perfiles/agregar', [App\Http\Controllers\PermisosController::class, 'agregarPerfiles'])->name('agregar_perfiles');
    Route::get('/configuraciones/perfiles/acciones/{action?}/{id?}', [App\Http\Controllers\PermisosController::class, 'actionsPerfiles'])->name('acciones_perfiles');
    //Logs
    Route::get('/configuraciones/logs',[App\Http\Controllers\LogController::class, 'logsView'])->name('viewLogs');
    Route::post('/configuraciones/logs/download',[App\Http\Controllers\LogController::class, 'downloadLogs'])->name('downloadLogs');
    //bitacora 
    Route::post('/configuraciones/get-bitacora',[App\Http\Controllers\BitacoraController::class,'getBitacora'])->name('get_bitacora');
    Route::get('/configuraciones/bitacora', [App\Http\Controllers\BitacoraController::class, 'bitacoras'])->name('bitacoras');
    Route::post('/configuraciones/get-fecha-bitacora', [App\Http\Controllers\BitacoraController::class, 'getBitacora2'])->name('get_fecha_bitacora');
    Route::post('/configuraciones/bitacora-export', [App\Http\Controllers\BitacoraController::class, 'exportBitacora'])->name('bitacora_export');
});



Route::middleware(['role:Monitor|Analista|Super Usuario'])->group(function () {
    Route::get('/concesiones/get-aseguradoras', [App\Http\Controllers\AdminConcesionesController::class, 'getAseguradoras'])->name('get_aseguradoras');//->middleware('can:concesiones.administrador_de_concesiones.consultar');
    Route::post('/concesiones/get-admin-concesiones', [App\Http\Controllers\AdminConcesionesController::class, 'getAdminConcesiones'])->name('get_admin_concesiones')->middleware('can:concesiones.administrador_de_concesiones.consultar');
    Route::get('/concesiones/admin-concesiones', [App\Http\Controllers\AdminConcesionesController::class, 'adminConcesiones'])->name('admin_concesiones')->middleware('can:concesiones.administrador_de_concesiones.consultar');
    Route::post('/concesiones/admin-concesiones/preview-file-poliza', [App\Http\Controllers\AdminConcesionesController::class, 'previewFilePoliza'])->name('preview_file_poliza')->middleware('can:concesiones.administrador_de_concesiones.ver_poliza');
    Route::post('/concesiones/admin-concesiones/change-estatus-poliza', [App\Http\Controllers\AdminConcesionesController::class, 'changeEstatusPoliza'])->name('change_estatus_poliza')->middleware('can:concesiones.administrador_de_concesiones.validar_poliza');
    Route::post('/concesiones/admin-concesiones/agregar-poliza', [App\Http\Controllers\AdminConcesionesController::class, 'agregarPolizaSeguro'])->name('agregar_poliza')->middleware('can:concesiones.administrador_de_concesiones.reemplazar_poliza');
    Route::post('/concesiones/admin-concesiones/reemplazar-archivo-poliza', [App\Http\Controllers\AdminConcesionesController::class, 'replaceFilePoliza'])->name('reemplazar_archivo_poliza')->middleware('can:concesiones.administrador_de_concesiones.reemplazar_archivo_poliza');
    Route::post('/concesiones/admin-concesiones/detalle-datos-concesion', [App\Http\Controllers\AdminConcesionesController::class, 'detalleDatosConcesion'])->name('detalle_datos_concesion')->middleware('can:concesiones.administrador_de_concesiones.ver_detalle');
    Route::post('/concesiones/admin-concesiones/export', [App\Http\Controllers\AdminConcesionesController::class, 'exportAdminConcesiones'])->name('export_admin_concesiones')->middleware('can:concesiones.administrador_de_concesiones.exportar');
});

Route::middleware(['role:Monitor|Analista|Super Usuario|Ventanilla'])->group(function () {
    Route::get('/concesiones', [App\Http\Controllers\pagoconcesion::class, 'index'])->name('consultaradeudoconsescion');
    Route::any('/concesionesgetdatos', [App\Http\Controllers\pagoconcesion::class, 'getdatosconsesiones'])->name('getdatosconsesiones');
    Route::post('/imprimirdatoss', [App\Http\Controllers\pagoconcesion::class, 'imprimirdatoss'])->name('imprimirdatos');
    Route::post('/descargarformato', [App\Http\Controllers\pagoconcesion::class, 'descargarformato'])->name('descargarformato');
    
    Route::post('/guardarpoliza', [App\Http\Controllers\pagoconcesion::class, 'guardarpoliza'])->name('guardarpoliza');



});

Route::middleware(['role:Monitor|Super Usuario'])->group(function () {
    Route::post('/reportes/get-reporte-polizas-x-concesion', [App\Http\Controllers\ReportesController::class, 'getReportePolizasXConcesion'])->name('get_reporte_polizas_x_concesion')->middleware('can:reportes.reporte_de_polizas_de_seguro_por_concesion.consultar');
    Route::get('/reportes/reporte-polizas-x-concesion', [App\Http\Controllers\ReportesController::class, 'reportePolizasXConcesion'])->name('reporte_polizas_x_concesion')->middleware('can:reportes.reporte_de_polizas_de_seguro_por_concesion.consultar');
    Route::post('/reportes/reporte-polizas-x-concesion/export', [App\Http\Controllers\ReportesController::class, 'exportReportePolizasXConcesion'])->name('export_reporte_polizas_x_concesion')->middleware('can:reportes.reporte_de_polizas_de_seguro_por_concesion.exportar');
    Route::post('/reportes/reporte-polizas-x-concesion/export-pdf', [App\Http\Controllers\ReportesController::class, 'exportReportePolizasXConcesionPdf'])->name('export_reporte_polizas_x_concesion_pdf')->middleware('can:reportes.reporte_de_polizas_de_seguro_por_concesion.exportar');

    Route::post('/reportes/get-reporte-movimientos-x-concesion', [App\Http\Controllers\ReportesController::class, 'getReporteMovimientosXConcesion'])->name('get_reporte_movimientos_x_concesion')->middleware('can:reportes.reporte_de_movimientos_de_concesion.consultar');
    Route::get('/reportes/reporte-movimientos-x-concesion', [App\Http\Controllers\ReportesController::class, 'reporteMovimientosXConcesion'])->name('reporte_movimientos_x_concesion')->middleware('can:reportes.reporte_de_movimientos_de_concesion.consultar');
    Route::post('/reportes/reporte-movimientos-x-concesion/export', [App\Http\Controllers\ReportesController::class, 'exportReporteMovimientosXConcesion'])->name('export_reporte_movimientos_x_concesion')->middleware('can:reportes.reporte_de_movimientos_de_concesion.exportar');
    Route::post('/reportes/reporte-movimientos-x-concesion/export-pdf', [App\Http\Controllers\ReportesController::class, 'exportReporteMovimientosXConcesionPdf'])->name('export_reporte_movimientos_x_concesion_pdf')->middleware('can:reportes.reporte_de_movimientos_de_concesion.exportar');
});

Route::middleware(['role:Analista|Super Usuario'])->group(function () {

Route::get('/desbloqueo/concesion', [App\Http\Controllers\pagoconcesion::class, 'desbloqueoconcesion'])->name('desbloqueoconcesion');
// ->middleware('can:reportes.reporte_de_polizas_de_seguro_por_concesion.consultar')
Route::post('/desbloqueo/concesion/id', [App\Http\Controllers\pagoconcesion::class, 'desbloqueoconcesionupdate'])->name('desbloqueoconcesionupdate');

});

