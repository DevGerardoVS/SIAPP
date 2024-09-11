<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RestablecerPass;
use App\Http\Controllers\NotificacionController;

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
        Session(['sistema' => 1]);
        if(Auth::user()->id_grupo !=1)
        {
            return redirect('/calendarizacion/claves');
            
        }else{
            return view('home');
        }
  
       
    } else {
        return view('auth.login');
    }
});

Route::get('', function () {
    if ((!Auth::check())) {
        return view('main_page');
    }
    if (!Auth::guest()) {
        Session(['sistema' => 1]);
        if(Auth::user()->id_grupo !=1)
        {
            return redirect('/calendarizacion/claves');
            
        }else{
            return view('home');
        }
    } else {
        return view('auth.login');
    }
});
Route::get('/login', function () {
    if ((!Auth::check())) {
        return view('auth.login');
    }else{
        return view('home');
    }
})->name('login');

Route::get('/home', function () {
    if (!Auth::guest()) {
        Session(['sistema' => 1]);
        if(Auth::user()->id_grupo !=1)
        {
            return redirect('/calendarizacion/claves');
            
        }else{
            return view('home');
        }
    } else {
        return view('auth.login');
    }
});

Route::get('/Inicio', [App\Http\Controllers\HomeController::class, 'index',])->name('Inicio');
Route::get('/get-links', [App\Http\Controllers\Administracion\InicioController::class, 'getLinks'])->name('links');
Route::get('/download-file', [App\Http\Controllers\Administracion\InicioController::class, 'getManual'])->name('manual');
// Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::controller(RestablecerPass::class)->group(function (){
		Route::post('/restablecer-password', 'store')->name('restablecerPass');
	});
Route::group(['middleware' => 'auth'], function () { //proteccion de rutas (AGREGAR AQUI DENTRO LAS RUTAS)
    // cambiar contraseña
    Route::get('/cambiar-contrasenia', [App\Http\Controllers\ChangePasswordController::class, 'index'])->name('cambiar_contrasenia');
    Route::post('/contrasenia-confirmada', [App\Http\Controllers\ChangePasswordController::class, 'store'])->name('change_password');
    Route::get('/logs', [App\Http\Controllers\LogController::class, 'logsView'])->name('viewLogs');
    Route::get('/get-logs', [App\Http\Controllers\LogController::class, 'getLogs'])->name('getLogs');
    Route::get('/logs/download/{file?}', [App\Http\Controllers\LogController::class, 'downloadLogs'])->name('downloadLogs');
    Route::post('/logs/clean', [App\Http\Controllers\LogController::class, 'clearLog'])->name('clearLog');
    Route::post('/logs/delete', [App\Http\Controllers\LogController::class, 'DeleteLog'])->name('deleteLog');
    include('administracion.php'); //Agregar las rutas para el módulo de administración en este archivo
    include('calendarizacion.php'); //Agregar las rutas para el módulo de Calendarizacion en este archivo
    include('epp.php'); //Agregar las rutas para el módulo de Epp en este archivo
    Route::get('/borrar-sesion_sesion_notificacion', [App\Http\Controllers\HomeController::class, 'borrarsesionnotificacion'])->name('borrar-sesion_notificacion');
    Route::post('/notificacion', [App\Http\Controllers\NotificacionesController::class, 'store'])->name('notificacion');

    
});

