<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Auth;
use Redirect;

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

    public function login(Request $request) {
        log::debug("::: ". $request['email']);
        $verificacion = Auth::attempt(['email' => $request['email'], 'password' => $request['password']]);

        if(Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            if(Auth::user()->estatus == 1)
                return redirect('/');
            else
                return Redirect::back()->withInput()->withErrors('Error');
        }
        return Redirect::back()->withInput()->withErrors('Error');

    }

    public function logout() {
        Auth::logout();
        return redirect('/');
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectTo() {
        if (session()->has('redirect_to'))
            return session()->pull('redirect_to');

        return $this->redirectTo;
    }
}
