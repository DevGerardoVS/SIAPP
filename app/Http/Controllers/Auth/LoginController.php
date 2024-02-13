<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Redirect;
date_default_timezone_set("America/Mexico_City");
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function login(Request $request)
    {

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            if (Auth::user()->estatus == 1) {Session::put('last_activity',Carbon::now());
                Session::put('status',3);
                return redirect('/');} else {
                Auth::logout();
                return back()->withErrors('Este usuario ha sido deshabilitado');
            }}
        return Redirect::back()->withInput()->withErrors('El nombre de usuario o contraseña es incorrecto');

    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectTo()
    {
        if (session()->has('redirect_to')) {
            return session()->pull('redirect_to');
        }

        return $this->redirectTo;
    }
}
