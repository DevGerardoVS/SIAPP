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
        Session(['sistema' => 1]);
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
        Session(['sistema' => 1]);
        return view('home');
    } else {
        return view('auth.login');
    }
});

Route::get('/home', function () {
    if(!Auth::guest()){
        Session(['sistema' => 1]);
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
Route::get('/logs',[App\Http\Controllers\LogController::class, 'logsView'])->name('viewLogs');
Route::post('/logs/download',[App\Http\Controllers\LogController::class, 'downloadLogs'])->name('downloadLogs');

include('administracion.php');//Agregar las rutas para el módulo de administración en este archivo