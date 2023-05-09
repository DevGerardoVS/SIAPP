<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
 
        if (Auth::attempt($credentials)) {
            // Authentication passed...
            return redirect()->intended('dashboard');
        }
    }



    // crear metodo login que verifique el status del usuario

    public function login(Request $request)
    {
        $this->validateLogin($request);
 
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
 
            return $this->sendLockoutResponse($request);
        }
 
        if ($this->guard()->attempt($this->credentials($request))) {
            //Verificamos el estado del usuario
            if ($this->guard()->user()->estatus == 1) {
                return $this->sendLoginResponse($request);
            }
            else{
                //Si el usuario no esta activo lo desconectamos
                $this->guard()->logout();
                //Y lo redireccionamos de nuevo al login pasándole un mensaje
                return redirect()->back()->with('status', 'Tu cuenta no esta activa!');
            }
        }
 
        $this->incrementLoginAttempts($request);
 
        return $this->sendFailedLoginResponse($request);
    }
}
