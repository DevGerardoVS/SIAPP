<?php
   
namespace App\Http\Controllers;

use App\Helpers\BitacoraHelper;
use App\Models\User as ModelsUser;
use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Redirect;

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
        // bitacora
         try { 
            $request->validate([
                'contraseña_actual' => ['required', new MatchOldPassword],
                'nueva_contraseña' => ['required'],
                'confirmar_nueva_contraseña' => ['same:nueva_contraseña'],           
            ]);
            if (Auth::attempt(['username' => Auth::user()->username, 'password' => $request->contraseña_actual])) {
                $ModelsUser = ModelsUser::where('id', auth()->user()->id)->firstOrFail();
                $ModelsUser->password = $request->nueva_contraseña;//Hash::make($request->nueva_contraseña);
                if($ModelsUser->id_grupo !=4){
                    $ModelsUser->clv_upp = NULL;
                }else{
                    $ModelsUser->clv_upp = Auth::user()->clv_upp;
                }
    
                $ModelsUser->save();
                      $b = array(
                    "username"=>Auth::user()->username,
                    "accion"=>'Cambio de Contraseña',
                    "modulo"=>'Contraseña'
                 );
                Controller::bitacora($b);
                
            }else{
                return Redirect::back()->withInput()->withErrors('La contraseña es incorrecta');
            }
           
         } catch (\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
        }
        return back()->with("status", "¡La contraseña se cambio correctamente!");
    }
}