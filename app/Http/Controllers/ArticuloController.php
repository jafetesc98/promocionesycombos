<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\UserMKS;
use App\Models\UserPyc;
use App\Models\Articulo;
use App\Models\Proveedor;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ArticuloController extends Controller
{
    public function getPreciosXSucursal(Request $request){
        $sucursal = $request->input('suc','001');
        $art = $request->input('art','0');
        $proveedor = $request->input('prov','0');
        $empaque = $request ->input('empaque');


        //////////busqueda de factor minimo en la tabla invars///////////
        if($empaque=='PZA'){

        }else{
        $sub_alm;
                if($empaque=='CJA'){
                    $sub_alm=$sucursal.'C';
                }if($empaque=='PAQ'){
                    $sub_alm=$sucursal.'M';
                }

                $fact = DB::table('invars')
                ->select(DB::raw('fac_minimo'))
                ->where('cve_art', $art)
                ->where('alm', $sucursal)
                ->where('sub_alm', $sub_alm)
                ->first();

                if (is_null($fact)) {
                    return response()->json(array(
                        'code'      =>  422,
                        'message'   =>  'No se encontro esta configuración para el articulo ingresado'
                    ), 422);  
                }
              
            }
        $arti = DB::table('inviar')->where('art', $art)->first();
        if (is_null($arti)) {
            return response()->json(array(
                'code'      =>  422,
                'message'   =>  'El código del artículo no fue encontrado'
            ), 422);  
        }


        $articulo = DB::table('invart')
                    ->leftJoin('inviar', 'invart.art', '=', 'inviar.art')
                    ->select('invart.art','des1','precio_vta0','precio_vta1'
                            ,'precio_vta2','precio_vta3','precio_vta4',
                            'cant_pre0', 'cant_pre1', 'cant_pre2', 'cant_pre3',
                             'cant_pre4', 'cve_pro')
                    ->where('alm',$sucursal)
                    ->where('invart.art',$art);
        
        $existeEnSuc = $articulo->first();

        //return response()->json($existeEnSuc);
        if(is_null($existeEnSuc)){
            return response()->json(array(
                'code'      =>  422,
                'message'   =>  'El código capturado no existe en la sucursal seleccionada o está inactivo'
            ), 422);  
        }

        $esSuProv = $articulo->where('cve_pro', $proveedor)->first();

        if(is_null($esSuProv)){
            return response()->json(array(
                'code'      =>  422,
                'message'   =>  'El código del artículo no corresponde al proveedor seleccionado'
            ), 422);  
        }
        
        return response()->json($esSuProv);

    }

    public function apiLogin(Request $request){
        //Validando si es el usuario ADMIN
        //En caso de serlo, no se valida en tabla de MKS
        if(strtoupper(trim($request->usuario)) === 'ADMIN'){
            $user = UserPyc::where('user_mks','ADMIN')->first();
            if (! Hash::check($request->clave, $user->password)) {
                return response()->json(array(
                    'code'      =>  422,
                    'message'   =>  'Contraseña incorrecta',
                    'error'     =>  'Contraseña incorrecta',
                ), 403);  
            }

            $array = array('nombre' => $user->name, 
                'usuario' => $user->user_mks,
                'token' => $user->createToken($user->user_mks)->plainTextToken
              );
            return response()->json($array);
        }

        //Si no es el usuario admin, valida primero que exista en MKS

        $user = UserMKS::where('nombre', $request->usuario)->first();
        if($user == null){
            return response()->json(array(
                'code'      =>  422,
                'message'   =>  'No se encontró el usuario',
                'error'     =>  'No se encontró el usuario',
            ), 403);
        }

        //Si existe en MKS, pero no en tablas PYC, no puede usar el sistema
        $userPYC = UserPyc::where('user_mks', $request->usuario)->first();
        if($userPYC == null){
            return response()->json(array(
                'code'      =>  422,
                'message'   =>  'Usuario MKS no dado de alta en MABPYC',
                'error'     =>  'No se encontró el usuario en MABPYC',
            ), 403);
        }
 
        if ($request->clave !== $user->pwd)
         {
            /*throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);*/
            return response()->json(array(
                'code'      =>  422,
                'message'   =>  'Contraseña incorrecta',
                'error'     =>  'Contraseña incorrecta',
            ), 403);  
        }

        $array = array('nombre' => $user->name, 
                'usuario' => $user->user_mks,
                'token' => $user->createToken($request->user_mks)->plainTextToken
              );
        return response()->json($array);
    }

    public function getProveedores(Request $request)
    {
        $comprador = $request->input('compr',-1);
        $usuario = $request->input('usr',-1);
        $puesto = 'admin';
        if(strtoupper($usuario) != 'PYC'){
            $user = UserMKS::where('nom_cto', $usuario)->first();
            $usuario = $user->nom_cto;
            $puesto = $user->puesto;
        }
        if(str_contains($puesto, 'COMPRAS')){
            $proveedores = DB::table('cprprv')
                            ->select('proveedor', 'nom')
                            ->where('modulo','P')
                            //->where('comprador',$comprador)
                            ->get();
        }

        else{
            $proveedores = DB::table('cprprv')
                            ->select('proveedor', 'nom')
                            ->where('modulo','P')
                            //->where('comprador',$comprador)
                            ->get();
        }
        
        return response()->json($proveedores);
    }


    public function getAcuerdos(Request $request){
        $comprador = $request->input('compr',-1);
        $proveedor = $request->input('prov','00');
        $usuario = $request->input('usr','-1');

        $pila=array();
                        

        $puesto = 'admin';
        if(strtoupper($usuario) != 'PYC'){
            $user = UserMKS::where('nom_cto', $usuario)->first();
            $usuario = $user->nom_cto;
            $puesto = $user->puesto;
        }
        if(str_contains($puesto, 'COMPRAS')){
           $acuerdos = DB::table('Rca_Acuerdos')
                        ->select(DB::raw('Folio, Comprador, Nombre, Linea1, boletin, Fecha'))
                        //->union($apoyos)
                        //->where('Comprador',$comprador)
                        ->where('Clave', $proveedor)
                        ->get();
                        $conteo = count($acuerdos);
                        /* $apoyos = DB::table('Rca_Acuerdos')
                        ->select(DB::raw('Folio, Comprador, Nombre, Linea1, boletin, Fecha'))
                        //->union($apoyos)
                        ->where('Comprador','3')
                        ->where('Clave', '000000057')
                        ->get(); */

              /* $apoyos = DB::table('Rca_ApoyosDir')
                        ->select(DB::raw("Folio + '*' as Folio,Comprador, Nombre, Linea1, 'APOYOS DIRECCION' as boletin, fecApoyo as Fecha"))
                        ->where('Comprador',$comprador);  */

                        for($i=0 ; $i<$conteo; $i++ ){
                            $acuer = array(
                             'Folio'=> $acuerdos[$i]->Folio,
                             'Comprador'=> $acuerdos[$i]->Comprador,
                             'Nombre'=> $acuerdos[$i]->Nombre, 
                             'Linea1'=> $acuerdos[$i]->Linea1,
                             'boletin'=> $acuerdos[$i]->boletin, 
                             'Fecha'=> $acuerdos[$i]->Fecha
                             );
                            
                             array_push($pila, $acuer);
                         }

                         $apoyos = DB::table('Rca_ApoyosDir')
                        ->select(DB::raw("Folio + '*' as Folio,Comprador, Nombre, Linea1, 'APOYOS DIRECCION' as boletin, fecApoyo as Fecha"))
                        //->where('Comprador',$comprador)
                        ->get(); 

                        $conteo1 = count($apoyos);
                        $total = $conteo + $conteo1;

                        for($i=0 ; $i<$conteo1; $i++ ){
                            $acuer1 = array(
                             'Folio'=> $apoyos[$i]->Folio,
                             'Comprador'=> $apoyos[$i]->Comprador,
                             'Nombre'=> $apoyos[$i]->Nombre, 
                             'Linea1'=> $apoyos[$i]->Linea1,
                             'boletin'=> $apoyos[$i]->boletin, 
                             'Fecha'=> $apoyos[$i]->Fecha
                             );
                            
                             array_push($pila, $acuer1);
                         }
                       //return $pila;
        }
       /*  else{
            $acuerdos = DB::table('Rca_Acuerdos')
                        ->select(DB::raw('Folio, Comprador, Nombre, Linea1, boletin, Fecha'))
                        //->union($apoyos)
                        //->where('Comprador',$comprador)
                        ->where('Clave', $proveedor)
                        ->get();
        }
        $countResult = count($acuerdos);
        if($countResult == 0){
            return response()->json($apoyos);
        }else{
            return response()->json($acuerdos);
        } */
        return json_encode($pila, JSON_PRETTY_PRINT);
        
        
    }
    

    public function getCliente(Request $request){
        $nCliente = $request->input('nCliente');

        
        $cliente = Cliente::where('cve','like','%'.$nCliente.'%')->first();

        if(is_null($cliente)==true){
            return response()->json(array(
                'code'      =>  403,
                'message'   =>  'No se encontró el cliente',
                'error'     =>  'No se encontró el cliente',
            ), 403);
        }
        
        $array = array('cve' => $cliente->cve,
                       'nom' => $cliente->nom);

        return response()->json($array);
    }


}
