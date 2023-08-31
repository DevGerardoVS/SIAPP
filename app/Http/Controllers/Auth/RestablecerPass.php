<?php
   
   namespace App\Http\Controllers\Auth;

   use App\Http\Controllers\Controller;
use App\Helpers\BitacoraHelper;
use App\Models\User as ModelsUser;
use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Redirect;

class RestablecerPass extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
   
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
   
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public static function store(Request $request)
    {
        // bitacora
            $request->validate([
                'password' => ['required'],
                'password_confirmation' => ['same:password'],           
            ]);
            $ModelsUser = ModelsUser::where('email', $request->email)->firstOrFail();
        $username = $ModelsUser->username;
            $ModelsUser->password =$request->password_confirmation;
            if($ModelsUser->id_grupo !=4){
                $ModelsUser->clv_upp = NULL;
            }
            $ModelsUser->save();
            if($ModelsUser->wasChanged()){
                $b = array(
                    "username"=>$username,
                    "accion"=>'restablecer de Contraseña nuevo controller',
                    "modulo"=>'Contraseña'
                 );
                Controller::bitacora($b);
                return redirect('/');

            }else{
                Redirect::back()->withInput()->withErrors('Ocurrió un error intente de nuevo');
            }
      			
    }
}