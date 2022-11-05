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
        $userPYC = UserPYC::where('user_mks', $request->usuario)->first();
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

    public function index(){
        return view('admin.ListaUsuarios');   
    }

    public function create()
    {
        return view('admin.AgregarUsuario');
    }

    public function store(Request $request){
        $usuario = new User;
 
        $usuario->nombre = $request->input('name');
        $usuario->apellido1 = $request->input('apellido1');
        $usuario->apellido2 = $request->input('apellido2');
        $usuario->email = $request->input('email');
        $usuario->departamento_id = $request->input('departamento');
        $usuario->rol = $request->input('rol');
       
        $password = 'pass1234';
        $password = Hash::make($password);
        $usuario->password = $password;

        $usuario->save();
        
        $bitacora = new Bitacora;
        $bitacora->user_id = Auth::id();
        $bitacora->elemento_id = $usuario->id;
        $bitacora->descripcion_bitacora_id = 5;
        $bitacora->save(); 
        return redirect('/usuarios');
    }

    public function show(Request $request, $id)
    {
        //$usuario = User::findOrFail($id);
        $usuario = DB::table('users')
            ->where('users.id', '=', $id)
            ->join('departamentos', 'users.departamento_id', '=', 'departamentos.id')
            ->select('users.*', 'departamentos.nombre as departamento')
            ->get()->first();
        $alta = new Carbon($usuario->created_at);
        $alta = $alta->toDayDateTimeString();

        //return $usuario;
        
        return view('admin.DetalleUsuario', ['usuario'=>$usuario, 'alta'=>$alta]);
        //return $usuario;
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);
        $usuario->nombre = $request->input('nombre');
        $usuario->apellido1 = $request->input('apellido1');
        $usuario->apellido2 = $request->input('apellido2');
        $usuario->email = $request->input('email');
        $usuario->departamento_id = $request->input('departamento');
        $usuario->rol = $request->input('rol');

        $usuario->save();
        
        $bitacora = new Bitacora;
        $bitacora->user_id = Auth::id();
        $bitacora->elemento_id = $usuario->id;
        $bitacora->descripcion_bitacora_id = 6;
        $bitacora->save(); 
        return redirect('/usuarios');
    }

    public function getAll(){
        return User::all();
    }

    public function getAll2(){
        //Devuelve todos los datos incluyendo el campo departamento
        $users = DB::table('users')
            ->join('departamentos', 'users.departamento_id', '=', 'departamentos.id')
            ->select('users.*', 'departamentos.nombre as departamento')
            ->where('users.estado','=','1')
            ->get();
        return $users;
    }

    public function getAll3(){
        //Devuelve todos los datos incluyendo el campo departamento
        $users = DB::table('users')
            ->join('departamentos', 'users.departamento_id', '=', 'departamentos.id')
            ->select('users.*', 'departamentos.nombre as departamento')
            //->where('users.estado','=','1')
            ->get();
        return $users;
    }

    public function disable(Request $request, $id){
        $user = User::findOrFail($id);
        $estado = $request->input('estado');
        
        $bitacora = new Bitacora;
        $bitacora->user_id = Auth::id();
        $bitacora->elemento_id = $user->id;
        
        if($estado == 1){
            $user->estado = 0;
            $bitacora->descripcion_bitacora_id = 7;
        }
        else{
            $user->estado = 1;
            $bitacora->descripcion_bitacora_id = 8;
        }
        $user->save();
        $bitacora->save();


        return redirect('/usuarios');
    }

    public function homeUser(Request $request){
        $datenow = Carbon::now('America/Mexico_City'); 
        $now = $datenow->format('Y-m-d');

        //15 dias atras de la fecha actual
        $before = Carbon::now('America/Mexico_City')->subDay(21);
        $before = $before->format('Y-m-d');
        $id = Auth::id();
        $tareas = DB::table('tareas')
            ->leftJoin('usuarios_hacen_tareas', 'tareas.id', '=', 'usuarios_hacen_tareas.tarea_id')
            ->where('user_id', $id)
            ->whereBetween('f_asignacion', [$before." 00:00", $now." 23:59"])
            //->whereMonth('f_asignacion', '=', $datenow->month)
            //->whereYear('f_asignacion', '=', $datenow->year)
            ->select('id', 'titulo', 'prioridad', 'estado', 'f_asignacion', 'f_limite')
            //->toSql();
            ->get();
        //return $tareas;// . "  ". $before."  " . $now;

        $tareasSupervisar = DB::table('tareas')
            ->leftJoin('usuarios_revisan_tareas', 'tareas.id', '=', 'usuarios_revisan_tareas.tarea_id')
            ->where('user_id', $id)
            ->whereBetween('f_asignacion', [$before." 00:00", $now." 23:59"])
            //->whereMonth('created_at', '=', $datenow->month)
            //->whereYear('created_at', '=', $datenow->year)
            ->select('id', 'titulo', 'prioridad', 'estado', 'f_asignacion', 'f_limite')
            ->get();

        $tareasCreadas = DB::table('tareas')
            ->where('created_by', Auth::id())
            ->whereBetween('f_asignacion', [$before." 00:00", $now." 23:59"])
            //->whereMonth('created_at', '=', $datenow->month)
            //->whereYear('created_at', '=', $datenow->year)
            ->select('id', 'titulo', 'prioridad', 'estado', 'f_asignacion', 'f_limite')
            ->get();

        
        
        $tareasNuevas = array();
        $tareasProgreso = array();
        $tareasEspera = array();
        $tareasPausadas = array();
        $tareasCanceladas = array();
        $tareasFinalizadas = array();
        $tareasRetrasadas = array();

        $tareasNuevas2 = array();
        $tareasProgreso2 = array();
        $tareasEspera2 = array();
        $tareasPausadas2 = array();
        $tareasCanceladas2 = array();
        $tareasFinalizadas2 = array();
        $tareasRetrasadas2= array();

        $tareasNuevas3 = array();
        $tareasProgreso3 = array();
        $tareasEspera3 = array();
        $tareasPausadas3 = array();
        $tareasCanceladas3 = array();
        $tareasFinalizadas3 = array();
        $tareasRetrasadas3= array();
        
        foreach ($tareas as $tarea) {
            $fechafin; 
            switch($tarea->estado){
                case 0: 
                    array_push($tareasNuevas, $tarea);
                    break;
                case 1: 
                    $fechafin = new Carbon($tarea->f_limite, 'America/Mexico_City');
                    if($datenow > $fechafin){
                        array_push($tareasRetrasadas, $tarea);
                    }
                    array_push($tareasProgreso, $tarea);
                    break;
                case 2: 
                    array_push($tareasEspera, $tarea);
                    break;
                case 3: 
                    array_push($tareasPausadas, $tarea);
                    break;
                case 4: 
                    array_push($tareasCanceladas, $tarea);
                    break;
                case 5:
                case 6: 
                    array_push($tareasFinalizadas, $tarea);
                    break;
            }
        }

        foreach ($tareasSupervisar as $tarea) {
            $fechafin; 
            switch($tarea->estado){
                case 0: 
                    array_push($tareasNuevas2, $tarea);
                    break;
                case 1: 
                    $fechafin = new Carbon($tarea->f_limite, 'America/Mexico_City');
                    if($datenow->gt($fechafin)){
                        array_push($tareasRetrasadas2, $tarea);
                    }
                    array_push($tareasProgreso2, $tarea);
                    break;
                case 2: 
                    array_push($tareasEspera2, $tarea);
                    break;
                case 3: 
                    array_push($tareasPausadas2, $tarea);
                    break;
                case 4: 
                    array_push($tareasCanceladas2, $tarea);
                    break;
                case 5:
                case 6: 
                    array_push($tareasFinalizadas2, $tarea);
                    break;
            }
        }

        foreach ($tareasCreadas as $tarea) {
            $fechafin; 
            switch($tarea->estado){
                case 0: 
                    array_push($tareasNuevas3, $tarea);
                    break;
                case 1: 
                    $fechafin = new Carbon($tarea->f_limite, 'America/Mexico_City');
                    if($datenow->gt($fechafin)){
                        array_push($tareasRetrasadas3, $tarea);
                    }
                    array_push($tareasProgreso3, $tarea);
                    break;
                case 2: 
                    array_push($tareasEspera3, $tarea);
                    break;
                case 3: 
                    array_push($tareasPausadas3, $tarea);
                    break;
                case 4: 
                    array_push($tareasCanceladas3, $tarea);
                    break;
                case 5:
                case 6: 
                    array_push($tareasFinalizadas3, $tarea);
                    break;
            }
        }
        
        $tareasUsuario = 
        array('tareasNuevas' => $tareasNuevas, 'tareasProgreso' => $tareasProgreso,
        'tareasEspera' => $tareasEspera,'tareasPausadas' => $tareasPausadas, 
        'tareasCanceladas' => $tareasCanceladas, 'tareasFinalizadas' => $tareasFinalizadas,
        'tareasRetrasadas' => $tareasRetrasadas);

        $tareasASupervisar = 
        array('tareasNuevas' => $tareasNuevas2, 'tareasProgreso' => $tareasProgreso2,
        'tareasEspera' => $tareasEspera2,'tareasPausadas' => $tareasPausadas2, 
        'tareasCanceladas' => $tareasCanceladas2, 'tareasFinalizadas' => $tareasFinalizadas2,
        'tareasRetrasadas' => $tareasRetrasadas2);

        $tareascreadas = 
        array('tareasNuevas' => $tareasNuevas3, 'tareasProgreso' => $tareasProgreso3,
        'tareasEspera' => $tareasEspera3,'tareasPausadas' => $tareasPausadas3, 
        'tareasCanceladas' => $tareasCanceladas3, 'tareasFinalizadas' => $tareasFinalizadas3,
        'tareasRetrasadas' => $tareasRetrasadas3);

        

        return view('userHome', [
            'tareas' => $tareas, 'tareasUsuario' => $tareasUsuario, 'tareasCreadas' => $tareascreadas,
            'tareasRevisar' => $tareasSupervisar, 'tareasSupervisar'=> $tareasASupervisar, 'tareascreadas' => $tareasCreadas
        ]);
        //return $tareas;
    }

    public function tareaUsuario(Request $request, $id){
        $tarea2 = Tarea::findOrFail($id);
        $this->authorize('view', $tarea2);
        $responsables = DB::table('users')
            ->rightJoin('usuarios_revisan_tareas', 'users.id', '=', 'usuarios_revisan_tareas.user_id')
            ->select('users.id', 'users.nombre', 'users.apellido1', 'users.apellido2')
            ->where('tarea_id',$id)
            ->get();
        $operativos = DB::table('users')
            ->rightJoin('usuarios_hacen_tareas', 'users.id', '=', 'usuarios_hacen_tareas.user_id')
            ->select('users.id','users.nombre', 'users.apellido1', 'users.apellido2')
            ->where('tarea_id',$id)
            ->get();

        $creador = User::where('id', '=', $tarea2->created_by)
            ->select('users.id','users.nombre', 'users.apellido1', 'users.apellido2')->get();
        $logueado = User::where('id', '=', Auth::id())
            ->select('users.id','users.nombre', 'users.apellido1', 'users.apellido2')->get();
        
        $es_responsable = $this->isResponsable($responsables, Auth::id()) || $tarea2->created_by == Auth::id();
        $es_operativo = $this->isResponsable($operativos, Auth::id());
        
        if($es_operativo)
        $this->setFechaInicio($tarea2);


        $tarea = DB::table('tareas')
            ->rightJoin('departamentos', 'tareas.departamento_id', '=', 'departamentos.id')
            ->leftJoin('categorias', 'tareas.categoria_id', '=', 'categorias.id')
            ->leftJoin('sucursales', 'sucursal_id', '=', 'sucursales.id')
            ->where('tareas.id', $id)
            ->select('tareas.*', 'departamentos.nombre as departamento',
            'categorias.nombre as categoria', 'sucursales.cve as cve_suc',
             'sucursales.nombre as sucursal' )
            ->get();
        //if($tarea == null) abort(403);
        if($tarea->isEmpty()) abort(403);
        $subtareas = Subtarea::where('tarea_id', $id)->get();
        $puedeCancelar = $this->puedeSolicitarCancelar($subtareas);

        $comentarios = Comentario::where('tarea_id',$id)->orderBy('created_at', 'desc')->get()->toArray();
        $respuestas = array();
        foreach ($comentarios as $c) {
            $c['respuestas'] = RespuestaComentario::where("comentario_id", $c['id'])->get();
            array_push($respuestas, $c);
        }
        
        $solicitudes = Solicitud::where('tarea_id', $id)->orderBy('id', 'DESC')->get();
        $sinRevisar = $this->setRevisionSolicitud($solicitudes, $es_responsable, $tarea2);
        $archivos = Archivo::where('tarea_id', $id)->get();
        
        return view('DetalleTareaUsuario', [ 'tarea2' => $tarea2,
            'tarea'=>$tarea, 'subtareas' => $subtareas,
            'operativos' => $operativos, 'responsables' => $responsables,
            'solicitudes' => $solicitudes, 'sinRevisar' => $sinRevisar,
            'comentarios' => $respuestas, 'archivos' => $archivos,
            'esEncargado' => $es_responsable, 'esOperativo' => $es_operativo,
            'puedeCancelar' => $puedeCancelar, 'creador' => $creador, 'logueado' => $logueado
        ]);
        
    }

    public function puedeSolicitarCancelar($subtareas){
        if($subtareas->isEmpty()){
            return true;
        }
        foreach ($subtareas as $s) {
            if($s->estado == 1)
                return false;
        }
        return true;
    }
    public function setRevisionSolicitud($solicitudes, $esEncargado, $tarea){
        $now = Carbon::now('America/Mexico_City');
        $sinRevisar = false;
        foreach ($solicitudes as $s) {
            if($s->estado == 0 || $s->estado == 4){
                if(($s->tipo == 3 || $s->tipo == 4) && $esEncargado){
                    if($s->f_revision == null){
                        $s->f_revision = $now;
                        $s->estado = 4;
                        $s->save();
                        $sinRevisar = true;
                    }
                }
                if($s->tipo == 2 && $s->f_revision == null && !$esEncargado){
                    $s->f_revision = $now;
                    $s->estado = 4;
                    $s->save();
                    $sinRevisar = true;
                }
                if(($s->tipo == 0 || $s->tipo == 1) && Auth::user()->can('editar', $tarea)){
                    if($s->f_revision == null){
                        $s->f_revision = $now;
                        $s->estado = 4;
                        $s->save();
                        $sinRevisar = true;
                    }
                }
            }

        }
        return $sinRevisar;
    }

    public function isResponsable($usuarios, $id){
        foreach ($usuarios as $u) {
            if($u->id == $id)
                return true;
        }
    }

    

    public function setFechaInicio($tarea){
        if($tarea->f_inicio == null){
            $datenow = Carbon::now('America/Mexico_City');
            $tarea->f_inicio = $datenow;
            $tarea->estado = 1;
            $tarea->save();
        }
    }

    public function todasPorUsuario3(Request $request){
        $id = Auth::id();
        $page = $request->query('draw');
        
        $tareas = DB::table('tareas')
                ->leftJoin('usuarios_hacen_tareas', 
                DB::raw('(`usuarios_hacen_tareas`.`tarea_id`'), 
                '=', DB::raw('`tareas`.`id` and `usuarios_hacen_tareas`.`user_id` = '.$id.')'))
                
                ->leftJoin('usuarios_revisan_tareas', 
                DB::raw('(`usuarios_revisan_tareas`.`tarea_id`'), 
                '=', DB::raw('`tareas`.`id` and `usuarios_revisan_tareas`.`user_id` = '.$id.')'))
                
                ->where('created_by','=', ''.$id)
                ->select('id', 'titulo', 'prioridad', 
                        'estado', 'created_by', 'usuarios_hacen_tareas.user_id as operativo', 
                        'usuarios_revisan_tareas.user_id as responsable')
                ->orderBy('id', 'desc')
                ->paginate(2);//->setPageName('draw');
        
        //$tareas->resolveCurrentPage('draw');
        //$tareas->setPageName('draw');
        
        //$tareas3 = array('data' => $tareas->getCollection(), 'recordsTotal' => $tareas->total());
        //$tareas3['recordsTotal'] = $tareas2->total();
        //$tareas3
        //return $tareas2;//->toSql();
        
        //$diferentes = $tareas->getCollection()->unique('id');

        //$tareas->setCollection($diferentes);
        $json = json_encode($tareas);
        $json = trim($json, '}'); 
        //return $json."}";
        $total =  ', "recordsTotal":'.strval($tareas->total())."}";
        return $json.$total;
    }


    public function todasPorUsuario(Request $request){
        $id = Auth::id();
        $limit = $request->input('length', '10');
        $page = $request->query('start');
        $start = $request->query('page', 0);
        
        $totalRegistros = DB::table('tareas')
                ->leftJoin('usuarios_hacen_tareas', 
                DB::raw('(`usuarios_hacen_tareas`.`tarea_id`'), 
                '=', DB::raw('`tareas`.`id` and `usuarios_hacen_tareas`.`user_id` = '.$id.')'))
                
                ->leftJoin('usuarios_revisan_tareas', 
                DB::raw('(`usuarios_revisan_tareas`.`tarea_id`'), 
                '=', DB::raw('`tareas`.`id` and `usuarios_revisan_tareas`.`user_id` = '.$id.')'))
                
                ->where('created_by','=', ''.$id)
                ->orWhere('usuarios_hacen_tareas.user_id', '=', $id)
                ->orWhere('usuarios_revisan_tareas.user_id', '=', $id)
                ->count('tareas.id');
                
        $tareas = DB::table('tareas')
                    ->leftJoin('usuarios_hacen_tareas', 
                    DB::raw('(`usuarios_hacen_tareas`.`tarea_id`'), 
                    '=', DB::raw('`tareas`.`id` and `usuarios_hacen_tareas`.`user_id` = '.$id.')'))
                    
                    ->leftJoin('usuarios_revisan_tareas', 
                    DB::raw('(`usuarios_revisan_tareas`.`tarea_id`'), 
                    '=', DB::raw('`tareas`.`id` and `usuarios_revisan_tareas`.`user_id` = '.$id.')'))
                    
                    ->leftJoin('categorias', 'tareas.categoria_id', '=', 'categorias.id')
                    
                    ->where('created_by','=', ''.$id)
                    ->orWhere('usuarios_hacen_tareas.user_id', '=', $id)
                    ->orWhere('usuarios_revisan_tareas.user_id', '=', $id)
                    ->select('tareas.id', 'titulo', 'prioridad', 
                    'estado', 'created_by', 'usuarios_hacen_tareas.user_id as operativo', 
                    'usuarios_revisan_tareas.user_id as responsable', 'categorias.nombre as categoria')
                    ->limit($limit)
                    ->offset($start)
                    ->orderBy('tareas.id', 'desc')
                    ->get();

        $totalFiltered = $tareas->count();
        
        $data = (!empty($tareas))? $tareas:array();
        $json_data = array(
            "draw"            => intval($request->query('page', 0)),  
            "recordsTotal"    => intval($totalRegistros),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
        );
            
        
        return response()->json($json_data);
    }

    public function tareasrelacionadas(){
        return view ('ListaTareasUser');
    }

    public function todasPorUsuario2(){
        $id = Auth::id();
        /*$tareas = DB::table('tareas')
            ->leftJoin('usuarios_hacen_tareas', 'tareas.id', '=', 'usuarios_hacen_tareas.tarea_id')
            ->where('user_id', $id)
            ->select('id', 'titulo', 'prioridad', 'estado', 'f_asignacion', 'f_limite')
            ->get();
        */
        $tareas = DB::table('tareas')
                    ->leftJoin('usuarios_hacen_tareas', 
                    DB::raw('(`usuarios_hacen_tareas`.`tarea_id`'), 
                    '=', DB::raw('`tareas`.`id` and `usuarios_hacen_tareas`.`user_id` = '.$id.')'))
                    
                    ->leftJoin('usuarios_revisan_tareas', 
                    DB::raw('(`usuarios_revisan_tareas`.`tarea_id`'), 
                    '=', DB::raw('`tareas`.`id` and `usuarios_revisan_tareas`.`user_id` = '.$id.')'))
                    
                    ->leftJoin('categorias', 'tareas.categoria_id', '=', 'categorias.id')
                    
                    ->leftJoin('sucursales', 'sucursal_id', '=', 'sucursales.id')
                    ->where('created_by','=', ''.$id)
                    ->orWhere('usuarios_hacen_tareas.user_id', '=', $id)
                    ->orWhere('usuarios_revisan_tareas.user_id', '=', $id)
                    ->select('tareas.id', 'titulo', 'prioridad', 
                    'tareas.estado', 'created_by', 'usuarios_hacen_tareas.user_id as operativo', 
                    'usuarios_revisan_tareas.user_id as responsable', 
                    'categorias.nombre as categoria', 'cve as cve_suc', 
                    'sucursales.nombre as sucursal')
                    ->orderBy('tareas.id', 'desc')
                    ->get();
            //return $tareas;

        return view('ListaTareasUser2', ["tareas" => $tareas]);
    }

    public function todasPorUsuario4(){
        $id = Auth::id();
        /*$tareas = DB::table('tareas')
            ->leftJoin('usuarios_hacen_tareas', 'tareas.id', '=', 'usuarios_hacen_tareas.tarea_id')
            ->where('user_id', $id)
            ->select('id', 'titulo', 'prioridad', 'estado', 'f_asignacion', 'f_limite')
            ->get();
        */
        $tareas = DB::table('tareas')
                    ->leftJoin('usuarios_hacen_tareas', 
                    DB::raw('(`usuarios_hacen_tareas`.`tarea_id`'), 
                    '=', DB::raw('`tareas`.`id` and `usuarios_hacen_tareas`.`user_id` = '.$id.')'))
                    
                    ->leftJoin('usuarios_revisan_tareas', 
                    DB::raw('(`usuarios_revisan_tareas`.`tarea_id`'), 
                    '=', DB::raw('`tareas`.`id` and `usuarios_revisan_tareas`.`user_id` = '.$id.')'))
                    
                    ->leftJoin('categorias', 'tareas.categoria_id', '=', 'categorias.id')
                    
                    ->where('created_by','=', ''.$id)
                    ->orWhere('usuarios_hacen_tareas.user_id', '=', $id)
                    ->orWhere('usuarios_revisan_tareas.user_id', '=', $id)
                    ->select('tareas.id', 'titulo', 'prioridad', 
                    'estado', 'created_by', 'usuarios_hacen_tareas.user_id as operativo', 
                    'usuarios_revisan_tareas.user_id as responsable', 'categorias.nombre as categoria')
                    ->orderBy('tareas.id', 'desc')
                    ->get();
        

        return view('ListaTareasUser3', ["tareas" => $tareas]);
    }

}
