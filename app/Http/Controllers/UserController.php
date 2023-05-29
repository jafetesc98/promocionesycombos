<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Models\UserMKS;
use App\Models\UserPYC;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
//use App\Models\Bitacora;

class UserController extends Controller
{
     public function apiLogin(Request $request){
        //Validando si es el usuario ADMIN
        //En caso de serlo, no se valida en tabla de MKS
        if(strtoupper(trim($request->usuario)) === 'ADMIN'){
            $user = UserPYC::where('user_mks','ADMIN')->first();
            if (! Hash::check($request->clave, $user->password)) {
                return response()->json(array(
                    'code'      =>  420,
                    'message'   =>  'Contraseña incorrecta',
                    'error'     =>  'Contraseña incorrecta',
                ), 420);  
            }

            $array = array('nombre' => $user->name, 
                'usuario' => $user->user_mks,
                'nombre_lar' => $user->name,
                'nomCto' => $user->cve_corta,
                'rol' => 1,
                'numcomp' => -1,
                'token' => $user->createToken($user->user_mks)->plainTextToken,
                'sexo' => 1

              );
            return response()->json($array);
        }

        //Si no es el usuario admin, valida primero que exista en MKS

        $user = UserMKS::where('nombre', $request->usuario)->first();
        if($user == null){
            return response()->json(array(
                'code'      =>  421,
                'message'   =>  'No se encontró el usuario',
                'error'     =>  'No se encontró el usuario',
            ), 421);
        }

        if($user->puesto == 'BAJA           '){
            return response()->json(array(
                'code'      =>  422,
                'message'   =>  'Usuario MKS dado de baja',
                'error'     =>  'Usuario MKS dado de baja',
            ), 422);
        }

        //Si existe en MKS, pero no en tablas PYC, no puede usar el sistema
        $userPYC = UserPYC::where('user_mks', $request->usuario)->first();
        if($userPYC == null){
            return response()->json(array(
                'code'      =>  422,
                'message'   =>  'Usuario MKS no dado de alta en MABPROMOCIONES',
                'error'     =>  'No se encontró el usuario en MABPROMOCIONES',
            ), 422);
        }


 
        if (strtoupper($request->clave) !== trim($user->pwd))
         {
            /*throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);*/
            return response()->json(array(
                'code'      =>  420,
                'message'   =>  'Contraseña incorrecta',
                'error'     =>  'Contraseña incorrecta',
            ), 420);  
        }

        $num_comp = -1;
        $puesto = $user->puesto;
        $rol = 4;
        $nombre = $user->nombre_lar;
        $nombre = trim($nombre);

        if(str_contains($puesto, 'COMPRAS')){
            $numcomp = DB::table('cprcom')
                        ->select('cve')
                        ->where('nom','like','%'.$nombre.'%')
                        ->first();
            $num_comp = $numcomp;
        }

        if(str_contains($puesto, 'COMPRASJE')){
            $rol = 2;
        }

        if(str_contains($puesto, 'TRADEMKT')){
            $rol = 2;
        }

        $array = array(
                'usuario' => $userPYC->user_mks,
                'nombre_lar' => $userPYC->name,
                'token' => $userPYC->createToken($userPYC->user_mks)->plainTextToken,
                'nomCto' => $userPYC->cve_corta,
                'rol' => $rol,
                'numcomp' => trim($num_comp->cve),
                'sexo' => $userPYC->sexo
              );
        return response()->json($array);
    }

}
