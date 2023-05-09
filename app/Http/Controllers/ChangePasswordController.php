<?php
   
namespace App\Http\Controllers;

use App\Helpers\BitacoraHelper;
use App\Models\User as ModelsUser;
use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ChangePasswordController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
   
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('auth.passwords.change');
    } 
   
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'contraseña_actual' => ['required', new MatchOldPassword],
            'nueva_contraseña' => ['required'],
            'confirmar_nueva_contraseña' => ['same:nueva_contraseña'],
        ]);
        ModelsUser::find(auth()->user()->id)->update(['password'=> Hash::make($request->nueva_contraseña)]);
        // bitacora
        try {
            $modulo = "Cambio de contraseña";
            $accion = "Modificacion";
            $data_old= array('nombre'=>auth()->user()->nombre, 'email'=>auth()->user()->email, 'password'=>auth()->user()->password);
            $data_new= array('nombre'=>auth()->user()->nombre, 'email'=>auth()->user()->email, 'password'=>Hash::make($request->nueva_contraseña));
            $array_data = array(
                'tabla'=>'users',
                'usuario'=> Auth::user()->username,
                'anterior'=>$data_old,
                'nuevo'=>$data_new
            );
            BitacoraHelper::saveBitacora(BitacoraHelper::getIp(), $modulo , $accion, json_encode($array_data));
        } catch (\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
        }
        return back()->with("status", "¡La contraseña se cambio correctamente!");
    }
}