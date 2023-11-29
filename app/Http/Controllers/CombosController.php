<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CombosPYC;
use App\Models\CombosDetPYC;
use App\Models\CombosSucPYC;
use App\Models\UserPyc;
use App\Models\ComboDetMKS;
use App\Models\ComboMKS;
use Illuminate\Support\Facades\DB;

class CombosController extends Controller
{
    public function crearPreCombos(Request $request){
        $datos = $request->input('datos',[]);
        $sucursales = $datos['sucSelected'];
        $articulos = $datos['arts'];

    if(is_null($sucursales) || count($sucursales) < 1 ||
           is_null($articulos) || count($articulos) < 1){
            return response()->json(array(
                'code'      =>  421,
                'message'   =>  'No se seleccionó ninguna sucursal',
                'error'     =>  'No se seleccionó ninguna sucursal',
            ), 421);
        }


          ///aqui empieza la insercion para combos en general

        DB::beginTransaction();
        $combos_pyc = new CombosPYC;
        $combos_pyc->status=-1;
        $combos_pyc->numProm = '';
        $combos_pyc->desProm = strtoupper($datos['nombre']);
        $combos_pyc->fec_ini = $datos['fec_ini'];
        $combos_pyc->fec_fin = $datos['fec_fin'];
        $combos_pyc->hra_ini = '010000';
        $combos_pyc->hra_fin = '235959';
        $combos_pyc->NumArtCom = count($articulos);
        $combos_pyc->NumArtReg = 1;
        
        $combos_pyc->inc_similares = is_null($datos['aplicaSim']) ? 'N' : 'S' ;

        $combos_pyc->tpoProm = $datos['tipo'];

        $combos_pyc->cte = is_null($datos['cliente']) ? '         ' : 
        str_pad(strval($datos['cliente']), 9, "0", STR_PAD_LEFT);

        $combos_pyc->retail = $datos['retail'];
        $combos_pyc->con_pag = is_null($datos['condPago']) ? '     ' 
        : $datos['condPago'];
        $combos_pyc->seg_0 = is_null($datos['seg1']) ? '   ' 
        : $datos['seg1'];
        $combos_pyc->seg_1 = is_null($datos['seg2']) ? '   ' 
        : $datos['seg2'];
        $combos_pyc->seg_2 = is_null($datos['seg3']) ? '   ' 
        : $datos['seg3'];
        $combos_pyc->seg_3 = is_null($datos['seg4']) ? '   ' 
        : $datos['seg4'];
        $combos_pyc->seg_4 = '   ';

        if($datos['limPzs']>=1){
            $combos_pyc->usa_limite = 'S';
        }else{
            $combos_pyc->usa_limite = 'N';
        }

        $combos_pyc->uds_limite = is_null($datos['limPzs']) ?
        0 : $datos['limPzs'];
        $combos_pyc->uds_vendidas = 0;
        $combos_pyc->uds_por_cte = is_null($datos['udsVenta']) ? 
        0 : $datos['udsVenta'];
        $combos_pyc->u_alt = $datos['u_alta'];
        if($datos['tipo']==2){
            $combos_pyc->proveedor = '        ';
            $combos_pyc->folio_ac = '    ';
        }else{
            $combos_pyc->proveedor = $datos['proveedor'];
            $combos_pyc->folio_ac = $datos['folioAcuerdo'];
        }
        $combos_pyc->paga = $datos['paga'];
        $combos_pyc->boletin = $datos['boletin'];
        $combos_pyc->autoriza = '';
        $combos_pyc->suc_prec_base = $datos['precBase'];
        $combos_pyc->indicador = $datos['indicador'];

        try{
            $combos_pyc->save();
        }catch(Throwable $e){
            DB::rollBack();
            return response()->json(array(
                'code'      =>  421,
                'message'   =>  'Ocurrió un error al guardar, intentelo nuevamente',
                'error'     =>  'Ocurrió un error al guardar, intentelo nuevamente',
            ), 421);
        }

       //insertando sucursales seleccionadas 
       foreach ($sucursales as $suc) {
        $sucSelected = new CombosSucPYC;
        $sucSelected->cmb_id = $combos_pyc->id;
        $sucSelected->suc = $suc;
        $sucSelected->save();
        //return response()->json($sucSelected);
    }

        ////insertando detalle 
        //for($i=0;$i<$value['cobradas'];$i++){
        foreach ($articulos as $art => $value) {
            $invdcm = new CombosDetPYC;
            $invdcm->id_pyc_cmb = $combos_pyc->id;
            $invdcm->status = 1;
            $invdcm->cve_art = $value['cve'];
            $invdcm->des_art = $value['des1'];
            $invdcm->emp=$value['emp_cob'];
            $invdcm->fac_sal=1;

            if($datos['tipo'] == 3){
                $invdcm->precio_0 = number_format($value['regaladas'], 2, '.', ',');
            }else{
                $invdcm->precio_0 = $value['regaladas'];
            }

            $invdcm->precio_1 = 0.0;
            $invdcm->precio_2 = 0.0;
            $invdcm->precio_3 = 0.0;
            $invdcm->precio_4 = $value['desc_reg'];

            $invdcm->cantidad=$value['cobradas'];
            $invdcm->dsc=0.0;
            $invdcm->tpoEmp=0;

            $invdcm->save();
        }
        
        DB::commit();
        return response()->json($combos_pyc);
}


public function editarPreCombo(Request $request){
    $idprom = $request->input('idprom',"0");
    $datos = $request->input('datos',[]);
    $sucursales = $datos['sucSelected'];
    $articulos = $datos['arts'];

    //return response()->json($datos['arts']);

    if(is_null($sucursales) || count($sucursales) < 1 ||
       is_null($articulos) || count($articulos) < 1){
        return response()->json(array(
            'code'      =>  421,
            'message'   =>  'No se seleccionó ninguna sucursal o ningún artículo',
            'error'     =>  'No se seleccionó ninguna sucursal o ningún artículo',
        ), 421);
    }

    //Recuperando la cabecera
    $combos_pyc = CombosPYC::where('id',$idprom)->first();


    //Actualizando la cabecera
        //$combos_pyc = new CombosPYC;
        $combos_pyc->status=-1;
        $combos_pyc->numProm = '';
        $combos_pyc->desProm = strtoupper($datos['nombre']);
        $combos_pyc->fec_ini = $datos['fec_ini'];
        $combos_pyc->fec_fin = $datos['fec_fin'];
        $combos_pyc->hra_ini = '010000';
        $combos_pyc->hra_fin = '235959';
        $combos_pyc->NumArtCom = count($articulos);
        $combos_pyc->NumArtReg = 1;
        
        $combos_pyc->inc_similares = is_null($datos['aplicaSim']) ? 'N' : 'S' ;

        $combos_pyc->tpoProm = $datos['tipo'];

        $combos_pyc->cte = is_null($datos['cliente']) ? '         ' : 
        str_pad(strval($datos['cliente']), 9, "0", STR_PAD_LEFT);

        $combos_pyc->retail = $datos['retail'];
        $combos_pyc->con_pag = is_null($datos['condPago']) ? '     ' 
        : $datos['condPago'];
        $combos_pyc->seg_0 = is_null($datos['seg1']) ? '   ' 
        : $datos['seg1'];
        $combos_pyc->seg_1 = is_null($datos['seg2']) ? '   ' 
        : $datos['seg2'];
        $combos_pyc->seg_2 = is_null($datos['seg3']) ? '   ' 
        : $datos['seg3'];
        $combos_pyc->seg_3 = is_null($datos['seg4']) ? '   ' 
        : $datos['seg4'];
        $combos_pyc->seg_4 = '   ';

        if($datos['limPzs']>=1){
            $combos_pyc->usa_limite = 'S';
        }else{
            $combos_pyc->usa_limite = 'N';
        }

        $combos_pyc->uds_limite = is_null($datos['limPzs']) ?
        0 : $datos['limPzs'];
        $combos_pyc->uds_vendidas = 0;
        $combos_pyc->uds_por_cte = is_null($datos['udsVenta']) ? 
        0 : $datos['udsVenta'];
        if($datos['tipo']==2){
            $combos_pyc->proveedor = '        ';
            $combos_pyc->folio_ac = '    ';
        }else{
            $combos_pyc->proveedor = $datos['proveedor'];
            $combos_pyc->folio_ac = $datos['folioAcuerdo'];
        }
        $combos_pyc->paga = $datos['paga'];
        $combos_pyc->boletin = $datos['boletin'];
        $combos_pyc->autoriza = '';
        $combos_pyc->suc_prec_base = $datos['precBase'];
        $combos_pyc->indicador = $datos['indicador'];

        try{
            $combos_pyc->save();
        }catch(Throwable $e){
            DB::rollBack();
            return response()->json(array(
                'code'      =>  421,
                'message'   =>  'Ocurrió un error al guardar, intentelo nuevamente',
                'error'     =>  'Ocurrió un error al guardar, intentelo nuevamente',
            ), 421);
        }
    

    //Actualizando las tablas de sucursales
    try{
        $eliminadas = CombosSucPYC::where('cmb_id', $idprom)->delete();
    }catch(Throwable $e){
        DB::rollBack();
        return response()->json(array(
            'code'      =>  421,
            'message'   =>  'Ocurrió un error al guardar, intentelo nuevamente',
            'error'     =>  'Ocurrió un error al guardar, intentelo nuevamente',
        ), 421);
    }

    foreach ($sucursales as $suc) {
        $sucSelected = new CombosSucPYC;
        $sucSelected->cmb_id = $combos_pyc->id;
        $sucSelected->suc = $suc;
        $sucSelected->save();
        //return response()->json($sucSelected);
    }

    //Actualizando el detalle
     try{
        $eliminadas = CombosDetPYC::where('id_pyc_cmb', $idprom)->delete();
    }catch(Throwable $e){
        DB::rollBack();
        return response()->json(array(
            'code'      =>  421,
            'message'   =>  'Ocurrió un error al guardar, intentelo nuevamente',
            'error'     =>  'Ocurrió un error al guardar, intentelo nuevamente',
        ), 421);
    }

    ////insertando detalle 
        //for($i=0;$i<$value['cobradas'];$i++){
            foreach ($articulos as $art => $value) {
                $invdcm = new CombosDetPYC;
                $invdcm->id_pyc_cmb = $combos_pyc->id;
                $invdcm->status = 1;
                $invdcm->cve_art = $value['cve'];
                $invdcm->des_art = $value['des1'];
                $invdcm->emp=$value['emp_cob'];
                $invdcm->fac_sal=1;
    
                if($datos['tipo'] == 3){
                    $invdcm->precio_0 =  number_format($value['regaladas'], 2, '.', ',');
                }else{
                    $invdcm->precio_0 = $value['regaladas'];
                }
    
                $invdcm->precio_1 = 0.0;
                $invdcm->precio_2 = 0.0;
                $invdcm->precio_3 = 0.0;
                $invdcm->precio_4 = $value['desc_reg'];
    
                $invdcm->cantidad=$value['cobradas'];
                $invdcm->dsc=0.0;
                $invdcm->tpoEmp=0;
    
                $invdcm->save();
            }
            
            DB::commit();
            return response()->json($combos_pyc);

}

public function denegarCombo(Request $request){
    $idprom = $request->input("idprom","-1");
    $comprador = $request->input("compr","-1");
    //$usuario = UserPyc::where('cve_corta', $comprador)->first();

    $datos1 = explode(" ", $idprom);
    //return print_r($datos);
    $id=$datos1[0];

    $tipo=$datos1[1];


    $combos_pyc = CombosPYC::where('id',$id)->first();
    $combos_pyc->status = 2;
    try{
        $combos_pyc->save();
    }catch(Throwable $e){
        DB::rollBack();
        return response()->json(array(
            'code'      =>  421,
            'message'   =>  'Usuario sin permisos para denegar la promoción',
            'error'     =>  'Usuario sin permisos para denegar la promoción',
        ), 421);
    }
    return response()->json($combos_pyc);

}

//aqui empieza la creacion de los combos
public function creaComboMks(Request $request)
{
    //$promo = new PromocionMKS;
    $idprom = $request->input("idprom","-1");
    $comprador = $request->input("compr","-1");
    $usuario = UserPyc::where('cve_corta', $comprador)->first();
    $datos1 = explode(" ", $idprom);
    //return print_r($datos);
    $id=$datos1[0];

    $tipo=$datos1[1];

    if($tipo== 2){
        //return $tipo;
        $consecutivo = DB::table('invhcm')
         ->select(DB::raw('isnull(MAX( substring(NumProm,2,7)),0)+1 as numProm'))
         //->where('modulo', 'P')
         ->first();
         //->get();
         $consec_aplicar = strval($consecutivo->numProm);
         $size_actual = strlen($consec_aplicar);
         for ($i= $size_actual; $i < 7; $i++) { 
             $consec_aplicar = '0'.$consec_aplicar;
         }
         $consec_aplicar = 'Q'.$consec_aplicar;
         //return response()->json($consec_aplicar);
         DB::beginTransaction();

         $combos_pyc = CombosPYC::where('id',$id)->first();
    $sucursales = CombosSucPYC::where('cmb_id', $id)->get()->toArray();
    $dat = '';
    $articulos = CombosDetPYC::where('id_pyc_cmb',$id)->get();

    //Insertando registros en invhcm por cada sucursal
    foreach ($sucursales as $key => $value) {
        $dat.= $value['suc'].$consec_aplicar.' , ';
        $promo = new ComboMKS;
        $promo->ibuff = '10000';
        $promo->cia = 'MAB';
        $promo->alm = $value['suc'];
        $promo->suc = $value['suc'];
        $promo->NumProm = $consec_aplicar;
        $promo->DesProm = $combos_pyc->desProm;
        $promo->fec_ini = $combos_pyc->fec_ini;
        $promo->fec_fin = $combos_pyc->fec_fin;
        $promo->status = '1';

        //for para sumas total de articulos
        $nArt=0;
        foreach ($articulos as $key => $v) {
            $nArt +=$v['cantidad'];
        }
        $promo->NumArtCom=$nArt;
        //$promo->NumArtCom=count($articulos);
        $promo->NumArtReg='1';
        $promo->SelPor = '0';
        $promo->tipo_precio='0';
        $promo->tipo_precio_reg='0';
        $promo->inc_similares = $combos_pyc->inc_similares;
        $promo->cte = $combos_pyc->cte;
        $promo->dep_sur =$value['suc'].'AUT';
            
        $promo->con_pag = $combos_pyc->con_pag;
        $promo->seg_0 = $combos_pyc->seg_0;
        $promo->seg_1 = $combos_pyc->seg_1;
        $promo->seg_2 = $combos_pyc->seg_2;
        $promo->seg_3 = $combos_pyc->seg_3;
        $promo->seg_4 = '   ';
        $promo->giro_0 = '   ';
        $promo->giro_1 = '   ';
        $promo->giro_2 = '   ';
        $promo->giro_3 = '   ';
        $promo->giro_4 = '   ';
        if($combos_pyc->uds_limite>0){
            $promo->usa_limite = 'S';
        }else{
            $promo->usa_limite = 'N';
        }
        $promo->uds_limite = $combos_pyc->uds_limite;
        $promo->uds_vendidas = $combos_pyc->uds_vendidas;
        $promo->uds_por_cte = $combos_pyc->uds_por_cte;
        $promo->f_alt = date("Ymd");
        $promo->h_alt = date("His");
        $promo->u_alt = $comprador;
        $promo->f_mod = date("Ymd");
        $promo->h_mod = date("His");
        $promo->u_mod = $comprador;

        try{
           
            $promo->save();
            ;
        }catch(Throwable $e){
            DB::rollBack();
            return response()->json(array(
                'code'      =>  421,
                'message'   =>  'Ocurrió un error al guardar, intentelo nuevamente',
                'error'     =>  'Ocurrió un error al guardar, intentelo nuevamente',
            ), 421);
        }

        //Insertando el detalle
        //Insertando registros en invdcm por cada articulo

        $npar = 0;
        foreach ($articulos as $key => $value2) {
            $det_prom = new ComboDetMKS;

            $det_prom->ibuff = '     ';
            $det_prom->cia = 'MAB';
            $det_prom->alm = $value['suc'];
            $det_prom->suc = $value['suc'];
            $det_prom->NumProm = $consec_aplicar;
            $det_prom->NPar = str_pad(strval($npar), 4, "0", STR_PAD_LEFT);
            $det_prom->RenExcep = 'N';
            $det_prom->RenEnCombo = str_pad(strval($npar), 2, "0", STR_PAD_LEFT);
            if($npar==count($articulos)-1 && $value2['cantidad']==1){
                $det_prom->TipoRen = 1;
            }else{
                $det_prom->TipoRen = 0;
            }
            $det_prom->cve_art = $value2['cve_art'];
            $det_prom->des_art = $value2['des_art'];
            $det_prom->Emp = 'PZA';
            $det_prom->fac_sal=1;
            $det_prom->precio_0 = $value2['precio_0'];
            $det_prom->precio_1 = $value2['precio_1'];
            $det_prom->precio_2 = $value2['precio_2'];
            $det_prom->precio_3 = $value2['precio_3'];
            $det_prom->precio_4 = 0;
            $det_prom->Cantidad = $value2['cantidad'];
            $det_prom->dsc = 0;
            $det_prom->TpoEmp = '0';
            $det_prom->lin = '    ';
            $det_prom->s_lin = '    ';
            $det_prom->fam = '    ';
            $det_prom->s_fam = '    ';
            $det_prom->marca = '        ';
            $det_prom->temp = '    ';
            $det_prom->prv = '         ';
            $det_prom->Id_modelo = '        ';
            $det_prom->cte = '         ';
            $det_prom->seg = '   ';
            $det_prom->giro = '   ';

            try{
                $det_prom->save();
                ;
            }catch(Throwable $e){
                DB::rollBack();
                return response()->json(array(
                    'code'      =>  421,
                    'message'   =>  'Ocurrió un error al guardar, intentelo nuevamente',
                    'error'     =>  'Ocurrió un error al guardar, intentelo nuevamente',
                ), 421);
            }
            $npar = $npar +1;
            
            //return response()->json($det_prom->art_reg);
            //return response()->json($value['art_reg']);

        }//aqui termina el foreach de los articulos a ingresar
    }//aqui termina el foreach de las sucursales
    $combos_pyc->numProm = $consec_aplicar;
        $combos_pyc->autoriza = $comprador;
        $combos_pyc->status = 1;
        try{
            $combos_pyc->save();
        }catch(Throwable $e){
            DB::rollBack();
            return response()->json(array(
                'code'      =>  421,
                'message'   =>  'Ocurrió un error al guardar, intentelo nuevamente',
                'error'     =>  'Ocurrió un error al guardar, intentelo nuevamente',
            ), 421);
        }
    DB::commit();
    return response()->json($combos_pyc);
    }//aqui termina el if de tipo 2

    //aqui comienza el if de tipo 3
    if($tipo==3){
        $articulos = CombosDetPYC::where('id_pyc_cmb',$id)->get();
        $combos_pyc = CombosPYC::where('id',$id)->first();
        $sucursales = CombosSucPYC::where('cmb_id', $id)->get()->toArray();

        for($i=0; $i<count($articulos); $i++){
        $consecutivo = DB::table('invhcm')
         ->select(DB::raw('isnull(MAX( substring(NumProm,2,7)),0)+1 as numProm'))
         //->where('modulo', 'P')
         ->first();
         //->get();
         $consec_aplicar = strval($consecutivo->numProm);
         $size_actual = strlen($consec_aplicar);
         for ($j= $size_actual; $j < 7; $j++) { 
             $consec_aplicar = '0'.$consec_aplicar;
         }
         $consec_aplicar = 'Q'.$consec_aplicar;
         //return response()->json($consec_aplicar);
         DB::beginTransaction();

        $dat = '';
        
    //Insertando registros en invhcm por cada sucursal
    foreach ($sucursales as $key => $value) {
        $dat.= $value['suc'].$consec_aplicar.' , ';
        $promo = new ComboMKS;
        $promo->ibuff = '10000';
        $promo->cia = 'MAB';
        $promo->alm = $value['suc'];
        $promo->suc = $value['suc'];
        $promo->NumProm = $consec_aplicar;
        $promo->DesProm = trim($combos_pyc->desProm)." CLAVE ".$articulos[$i]->cve_art;
        $promo->fec_ini = $combos_pyc->fec_ini;
        $promo->fec_fin = $combos_pyc->fec_fin;
        $promo->status = '1';
        $promo->NumArtCom=$articulos[$i]->cantidad;
        $promo->NumArtReg='1';
        $promo->SelPor = '0';
        $promo->tipo_precio='0';
        $promo->tipo_precio_reg='0';
        $promo->inc_similares = $combos_pyc->inc_similares;
        $promo->cte = $combos_pyc->cte;
        $promo->dep_sur =$value['suc'].'AUT';
            
        $promo->con_pag = $combos_pyc->con_pag;
        $promo->seg_0 = $combos_pyc->seg_0;
        $promo->seg_1 = $combos_pyc->seg_1;
        $promo->seg_2 = $combos_pyc->seg_2;
        $promo->seg_3 = $combos_pyc->seg_3;
        $promo->seg_4 = '   ';
        $promo->giro_0 = '   ';
        $promo->giro_1 = '   ';
        $promo->giro_2 = '   ';
        $promo->giro_3 = '   ';
        $promo->giro_4 = '   ';
        if($combos_pyc->uds_limite>0){
            $promo->usa_limite = 'S';
        }else{
            $promo->usa_limite = 'N';
        }
        $promo->uds_limite = $combos_pyc->uds_limite;
        $promo->uds_vendidas = $combos_pyc->uds_vendidas;
        $promo->uds_por_cte = $combos_pyc->uds_por_cte;
        $promo->f_alt = date("Ymd");
        $promo->h_alt = date("His");
        $promo->u_alt = $comprador;
        $promo->f_mod = date("Ymd");
        $promo->h_mod = date("His");
        $promo->u_mod = $comprador;

        try{         
            $promo->save();
        }catch(Throwable $e){
            DB::rollBack();
            return response()->json(array(
                'code'      =>  421,
                'message'   =>  'Ocurrió un error al guardar, intentelo nuevamente',
                'error'     =>  'Ocurrió un error al guardar, intentelo nuevamente',
            ), 421);
        }
        //Insertando el detalle
        //Insertando registros en invdcm por cada articulo
        $npar = 0;
        for($k=0; $k<2;$k++){
            //for($k=0; $k<$articulos[$i]->cantidad;$k++){
            $det_prom = new ComboDetMKS;

            $det_prom->ibuff = '     ';
            $det_prom->cia = 'MAB';
            $det_prom->alm = $value['suc'];
            $det_prom->suc = $value['suc'];
            $det_prom->NumProm = $consec_aplicar;
            $det_prom->NPar = str_pad(strval($npar), 4, "0", STR_PAD_LEFT);
            $det_prom->RenExcep = 'N';
            $det_prom->RenEnCombo = str_pad(strval($npar), 2, "0", STR_PAD_LEFT);
            if($k==1){
                $det_prom->TipoRen = 1;
            }else{
                $det_prom->TipoRen = 0;
            }
            $det_prom->cve_art = $articulos[$i]->cve_art;
            $det_prom->des_art = $articulos[$i]->des_art;
            $det_prom->Emp = 'PZA';
            $det_prom->fac_sal=1;
            $det_prom->precio_0 = $articulos[$i]->precio_0/$articulos[$i]->cantidad;
            $det_prom->precio_1 = 0;
            $det_prom->precio_2 = 0;
            $det_prom->precio_3 = 0;
            $det_prom->precio_4 = 0;
            if($k==0){
                $det_prom->Cantidad =1;
            }else{
                $det_prom->Cantidad = ($articulos[$i]->cantidad)-1;
            }
            
            $det_prom->dsc = 0;
            $det_prom->TpoEmp = '0';
            $det_prom->lin = '    ';
            $det_prom->s_lin = '    ';
            $det_prom->fam = '    ';
            $det_prom->s_fam = '    ';
            $det_prom->marca = '        ';
            $det_prom->temp = '    ';
            $det_prom->prv = '         ';
            $det_prom->Id_modelo = '        ';
            $det_prom->cte = '         ';
            $det_prom->seg = '   ';
            $det_prom->giro = '   ';

            try{
                $det_prom->save();
                ;
            }catch(Throwable $e){
                DB::rollBack();
                return response()->json(array(
                    'code'      =>  421,
                    'message'   =>  'Ocurrió un error al guardar, intentelo nuevamente',
                    'error'     =>  'Ocurrió un error al guardar, intentelo nuevamente',
                ), 421);
            }
            $npar = $npar +1;
        }//aqui termina el for que inserta por cantidad de articulos

    }//aqui termina el foreach de las sucursales
    DB::commit();
    //print_r("aqui termina de insertar la cabecera numero ".$i);
    }//aqui termina el for que agrega cabecera por articulo
    $combos_pyc->numProm = $consec_aplicar;
        $combos_pyc->autoriza = $comprador;
        $combos_pyc->status = 1;
        try{
            $combos_pyc->save();
        }catch(Throwable $e){
            DB::rollBack();
            return response()->json(array(
                'code'      =>  421,
                'message'   =>  'Ocurrió un error al guardar, intentelo nuevamente',
                'error'     =>  'Ocurrió un error al guardar, intentelo nuevamente',
            ), 421);
        }
    DB::commit();
    return response()->json($combos_pyc);
    }//aqui termina el if de tipo 3

} 

}

