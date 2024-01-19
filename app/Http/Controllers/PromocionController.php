<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserMKS;
use App\Models\UserPyc;
use App\Models\PromocionPYC;
use App\Models\PromocionSucPYC;
use App\Models\PromocionDetPYC;
use App\Models\Articulo;
use App\Models\Proveedor;
use App\Models\PromocionMKS;
use App\Models\PromocionDetMKS;
use App\Models\CombosPYC;
use App\Models\CombosDetPYC;
use App\Models\CombosSucPYC;
use Illuminate\Support\Facades\DB;

class PromocionController extends Controller
{
    public function crearPrePromocion(Request $request){
        $datos = $request->input('datos',[]);
        $sucursales = $datos['sucSelected'];
        $articulos = $datos['arts'];

        //return response()->json($datos);

        if(is_null($sucursales) || count($sucursales) < 1 ||
           is_null($articulos) || count($articulos) < 1){
            return response()->json(array(
                'code'      =>  421,
                'message'   =>  'No se seleccionó ninguna sucursal',
                'error'     =>  'No se seleccionó ninguna sucursal',
            ), 421);
        }

        //Insertando la cabecera
        DB::beginTransaction();
        $promocion_pyc = new PromocionPYC;
        $promocion_pyc->status = -1;
        $promocion_pyc->numProm = '';
        $promocion_pyc->desProm = strtoupper($datos['nombre']);
        $promocion_pyc->fec_ini = $datos['fec_ini'];
        $promocion_pyc->fec_fin = $datos['fec_fin'];
        $promocion_pyc->hra_ini = '010000';
        $promocion_pyc->hra_fin = '235959';
        $promocion_pyc->inc_similares = is_null($datos['aplicaSim']) ? 'N' : 'S' ;

        //if($datos['tipo'] == 5 || $datos['tipo'] == 6)
          //  $promocion_pyc->tpoProm = 5;
        //else 
            $promocion_pyc->tpoProm = $datos['tipo'];
        
        $promocion_pyc->cte = is_null($datos['cliente']) ? '         ' : 
        str_pad(strval($datos['cliente']), 9, "0", STR_PAD_LEFT);;
        
        $promocion_pyc->retail = $datos['retail'];
        $promocion_pyc->con_pag = is_null($datos['condPago']) ? '     ' 
        : $datos['condPago'];
        $promocion_pyc->seg_0 = is_null($datos['seg1']) ? '   ' 
        : $datos['seg1'];
        $promocion_pyc->seg_1 = is_null($datos['seg2']) ? '   ' 
        : $datos['seg2'];
        $promocion_pyc->seg_2 = is_null($datos['seg3']) ? '   ' 
        : $datos['seg3'];
        $promocion_pyc->seg_3 = is_null($datos['seg4']) ? '   ' 
        : $datos['seg4'];
        $promocion_pyc->seg_4 = '   ';
        
        $promocion_pyc->uds_limite = is_null($datos['limPzs']) ?
        0 : $datos['limPzs'];
        $promocion_pyc->uds_vendidas = 0;
        $promocion_pyc->uds_por_cte = is_null($datos['udsVenta']) ? 
        0 : $datos['udsVenta'];
        $promocion_pyc->cantidad_minima = is_null($datos['cantMin']) ?
        0 : $datos['cantMin'] ;
        $promocion_pyc->compra_minima = is_null($datos['montoMin']) ?
        0 : $datos['montoMin'];
        $promocion_pyc->u_alt = $datos['u_alta'];
        $promocion_pyc->proveedor = $datos['proveedor'];
        $promocion_pyc->paga = $datos['paga'];
        $promocion_pyc->folio_ac = $datos['folioAcuerdo'];
        $promocion_pyc->boletin = $datos['boletin'];
        $promocion_pyc->autoriza = '';
        $promocion_pyc->mostrador = $datos['mostrador'];
        $promocion_pyc->suc_prec_base = $datos['precBase'];
        $promocion_pyc->numPromReg = '';
        $promocion_pyc->indicador = $datos['indicador'];
        
    
        try{
            $promocion_pyc->save();
        }catch(Throwable $e){
            DB::rollBack();
            return response()->json(array(
                'code'      =>  421,
                'message'   =>  'Ocurrió un error al guardar, intentelo nuevamente',
                'error'     =>  'Ocurrió un error al guardar, intentelo nuevamente',
            ), 421);
        }
        

        foreach ($sucursales as $suc) {
            $sucSelected = new PromocionSucPYC;
            $sucSelected->prm_id = $promocion_pyc->id;
            $sucSelected->suc = $suc;
            $sucSelected->save();
            //return response()->json($sucSelected);
        }

        //Insertando el detalle
        foreach ($articulos as $art => $value) {
            $a = DB::table('invart')
                    ->where('art', $value['cve'])
                    ->where('alm',$datos['precBase'])
                    ->first();

                   
                    //return response()->json($sucSelected);
            $prmdet = new PromocionDetPYC;
            $prmdet->id_pyc_prom = $promocion_pyc->id;
            $prmdet->status = 1;
            $prmdet->cve_art = $value['cve'];
            $prmdet->des_art = $value['des1'];
            
            //array_key_exists(array_key, array_name)
            
            $prmdet->p_dsc_0 = 0.0;
            $prmdet->p_dsc_1 = 0.0;
            $prmdet->p_dsc_2 = 0.0;
            $prmdet->Monto_Dsc = 0.0;

            //Si es promocion de precio
            if($datos['tipo'] == 1){
                //Si precio no esta capturado ponemos el del cat art
                /* $prmdet->precio_0 = is_null($value['precio1']) ? $a->precio_vta0 : round($value['precio1'], 2);
                $prmdet->precio_1 = is_null($value['precio2']) ? $a->precio_vta1 : round($value['precio2'], 2);
                $prmdet->precio_2 = is_null($value['precio3']) ? $a->precio_vta2 : round($value['precio3'], 2);
                $prmdet->precio_3 = is_null($value['precio4']) ? $a->precio_vta3 : round($value['precio4'], 2);
                $prmdet->precio_4 = is_null($value['precio5']) ? $a->precio_vta4 : round($value['precio5'], 2); */
////////////////cambios para precios por almacen 
                $prmdet->precio_0 = is_null($value['precio1']) ? 0.0 : round($value['precio1'], 2);
                $prmdet->precio_1 = is_null($value['precio2']) ? 0.0 : round($value['precio2'], 2);
                $prmdet->precio_2 = is_null($value['precio3']) ? 0.0 : round($value['precio3'], 2);
                $prmdet->precio_3 = is_null($value['precio4']) ? 0.0 : round($value['precio4'], 2);
                $prmdet->precio_4 = is_null($value['precio5']) ? 0.0 : round($value['precio5'], 2);
                
                $prmdet->sin_cargo = 'N';
                $prmdet->cobradas = 0.0;
                $prmdet->regaladas = 0.0;
                //$prmdet->art_reg = $value->cve;
                //$prmdet->emp_reg = $value->cve;
                $prmdet->fac_min_reg = 0.0;
                $prmdet->precio_reg = 0.0;
            }

            //Si es promocion de Regalo
            else if($datos['tipo'] == 5){
                //return print_r($value);
                $sub_alm='';
                $fac_min;
                if($value['emp_cob']!='PZA'){
                    
                if($value['emp_cob']=='CJA'){
                    $sub_alm=$datos['precBase'].'C';
                }if($value['emp_cob']=='PAQ'){
                    $sub_alm=$datos['precBase'].'M';
                }


                $fact = DB::table('invars')
                ->select(DB::raw('fac_minimo'))
                ->where('cve_art', $value['cve'])
                ->where('alm', $datos['precBase'])
                ->where('sub_alm', $sub_alm)
                ->first();

                $fac_min=$fact->fac_minimo;

                } else{
                    $fac_min=1;
                } 
               
                //$prmdet->cve_art = $value['cod_cob'];
                //$prmdet->des_art = $value['desc_cob'];
                $prmdet->sin_cargo = 'S';
                //$prmdet->cobradas = $value['cobradas'];
                $prmdet->cobradas = $value['cobradas']*$fac_min;
                $prmdet->regaladas = $value['regaladas'];
                $prmdet->art_reg = $value['cod_reg'];
                $prmdet->emp_reg = $value['emp_reg'];
                $prmdet->fac_min_reg = $value['fac_min_reg'];
                $prmdet->precio_reg = 0.0;
                $prmdet->desc_reg = $value['desc_reg'];

                //Si precio no esta capturado ponemos el del cat art
                /* $prmdet->precio_0 = $a->precio_vta0;
                $prmdet->precio_1 = $a->precio_vta1;
                $prmdet->precio_2 = $a->precio_vta2;
                $prmdet->precio_3 = $a->precio_vta3;
                $prmdet->precio_4 = $a->precio_vta4; */

/////////////////cambio para precios por almacen 
                $prmdet->precio_0 = 0.0;
                $prmdet->precio_1 = 0.0;
                $prmdet->precio_2 = 0.0;
                $prmdet->precio_3 = 0.0;
                $prmdet->precio_4 = 0.0;
            }
            
            //Si es promocion hibirida
            else if($datos['tipo'] == 6){
                
                    
                if($value['emp_cob']!='PZA'){
                    
                    if($value['emp_cob']=='CJA'){
                        $sub_alm=$datos['precBase'].'C';
                    }if($value['emp_cob']=='PAQ'){
                        $sub_alm=$datos['precBase'].'M';
                    }
    
    
                    $fact = DB::table('invars')
                    ->select(DB::raw('fac_minimo'))
                    ->where('cve_art', $value['cve'])
                    ->where('alm', $datos['precBase'])
                    ->where('sub_alm', $sub_alm)
                    ->first();
    
                    $fac_min=$fact->fac_minimo;
    
                    } else{
                        $fac_min=1;
                    } 
                
                //$prmdet->cve_art = $value['cod_cob'];
                //$prmdet->des_art = $value['desc_cob'];
                $prmdet->sin_cargo = 'S';
                //$prmdet->cobradas = $value['cobradas'];
                if($value['emp_cob']=='PZA'){
                    $prmdet->cobradas = $value['cobradas'];
                }else{
                    $prmdet->cobradas = $value['cobradas']*$fact->fac_minimo;
                }
                
                $prmdet->regaladas = $value['regaladas'];
                $prmdet->art_reg = $value['cod_reg'];
                $prmdet->emp_reg = $value['emp_reg'];
                $prmdet->fac_min_reg = $value['fac_min_reg'];
                $prmdet->precio_reg = 0.0;
                $prmdet->desc_reg = $value['desc_reg'];

                //Si precio no esta capturado ponemos el del cat art
                $prmdet->precio_0 = is_null($value['precio1']) ? $a->precio_vta0 : $value['precio1'];
                $prmdet->precio_1 = is_null($value['precio2']) ? $a->precio_vta1 : $value['precio2'];
                $prmdet->precio_2 = is_null($value['precio3']) ? $a->precio_vta2 : $value['precio3'];
                $prmdet->precio_3 = is_null($value['precio4']) ? $a->precio_vta3 : $value['precio4'];
                $prmdet->precio_4 = is_null($value['precio5']) ? $a->precio_vta4 : $value['precio5'];
            }
            $prmdet->save();
        }

        DB::commit();

        
        return response()->json($promocion_pyc);
    }
    

    public function getPromocionesXComprador(Request $request){

        $comprador = $request->input('compr', -1);
        $promociones = PromocionPYC::where('u_alt',$comprador)
                        ->leftJoin('cprprv', 'pyc_prmhdr.proveedor','=','cprprv.proveedor')
                        ->select('pyc_prmhdr.*','cprprv.nom as nom_prov');
                        //->orderByDesc('updated_at')
                        //->get()
                        //->toArray();
        
        $combos = CombosPYC::where('u_alt',$comprador)
                        ->leftJoin('cprprv', 'pyc_invhcm.proveedor','=','cprprv.proveedor')
                        ->select('pyc_invhcm.*','cprprv.nom as nom_prov')
                        ->union($promociones)
                        ->orderByDesc('updated_at')
                        ->get()
                        ->toArray(); 


        return response()->json($combos);
    }

    public function getAllPromociones(Request $request){
        $comprador = $request->input('compr', -1);
        $promociones = PromocionPYC::
        //where('u_alt',$comprador)
                        leftJoin('cprprv', 'pyc_prmhdr.proveedor','=','cprprv.proveedor')
                        ->select('pyc_prmhdr.*','cprprv.nom as nom_prov');
                        //->orderByDesc('updated_at')
                        //->get()
                        //->toArray();
        //return response()->json($promociones);
        $combos = CombosPYC:://where('u_alt',$comprador)
                        leftJoin('cprprv', 'pyc_invhcm.proveedor','=','cprprv.proveedor')
                        ->select('pyc_invhcm.*','cprprv.nom as nom_prov')
                        ->union($promociones)
                        ->orderByDesc('updated_at')
                        ->get()
                        ->toArray(); 


        return response()->json($combos);
    }

    public function getPromAut(Request $request){
        $comprador = $request->input('compr', -1);
        $promociones = PromocionPYC::
                        whereIn('status', [-1,0])
                        ->rightJoin('cprprv', 'pyc_prmhdr.proveedor','=','cprprv.proveedor')
                        ->select('pyc_prmhdr.*','cprprv.nom as nom_prov');
                        /* ->orderByDesc('updated_at')
                        ->get()
                        ->toArray(); */

        $combos = CombosPYC::whereIn('status', [-1,0])
                        ->leftJoin('cprprv', 'pyc_invhcm.proveedor','=','cprprv.proveedor')
                        ->select('pyc_invhcm.*','cprprv.nom as nom_prov')
                        ->union($promociones)
                        ->orderByDesc('updated_at')
                        ->get()
                        ->toArray(); 
        return response()->json($combos);
    }

    public function getDetallePromocion(Request $request){
        $idprom = $request->input("idprom","-1");
        $comprador = $request->input("compr","-1");
        
        $datos1 = explode(" ", $idprom);
        //return print_r($datos);
        $id=$datos1[0];

        $tipo=$datos1[1];

        //return print_r("tipo: ".$tipo. " idPromo:".$id );

        if($tipo==2 || $tipo==3 ){

        $combos = CombosPYC::where('id',$id)
            ->leftJoin('cprprv', 'pyc_invhcm.proveedor','=','cprprv.proveedor')
            ->select('pyc_invhcm.*','cprprv.nom as nom_prov')
            ->get()->first()
            ->toArray(); 

            $alm=substr($combos['suc_prec_base'],0,3);

        $detprom = CombosDetPYC::where('id_pyc_cmb',$id )
                    ->where('invart.alm','=',$alm)
                    ->join('invart', 'pyc_invdcm.cve_art','=','invart.art')
                    ->select('pyc_invdcm.*','invart.precio_vta0 as precio_cat_art') 
                    ->get()->toArray();

        $suc = CombosSucPYC::where('cmb_id',$id)->select('suc')->get()->toArray();

        $datos = array('prom' => $combos, 'arts' => $detprom, 'suc' => $suc );
        return response()->json($datos);

        }else{
        $promo = PromocionPYC::where('id',$id)
                    ->leftJoin('cprprv', 'pyc_prmhdr.proveedor','=','cprprv.proveedor')
                    ->select('pyc_prmhdr.*','cprprv.nom as nom_prov')
                    ->get()->first()
                    ->toArray();
        $detprom = PromocionDetPYC::where('id_pyc_prom',$id)
                    ->get()->toArray();
        $suc = PromocionSucPYC::where('prm_id',$id)->select('suc')->get()->toArray();

        //Agregando factor de empaques
        foreach ($detprom as $key => $value) {
            //Buscando el articulo en tabla invart
            $factores = DB::table('invart')
                    ->where('art', $value['cve_art'])
                    ->where('alm', $promo['suc_prec_base'])
                    ->select('cant_pre0', 'cant_pre1', 'cant_pre2', 
                        'cant_pre3', 'cant_pre4', 'precio_vta0', 'precio_vta1',
                        'precio_vta2', 'precio_vta3', 'precio_vta4'
                    )
                    ->get()
                    ->first();
            $detprom[$key]['cant_pre0'] = $factores->cant_pre0;
            $detprom[$key]['cant_pre1'] = $factores->cant_pre1;
            $detprom[$key]['cant_pre2'] = $factores->cant_pre2;
            $detprom[$key]['cant_pre3'] = $factores->cant_pre3;
            $detprom[$key]['cant_pre4'] = $factores->cant_pre4;

            $detprom[$key]['precio_vta0'] = $factores->precio_vta0;
            $detprom[$key]['precio_vta1'] = $factores->precio_vta1;
            $detprom[$key]['precio_vta2'] = $factores->precio_vta2;
            $detprom[$key]['precio_vta3'] = $factores->precio_vta3;
            $detprom[$key]['precio_vta4'] = $factores->precio_vta4;
        }

        $datos = array('prom' => $promo, 'arts' => $detprom, 'suc' => $suc );
        return response()->json($datos);
        }
    }


    public function editarPrePromocion(Request $request){
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
        $promocion_pyc = PromocionPYC::where('id',$idprom)->first();


        //Actualizando la cabecera
        //$promocion_pyc = new PromocionPYC;
        $promocion_pyc->status = -1;
        $promocion_pyc->desProm = $datos['nombre'];
        $promocion_pyc->fec_ini = $datos['fec_ini'];
        $promocion_pyc->fec_fin = $datos['fec_fin'];
        $promocion_pyc->hra_ini = '010000';
        $promocion_pyc->hra_fin = '235959';
        $promocion_pyc->inc_similares = is_null($datos['aplicaSim']) ? 'N' : 'S' ;
        $promocion_pyc->tpoProm = $datos['tipo'];
        $promocion_pyc->cte = is_null($datos['cliente']) ? '         ' : 
        str_pad(strval($datos['cliente']), 9, "0", STR_PAD_LEFT);;
        
        $promocion_pyc->retail = $datos['retail'];
        $promocion_pyc->mostrador = $datos['mostrador'];
        $promocion_pyc->con_pag = is_null($datos['condPago']) ? '     ' 
        : $datos['condPago'];
        $promocion_pyc->seg_0 = is_null($datos['seg1']) ? '   ' 
        : $datos['seg1'];
        $promocion_pyc->seg_1 = is_null($datos['seg2']) ? '   ' 
        : $datos['seg2'];
        $promocion_pyc->seg_2 = is_null($datos['seg3']) ? '   ' 
        : $datos['seg3'];
        $promocion_pyc->seg_3 = is_null($datos['seg4']) ? '   ' 
        : $datos['seg4'];
        $promocion_pyc->seg_4 = '   ';
        
        $promocion_pyc->uds_limite = is_null($datos['limPzs']) ?
        0 : $datos['limPzs'];
        $promocion_pyc->uds_por_cte = is_null($datos['udsVenta']) ? 
        0 : $datos['udsVenta'];
        $promocion_pyc->cantidad_minima = is_null($datos['cantMin']) ?
        0 : $datos['cantMin'] ;
        $promocion_pyc->compra_minima = is_null($datos['montoMin']) ?
        0 : $datos['montoMin'];
        //$promocion_pyc->u_alt = $datos['u_alta'];
        $promocion_pyc->proveedor = $datos['proveedor'];
        $promocion_pyc->uds_por_cte = is_null($datos['udsVenta']) ?
        0 : $datos['udsVenta'];
        $promocion_pyc->uds_vendidas = 0;
        $promocion_pyc->paga = $datos['paga'];
        $promocion_pyc->folio_ac = $datos['folioAcuerdo'];
        $promocion_pyc->boletin = $datos['boletin'];
        $promocion_pyc->suc_prec_base = $datos['precBase'];
        $promocion_pyc->indicador = $datos['indicador'];

        DB::beginTransaction();
        try{
            $promocion_pyc->save();
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
            $eliminadas = PromocionSucPYC::where('prm_id', $idprom)->delete();
        }catch(Throwable $e){
            DB::rollBack();
            return response()->json(array(
                'code'      =>  421,
                'message'   =>  'Ocurrió un error al guardar, intentelo nuevamente',
                'error'     =>  'Ocurrió un error al guardar, intentelo nuevamente',
            ), 421);
        }

        foreach ($sucursales as $suc) {
            $sucSelected = new PromocionSucPYC;
            $sucSelected->prm_id = $promocion_pyc->id;
            $sucSelected->suc = $suc;
            $sucSelected->save();
            //return response()->json($sucSelected);
        }

        //Actualizando el detalle
         try{
            $eliminadas = PromocionDetPYC::where('id_pyc_prom', $idprom)->delete();
        }catch(Throwable $e){
            DB::rollBack();
            return response()->json(array(
                'code'      =>  421,
                'message'   =>  'Ocurrió un error al guardar, intentelo nuevamente',
                'error'     =>  'Ocurrió un error al guardar, intentelo nuevamente',
            ), 421);
        }

        foreach ($articulos as $art => $value) {
            $a = DB::table('invart')
                    ->where('art', $value['cve'])
                    ->where('alm',$datos['precBase'])
                    ->first();

                   
                    //return response()->json($sucSelected);
            $prmdet = new PromocionDetPYC;
            $prmdet->id_pyc_prom = $promocion_pyc->id;
            $prmdet->status = 1;
            $prmdet->cve_art = $value['cve'];
            $prmdet->des_art = $value['des1'];
            
            //array_key_exists(array_key, array_name)
            
            $prmdet->p_dsc_0 = 0.0;
            $prmdet->p_dsc_1 = 0.0;
            $prmdet->p_dsc_2 = 0.0;
            $prmdet->Monto_Dsc = 0.0;

            //Si es promocion de precio
            if($datos['tipo'] == 1){
                //Si precio no esta capturado ponemos el del cat art
                /* $prmdet->precio_0 = is_null($value['precio1']) ? $a->precio_vta0 : round($value['precio1'], 2);
                $prmdet->precio_1 = is_null($value['precio2']) ? $a->precio_vta1 : round($value['precio2'], 2);
                $prmdet->precio_2 = is_null($value['precio3']) ? $a->precio_vta2 : round($value['precio3'], 2);
                $prmdet->precio_3 = is_null($value['precio4']) ? $a->precio_vta3 : round($value['precio4'], 2);
                $prmdet->precio_4 = is_null($value['precio5']) ? $a->precio_vta4 : round($value['precio5'], 2); */
                ////////////////cambios para precios por almacen 
                $prmdet->precio_0 = is_null($value['precio1']) ? 0.0 : round($value['precio1'], 2);
                $prmdet->precio_1 = is_null($value['precio2']) ? 0.0 : round($value['precio2'], 2);
                $prmdet->precio_2 = is_null($value['precio3']) ? 0.0 : round($value['precio3'], 2);
                $prmdet->precio_3 = is_null($value['precio4']) ? 0.0 : round($value['precio4'], 2);
                $prmdet->precio_4 = is_null($value['precio5']) ? 0.0 : round($value['precio5'], 2);
                
                $prmdet->sin_cargo = 'N';
                $prmdet->cobradas = 0.0;
                $prmdet->regaladas = 0.0;
                //$prmdet->art_reg = $value->cve;
                //$prmdet->emp_reg = $value->cve;
                $prmdet->fac_min_reg = 0.0;
                $prmdet->precio_reg = 0.0;
            }

            //Si es promocion de Regalo
            else if($datos['tipo'] == 5){
                //return print_r($value);
                $sub_alm='';
                $fac_min;
                if($value['emp_cob']!='PZA'){
                    
                if($value['emp_cob']=='CJA'){
                    $sub_alm=$datos['precBase'].'C';
                }if($value['emp_cob']=='PAQ'){
                    $sub_alm=$datos['precBase'].'M';
                }


                $fact = DB::table('invars')
                ->select(DB::raw('fac_minimo'))
                ->where('cve_art', $value['cve'])
                ->where('alm', $datos['precBase'])
                ->where('sub_alm', $sub_alm)
                ->first();

                $fac_min=$fact->fac_minimo;

                } else{
                    $fac_min=1;
                } 
               
                //$prmdet->cve_art = $value['cod_cob'];
                //$prmdet->des_art = $value['desc_cob'];
                $prmdet->sin_cargo = 'S';
                //$prmdet->cobradas = $value['cobradas'];
                $prmdet->cobradas = $value['cobradas']*$fac_min;
                $prmdet->regaladas = $value['regaladas'];
                $prmdet->art_reg = $value['cod_reg'];
                $prmdet->emp_reg = $value['emp_reg'];
                $prmdet->fac_min_reg = $value['fac_min_reg'];
                $prmdet->precio_reg = 0.0;
                $prmdet->desc_reg = $value['desc_reg'];

                //Si precio no esta capturado ponemos el del cat art
               /*  $prmdet->precio_0 = $a->precio_vta0;
                $prmdet->precio_1 = $a->precio_vta1;
                $prmdet->precio_2 = $a->precio_vta2;
                $prmdet->precio_3 = $a->precio_vta3;
                $prmdet->precio_4 = $a->precio_vta4; */
                
                /////////////////cambio para precios por almacen 
                $prmdet->precio_0 = 0.0;
                $prmdet->precio_1 = 0.0;
                $prmdet->precio_2 = 0.0;
                $prmdet->precio_3 = 0.0;
                $prmdet->precio_4 = 0.0;
            }
            
            //Si es promocion hibirida
            else if($datos['tipo'] == 6){
                
                    
                if($value['emp_cob']!='PZA'){
                    
                    if($value['emp_cob']=='CJA'){
                        $sub_alm=$datos['precBase'].'C';
                    }if($value['emp_cob']=='PAQ'){
                        $sub_alm=$datos['precBase'].'M';
                    }
    
    
                    $fact = DB::table('invars')
                    ->select(DB::raw('fac_minimo'))
                    ->where('cve_art', $value['cve'])
                    ->where('alm', $datos['precBase'])
                    ->where('sub_alm', $sub_alm)
                    ->first();
    
                    $fac_min=$fact->fac_minimo;
    
                    } else{
                        $fac_min=1;
                    } 
                
                //$prmdet->cve_art = $value['cod_cob'];
                //$prmdet->des_art = $value['desc_cob'];
                $prmdet->sin_cargo = 'S';
                //$prmdet->cobradas = $value['cobradas'];
                if($value['emp_cob']=='PZA'){
                    $prmdet->cobradas = $value['cobradas'];
                }else{
                    $prmdet->cobradas = $value['cobradas']*$fact->fac_minimo;
                }
                
                $prmdet->regaladas = $value['regaladas'];
                $prmdet->art_reg = $value['cod_reg'];
                $prmdet->emp_reg = $value['emp_reg'];
                $prmdet->fac_min_reg = $value['fac_min_reg'];
                $prmdet->precio_reg = 0.0;
                $prmdet->desc_reg = $value['desc_reg'];

                //Si precio no esta capturado ponemos el del cat art
                /* $prmdet->precio_0 = is_null($value['precio1']) ? $a->precio_vta0 : $value['precio1'];
                $prmdet->precio_1 = is_null($value['precio2']) ? $a->precio_vta1 : $value['precio2'];
                $prmdet->precio_2 = is_null($value['precio3']) ? $a->precio_vta2 : $value['precio3'];
                $prmdet->precio_3 = is_null($value['precio4']) ? $a->precio_vta3 : $value['precio4'];
                $prmdet->precio_4 = is_null($value['precio5']) ? $a->precio_vta4 : $value['precio5']; */
                $prmdet->precio_0 = is_null($value['precio1']) ? 0.0 : $value['precio1'];
                $prmdet->precio_1 = is_null($value['precio2']) ? 0.0 : $value['precio2'];
                $prmdet->precio_2 = is_null($value['precio3']) ? 0.0 : $value['precio3'];
                $prmdet->precio_3 = is_null($value['precio4']) ? 0.0 : $value['precio4'];
                $prmdet->precio_4 = is_null($value['precio5']) ? 0.0 : $value['precio5'];
            }
            $prmdet->save();
        }

        DB::commit();

        
        return response()->json($promocion_pyc);
    }

    public function softDeletePromocion(Request $request){
        $idprom = $request->input('idprom',"0");
        $datos1 = explode(" ", $idprom);
        //return print_r($datos);
        $id=$datos1[0];
        $tipo=$datos1[1];
        //

        if($tipo==2 || $tipo==3){
            $combos_pyc = CombosPYC::where('id',$id)->first();
            $combos_pyc->status = "-2";
            $combos_pyc->save();
            return response()->json($combos_pyc);
        }
        else{
            $promocion_pyc = PromocionPYC::where('id',$id)->first();
            $promocion_pyc->status = "-2";
            $promocion_pyc->save();
            return response()->json($promocion_pyc);
        }
        
    }



    //Autorizar
    public function getDetallePromocionAut(Request $request){
        $idprom = $request->input("idprom","-1");
        $comprador = $request->input("compr","-1");
        $usuario = UserPyc::where('cve_corta', $comprador)->first();

        $datos1 = explode(" ", $idprom);
        //return print_r($datos);
        $id=$datos1[0];

        $tipo=$datos1[1];

        /* $permiso = DB::table('pyc_roles_permisos')
                            ->where('rol_id',$usuario->id)
                            ->first();
        if(is_null($permiso)){
            return 'es lamentable';
        } */
        if($tipo==2 || $tipo==3 ){
            $combos = CombosPYC::where('id',$id)->first();
            if($combos->status == -1){
                $combos->status = 0;
                $combos->save();
            }

            $combos = CombosPYC::where('id',$id)
                ->leftJoin('cprprv', 'pyc_invhcm.proveedor','=','cprprv.proveedor')
                ->select('pyc_invhcm.*','cprprv.nom as nom_prov')
                ->get()->first()
                ->toArray(); 
    
                $alm=substr($combos['suc_prec_base'],0,3);
    
            $detprom = CombosDetPYC::where('id_pyc_cmb',$id )
                        ->where('invart.alm','=',$alm)
                        ->join('invart', 'pyc_invdcm.cve_art','=','invart.art')
                        ->select('pyc_invdcm.*','invart.precio_vta0 as precio_cat_art') 
                        ->get()->toArray();
    
            $suc = CombosSucPYC::where('cmb_id',$id)->select('suc')->get()->toArray();
    
            $datos = array('prom' => $combos, 'arts' => $detprom, 'suc' => $suc );
            return response()->json($datos);
    
            }else{

        $promo = PromocionPYC::where('id',$id)->first();
        if($promo->status == -1){
            $promo->status = 0;
            $promo->save();
        }
        
        $promo = PromocionPYC::where('id',$id)
                    ->leftJoin('cprprv', 'pyc_prmhdr.proveedor','=','cprprv.proveedor')
                    ->select('pyc_prmhdr.*','cprprv.nom as nom_prov')
                    ->get()->first()
                    ->toArray();
        $detprom = PromocionDetPYC::where('id_pyc_prom',$id)
                    ->get()->toArray();
        $suc = PromocionSucPYC::where('prm_id',$id)->select('suc')->get()->toArray();

        //Agregando factor de empaques
        foreach ($detprom as $key => $value) {
            //Buscando el articulo en tabla invart
            $factores = DB::table('invart')
                    ->where('art', $value['cve_art'])
                    ->where('alm', $promo['suc_prec_base'])
                    ->select('cant_pre0', 'cant_pre1', 'cant_pre2', 
                        'cant_pre3', 'cant_pre4', 'precio_vta0', 'precio_vta1',
                        'precio_vta2', 'precio_vta3', 'precio_vta4'
                    )
                    ->get()
                    ->first();
            $detprom[$key]['cant_pre0'] = $factores->cant_pre0;
            $detprom[$key]['cant_pre1'] = $factores->cant_pre1;
            $detprom[$key]['cant_pre2'] = $factores->cant_pre2;
            $detprom[$key]['cant_pre3'] = $factores->cant_pre3;
            $detprom[$key]['cant_pre4'] = $factores->cant_pre4;

            $detprom[$key]['precio_vta0'] = $factores->precio_vta0;
            $detprom[$key]['precio_vta1'] = $factores->precio_vta1;
            $detprom[$key]['precio_vta2'] = $factores->precio_vta2;
            $detprom[$key]['precio_vta3'] = $factores->precio_vta3;
            $detprom[$key]['precio_vta4'] = $factores->precio_vta4;
        }

        $datos = array('prom' => $promo, 'arts' => $detprom, 'suc' => $suc );
        return response()->json($datos);
    }
    }

    public function creaPromoMks(Request $request)
    {
        //$promo = new PromocionMKS;
        $idprom = $request->input("idprom","-1");
        $comprador = $request->input("compr","-1");
        $usuario = UserPyc::where('cve_corta', $comprador)->first();
        $datos1 = explode(" ", $idprom);
        //return print_r($datos);
        $id=$datos1[0];

        $tipo=$datos1[1];

        //return response()->json($comprador);

        /* if(is_null($usuario)){
            try{
                $promo->save();
                
            }catch(Throwable $e){
                DB::rollBack();
                return response()->json(array(
                    'code'      =>  421,
                    'message'   =>  'Usuario no encontrado',
                    'error'     =>  'Usuario no encontrado',
                ), 421);
            }
        }

        $permiso = DB::table('pyc_roles_permisos')
                            ->where('rol_id',$usuario->id)
                            ->first();
        if(is_null($permiso)){
            try{
                $promo->save();
                ;
            }catch(Throwable $e){
                DB::rollBack();
                return response()->json(array(
                    'code'      =>  421,
                    'message'   =>  'Usuario sin permisos para dar de alta la promoción',
                    'error'     =>  'Usuario sin permisos para dar de alta la promoción',
                ), 421);
            }
        } */

        $consecutivo = DB::table('prmhdr')
             ->select(DB::raw('isnull(MAX( substring(NumProm,2,7)),0)+1 as numProm'))
             ->where('modulo', 'P')
             ->first();
             //->get();

        $consec_aplicar = strval($consecutivo->numProm);
        $size_actual = strlen($consec_aplicar);
        for ($i= $size_actual; $i < 7; $i++) { 
            $consec_aplicar = '0'.$consec_aplicar;
        }
        $consec_aplicar = 'P'.$consec_aplicar;
        //return response()->json($consec_aplicar);
        DB::beginTransaction();

        $promocion_pyc = PromocionPYC::where('id',$id)->first();
        $sucursales = PromocionSucPYC::where('prm_id', $id)->get()->toArray();
        $dat = '';
        $articulos = PromocionDetPYC::where('id_pyc_prom',$id)->get();

        //Insertando registros en prmhdr por cada sucursal
        foreach ($sucursales as $key => $value) {
            $dat.= $value['suc'].$consec_aplicar.' , ';
            $promo = new PromocionMKS;
            $promo->ibuff = '     ';
            $promo->cia = 'MAB';
            $promo->alm = $value['suc'];
            $promo->suc = $value['suc'];
            $promo->NumProm = $consec_aplicar;
            $promo->DesProm = $promocion_pyc->desProm;
            $promo->fec_ini = $promocion_pyc->fec_ini;
            $promo->fec_fin = $promocion_pyc->fec_fin;
            $promo->hra_ini = $promocion_pyc->hra_ini;
            $promo->hra_fin = $promocion_pyc->hra_fin;
            $promo->hra_ini = $promocion_pyc->hra_ini;
            $promo->Modulo = 'P';
            $promo->status = '1';
            $promo->inc_similares = $promocion_pyc->inc_similares;
            $promo->AplicaSobrePrm = ' ';
            $promo->AplicaSobreNeg = '1';
            $promo->SelPor = '0';
            if($promocion_pyc->tpoProm == 6){
                $promo->TpoProm = 5;
            }
            else{
                $promo->TpoProm = $promocion_pyc->tpoProm;
            }
            //$promo->cte = $promocion_pyc->
            $promo->cte = $promocion_pyc->cte;
            $promo->CodBarCF = '                ';
           
            if($promocion_pyc->retail == 1 && $promocion_pyc->mostrador == 1 || $promocion_pyc->retail == 0 && $promocion_pyc->mostrador == 1){
                $promo->dep_sur = '      ';
            }else{ 
            if($value['suc']=='001' || $value['suc']=='002' || $value['suc']=='013' || $value['suc']=='037' ||$value['suc']=='051' || $value['suc']=='053' || $value['suc']=='057'){
                $promo->dep_sur = '      ';
            }else{
                if($promocion_pyc->retail == 1 ){
                    $promo->dep_sur =$value['suc'].'AUT';
                }else{
                    $promo->dep_sur = '      ';
                }
            }
            }
            $promo->con_pag = $promocion_pyc->con_pag;
            $promo->seg_0 = $promocion_pyc->seg_0;
            $promo->seg_1 = $promocion_pyc->seg_1;
            $promo->seg_2 = $promocion_pyc->seg_2;
            $promo->seg_3 = $promocion_pyc->seg_3;
            $promo->seg_4 = '   ';
            $promo->giro_0 = '   ';
            $promo->giro_1 = '   ';
            $promo->giro_2 = '   ';
            $promo->giro_3 = '   ';
            $promo->giro_4 = '   ';
            $promo->usa_limite = 'N';
            $promo->uds_limite = $promocion_pyc->uds_limite;
            $promo->uds_vendidas = $promocion_pyc->uds_vendidas;
            $promo->uds_por_cte = $promocion_pyc->uds_por_cte;
            $promo->cantidad_minima = $promocion_pyc->cantidad_minima;
            $promo->compra_minima = $promocion_pyc->compra_minima;
            $promo->f_alt = date("Ymd");
            $promo->h_alt = date("His");
            $promo->u_alt = $promocion_pyc->u_alt;
            $promo->f_mod = date("Ymd");
            $promo->h_mod = date("His");
            $promo->u_mod = $promocion_pyc->u_alt;

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
            //Insertando registros en prmhdet por cada articulo

            $npar = 0;
            foreach ($articulos as $key => $value2) {
                //Primero verificar que si la promocion es de precio + regalo
                //insertamos primero la de regalo con precios del cat art

                $det_prom = new PromocionDetMKS;

                //if($promocion_pyc->tpoProm == 6){
                    $a = DB::table('invart')
                        ->where('art', $value2['cve_art'])
                        //->where('alm',$promocion_pyc->suc_prec_base)
                        ->where('alm', $value['suc']) ////linea nueva para buscar articulo por cada sucursal
                        ->first();
                                     
            
                   /*  $det_prom->precio_0 = $a->precio_vta0;
                    $det_prom->precio_1 = $a->precio_vta1;
                    $det_prom->precio_2 = $a->precio_vta2;
                    $det_prom->precio_3 = $a->precio_vta3;
                    $det_prom->precio_4 = $a->precio_vta4;
                }  else{
                    $det_prom->precio_0 = $value2['precio_0'];
                    $det_prom->precio_1 = $value2['precio_1'];
                    $det_prom->precio_2 = $value2['precio_2'];
                    $det_prom->precio_3 = $value2['precio_3'];
                    $det_prom->precio_4 = $value2['precio_4'];
                }  */
                //return $array;
                if(is_null($a)){
                    
                    if(count($articulos)==1){
                    $promocionmks = PromocionMKS::where('NumProm',$consec_aplicar)
                    ->where('alm', $value['suc'])
                    ->delete();
                    //$promocionmks->save();
                    }
                    continue;
                }else{
                if($value2['precio_0']==0){
                    $det_prom->precio_0 = $a->precio_vta0;
                }else{
                    $det_prom->precio_0 = $value2['precio_0'];
                }
                if($value2['precio_1']==0){
                    $det_prom->precio_1 = $a->precio_vta1;
                }else{
                    $det_prom->precio_1 = $value2['precio_1'];
                }
                if($value2['precio_2']==0){
                    $det_prom->precio_2 = $a->precio_vta2;
                }else{
                    $det_prom->precio_2 = $value2['precio_2'];
                }
                if($value2['precio_3']==0){
                    $det_prom->precio_3 = $a->precio_vta3;
                }else{
                    $det_prom->precio_3 = $value2['precio_3'];
                }
                if($value2['precio_4']==0){
                    $det_prom->precio_4 = $a->precio_vta4;
                }else{
                    $det_prom->precio_4 = $value2['precio_4'];
                }
                }

                $det_prom->ibuff = '     ';
                $det_prom->cia = 'MAB';
                $det_prom->alm = $value['suc'];
                $det_prom->suc = $value['suc'];
                $det_prom->NumProm = $consec_aplicar;
                $det_prom->NPar = str_pad(strval($npar), 5, " ", STR_PAD_LEFT);
                $det_prom->RenExcep = ' ';
                $det_prom->status = 1;
                $det_prom->cve_art = $value2['cve_art'];
                $det_prom->des_art = $value2['des_art'];
                $det_prom->lin = '    ';
                $det_prom->s_lin = '    ';
                $det_prom->fam = '    ';
                $det_prom->s_fam = '    ';
                $det_prom->marca = '        ';
                $det_prom->temp = '    ';
                $det_prom->prv = '         ';
                $det_prom->Id_modelo = '                    ';
                $det_prom->cte = '         ';
                $det_prom->seg = '   ';
                $det_prom->giro = '   ';
                $det_prom->sin_cargo = $value2['sin_cargo'];
                $det_prom->cobradas = $value2['cobradas'];
                $det_prom->regaladas = $value2['regaladas'];
                $det_prom->art_reg = str_pad(strval($value2['art_reg']), 10);
                $det_prom->emp_reg = str_pad(strval($value2['emp_reg']), 3, " ", STR_PAD_LEFT);
                $det_prom->fac_min_reg = $value2['fac_min_reg'];
                $det_prom->precio_reg = $value2['precio_reg'];
                

                $det_prom->p_dsc_0 = $value2['p_dsc_0'];
                $det_prom->p_dsc_1 = $value2['p_dsc_1'];
                $det_prom->p_dsc_2 = $value2['p_dsc_2'];
                $det_prom->MontoDsc = $value2['Monto_Dsc'];
                $det_prom->PuntosSuma = $value2['Monto_Dsc'];
                $det_prom->PuntosResta = $value2['Monto_Dsc'];
                $det_prom->PorcMonedero = $value2['Monto_Dsc'];
                $det_prom->MontoBoletos = $value2['Monto_Dsc'];
                $det_prom->Boletos = 0;

                try{
                    $det_prom->save();
                    
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
            }


        }

        //Si la promocion es de precio y regalo, se insertan 2 registros
        //Primero la de regalo y luego la de precio, de esta manera pasan las 2
        $consec_aplicar2 = "";
        if($promocion_pyc->tpoProm == 6){

            //Obtner el segundo consecutivo
            $consecutivo2 = DB::table('prmhdr')
             ->select(DB::raw('isnull(MAX( substring(NumProm,2,7)),0)+1 as numProm'))
             ->where('modulo', 'P')
             ->first();


            $consec_aplicar2 = str_pad($consecutivo2->numProm."", 7, "0", STR_PAD_LEFT);
            $consec_aplicar2 = 'P'.$consec_aplicar2;
            foreach ($sucursales as $key => $value) {
                $a = DB::table('invart')
                        ->where('art', $value2['cve_art'])
                        //->where('alm',$promocion_pyc->suc_prec_base)
                        ->where('alm', $value['suc']) ////linea nueva para buscar articulo por cada sucursal
                        ->first();
                $promo = new PromocionMKS;
                $promo->ibuff = '     ';
                $promo->cia = 'MAB';
                $promo->alm = $value['suc'];
                $promo->suc = $value['suc'];
                $promo->NumProm = $consec_aplicar2;
                $promo->DesProm = $promocion_pyc->desProm;
                $promo->fec_ini = $promocion_pyc->fec_ini;
                $promo->fec_fin = $promocion_pyc->fec_fin;
                $promo->hra_ini = $promocion_pyc->hra_ini;
                $promo->hra_fin = $promocion_pyc->hra_fin;
                $promo->hra_ini = $promocion_pyc->hra_ini;
                $promo->Modulo = 'P';
                $promo->status = '1';
                $promo->inc_similares = $promocion_pyc->inc_similares;
                $promo->AplicaSobrePrm = ' ';
                $promo->AplicaSobreNeg = '1';
                $promo->SelPor = '0';
                $promo->TpoProm = 1;
                //$promo->cte = $promocion_pyc->
                $promo->cte = $promocion_pyc->cte;
                $promo->CodBarCF = '                ';
                $promo->dep_sur = '      ';
                $promo->con_pag = $promocion_pyc->con_pag;
                $promo->seg_0 = $promocion_pyc->seg_0;
                $promo->seg_1 = $promocion_pyc->seg_1;
                $promo->seg_2 = $promocion_pyc->seg_2;
                $promo->seg_3 = $promocion_pyc->seg_3;
                $promo->seg_4 = '   ';
                $promo->giro_0 = '   ';
                $promo->giro_1 = '   ';
                $promo->giro_2 = '   ';
                $promo->giro_3 = '   ';
                $promo->giro_4 = '   ';
                $promo->usa_limite = 'N';
                $promo->uds_limite = $promocion_pyc->uds_limite;
                $promo->uds_vendidas = $promocion_pyc->uds_vendidas;
                $promo->uds_por_cte = $promocion_pyc->uds_por_cte;
                $promo->cantidad_minima = $promocion_pyc->cantidad_minima;
                $promo->compra_minima = $promocion_pyc->compra_minima;
                $promo->f_alt = date("Ymd");
                $promo->h_alt = date("His");
                $promo->u_alt = $promocion_pyc->u_alt;
                $promo->f_mod = date("Ymd");
                $promo->h_mod = date("His");
                $promo->u_mod = $promocion_pyc->u_alt;

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
                //Insertando registros en prmhdet por cada articulo

                $npar = 0;
                foreach ($articulos as $key => $value2) {
                    /* $a = DB::table('invart')
                        ->where('art', $value2['cve_art'])
                        //->where('alm',$promocion_pyc->suc_prec_base)
                        ->where('alm', $value['suc']) ////linea nueva para buscar articulo por cada sucursal
                        ->first();

                    $det_prom = new PromocionDetMKS;
                    if(isnull($a)){
                        continue;
                    }else{
                        $det_prom->precio_0 = $a->precio_vta0;
                        $det_prom->precio_1 = $a->precio_vta1;
                        $det_prom->precio_2 = $a->precio_vta2;
                        $det_prom->precio_3 = $a->precio_vta3;
                        $det_prom->precio_4 = $a->precio_vta4;
                    } */
                    $det_prom->precio_0 = 0.0;
                    $det_prom->precio_1 = 0.0;
                    $det_prom->precio_2 = 0.0;
                    $det_prom->precio_3 = 0.0;
                    $det_prom->precio_4 = 0.0; 

                    $det_prom->ibuff = '     ';
                    $det_prom->cia = 'MAB';
                    $det_prom->alm = $value['suc'];
                    $det_prom->suc = $value['suc'];
                    $det_prom->NumProm = $consec_aplicar2;
                    $det_prom->NPar = str_pad(strval($npar), 5, " ", STR_PAD_LEFT);
                    $det_prom->RenExcep = ' ';
                    $det_prom->status = 1;
                    $det_prom->cve_art = $value2['cve_art'];
                    $det_prom->des_art = $value2['des_art'];
                    $det_prom->lin = '    ';
                    $det_prom->s_lin = '    ';
                    $det_prom->fam = '    ';
                    $det_prom->s_fam = '    ';
                    $det_prom->marca = '        ';
                    $det_prom->temp = '    ';
                    $det_prom->prv = '         ';
                    $det_prom->Id_modelo = '                    ';
                    $det_prom->cte = '         ';
                    $det_prom->seg = '   ';
                    $det_prom->giro = '   ';

                    $det_prom->sin_cargo = 'N';
                    $det_prom->cobradas = 0.0;
                    $det_prom->regaladas = 0.0;
                    //$prmdet->art_reg = $value->cve;
                    //$prmdet->emp_reg = $value->cve;
                    $det_prom->fac_min_reg = 0.0;
                    $det_prom->precio_reg = 0.0;
                    

                    $det_prom->art_reg = "               ";
                    $det_prom->emp_reg = "   ";
                    

                    $det_prom->p_dsc_0 = $value2['p_dsc_0'];
                    $det_prom->p_dsc_1 = $value2['p_dsc_1'];
                    $det_prom->p_dsc_2 = $value2['p_dsc_2'];
                    $det_prom->MontoDsc = $value2['Monto_Dsc'];
                    $det_prom->PuntosSuma = $value2['Monto_Dsc'];
                    $det_prom->PuntosResta = $value2['Monto_Dsc'];
                    $det_prom->PorcMonedero = $value2['Monto_Dsc'];
                    $det_prom->MontoBoletos = $value2['Monto_Dsc'];
                    $det_prom->Boletos = 0;

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
                }


            }
        }

        //Hasta aqui todo bien, falta validar que el cliente exista
        $promocion_pyc->numProm = $consec_aplicar;
        $promocion_pyc->autoriza = $comprador;
        $promocion_pyc->status = 1;
        if($promocion_pyc->tpoProm == 6){
            $promocion_pyc->numProm = $consec_aplicar2;
            $promocion_pyc->numPromReg = $consec_aplicar;
        }
        try{
            $promocion_pyc->save();
        }catch(Throwable $e){
            DB::rollBack();
            return response()->json(array(
                'code'      =>  421,
                'message'   =>  'Ocurrió un error al guardar, intentelo nuevamente',
                'error'     =>  'Ocurrió un error al guardar, intentelo nuevamente',
            ), 421);
        }


        DB::commit();
        return response()->json($promocion_pyc);
        //return response()->json($consecutivo->numProm);
    }

    public function denegarProm(Request $request){
        $idprom = $request->input("idprom","-1");
        $comprador = $request->input("compr","-1");
        $usuario = UserPyc::where('cve_corta', $comprador)->first();

        $datos1 = explode(" ", $idprom);
        //return print_r($datos);
        $id=$datos1[0];

        $tipo=$datos1[1];

        //return response()->json($comprador);

       /*  if(is_null($usuario)){
            try{
                //$promo->save();
                ;
            }catch(Throwable $e){
                DB::rollBack();
                return response()->json(array(
                    'code'      =>  421,
                    'message'   =>  'Usuario no encontrado',
                    'error'     =>  'Usuario no encontrado',
                ), 421);
            }
        }

        $permiso = DB::table('pyc_roles_permisos')
                            ->where('rol_id',$usuario->id)
                            ->first();
        if(is_null($permiso)){
            try{
                //$promo->save();
                ;
            }catch(Throwable $e){
                DB::rollBack();
                return response()->json(array(
                    'code'      =>  421,
                    'message'   =>  'Usuario sin permisos para denegar la promoción',
                    'error'     =>  'Usuario sin permisos para denegar la promoción',
                ), 421);
            }
        } */

        $promocion_pyc = PromocionPYC::where('id',$id)->first();
        $promocion_pyc->status = 2;
        try{
            $promocion_pyc->save();
        }catch(Throwable $e){
            DB::rollBack();
            return response()->json(array(
                'code'      =>  421,
                'message'   =>  'Usuario sin permisos para denegar la promoción',
                'error'     =>  'Usuario sin permisos para denegar la promoción',
            ), 421);
        }
        return response()->json($promocion_pyc);

    }

    public function getAutorizadas(Request $request){
        $comprador = $request->input('compr', "-1");
        $usuario = UserMKS::where('nom_cto', $comprador)->first();
        if(strtoupper($comprador) != 'PYC' && is_null($usuario)){
            return response()->json(array(
                    'code'      =>  421,
                    'message'   =>  'Usuario no encontrado',
                    'error'     =>  'Usuario no encontrado',
                ), 421);
        }
        $puesto = 'COMPRAS';
        if(strtoupper($comprador) != 'PYC'){
            $puesto = $usuario->puesto;   
        }else{
            $puesto = 'COMPRASJE';   
        }
        if(str_contains($puesto, 'COMPRASJE') || str_contains($puesto, 'TRADEMKT') || strtoupper($comprador) == 'PYC'){

            $combos = CombosPYC:://where('u_alt',$comprador)
                        leftJoin('cprprv', 'pyc_invhcm.proveedor','=','cprprv.proveedor')
                        ->select('pyc_invhcm.*','cprprv.nom as nom_prov');
                        
                        //->orderByDesc('updated_at')
                        //->get()
                        //->toArray(); 


        //return response()->json($combos);
            $promociones = PromocionPYC::where('pyc_prmhdr.status', 1)
                        ->leftJoin('cprprv', 'pyc_prmhdr.proveedor','=','cprprv.proveedor')
                        ->select('pyc_prmhdr.*','cprprv.nom as nom_prov')
                        ->union($combos)
                        ->orderByDesc('updated_at')
                        ->get()
                        ->toArray();
        }
        else {
            $promociones = PromocionPYC::where('pyc_prmhdr.status', 1)->where('u_alt',$comprador)
                        ->leftJoin('cprprv', 'pyc_prmhdr.proveedor','=','cprprv.proveedor')
                        ->select('pyc_prmhdr.*','cprprv.nom as nom_prov')
                        ->orderByDesc('updated_at')
                        ->get()
                        ->toArray();
        }
        
        return response()->json($promociones);
    }

    public function getAcuerdoXComprador(Request $request){
        //$comprador = $request->input('compr',-1);
        $proveedor = '000000036';
        $folio ='4132';
        //$usuario = $request->input('usr','-1');

        $acuerdos = DB::table('Rca_Acuerdos')
                        ->select(DB::raw('Folio, Comprador, Nombre, Linea1, boletin, Fecha'))
                        //->union($apoyos)
                        //->where('Comprador',$comprador)
                        ->where('nom','like','%'.$nombre.'%')
                        ->where('Clave', $proveedor)
                        ->get();

                        /* $apoyos = DB::table('Rca_Acuerdos')
                        ->select(DB::raw('Folio, Comprador, Nombre, Linea1, boletin, Fecha'))
                        //->union($apoyos)
                        ->where('Comprador','3')
                        ->where('Clave', '000000057')
                        ->get(); */

              /* $apoyos = DB::table('Rca_ApoyosDir')
                        ->select(DB::raw("Folio + '*' as Folio,Comprador, Nombre, Linea1, 'APOYOS DIRECCION' as boletin, fecApoyo as Fecha"))
                        ->where('Comprador',$comprador);  */
                        $apoyos = DB::table('Rca_ApoyosDir')
                        ->select(DB::raw("Folio + '*' as Folio,Comprador, Nombre, Linea1, 'APOYOS DIRECCION' as boletin, fecApoyo as Fecha"))
                        //->where('Comprador',$comprador)
                        ->get();; 

        return response()->json($acuerdos);
    }

    public function formato(Request $request){
        //$idprom = $request->input("idprom","2");
        $idprom = $request->input("idprom","-1");
        //$nom_cto = $request->input("nom_cto","-1");

        $datos1 = explode(" ", $idprom);
        //return print_r($datos);
        $id=$datos1[0];
        $tipo=$datos1[1];
       // return $tipo;
        if($tipo==1 || $tipo==5 || $tipo==6){
        $promo = PromocionPYC::where('id',$id)
                    ->leftJoin('cprprv', 'pyc_prmhdr.proveedor','=','cprprv.proveedor')
                    ->select('pyc_prmhdr.*','cprprv.nom as nom_prov')
                    ->get()->first()
                    ->toArray();
        $detprom = PromocionDetPYC::where('id_pyc_prom',$id)
                    ->get()->toArray();

                    //return $promo;
        $suc = PromocionSucPYC::where('prm_id',$id)->select('suc')->get()->pluck('suc')->toArray();
        //return response()->json($suc);

        $firmaUser="";

        if (strpos($promo['folio_ac'], '*') !== false) {
            $cadena = $promo['folio_ac'];
            $separador = " ";
            $fol = explode($separador, $cadena);
            $apoyos = DB::table('Rca_ApoyosDir')
            ->select(DB::raw("Folio + '*' as Folio,Comprador, Nombre, Linea1, 'APOYOS DIRECCION' as boletin, fecApoyo as Fecha"))
            ->where('Folio',$fol[0])
            ->get();

            foreach ($apoyos as &$apoyos) {
                $firmaUser = $apoyos ;
            }
            //return  $firmaUser->Nombre;
        }else{
            $cadena = $promo['folio_ac'];
            $separador = " ";
            $fol = explode($separador, $cadena);
            $acuerdos = DB::table('Rca_Acuerdos')
                     ->select(DB::raw('Folio, Comprador, Nombre, Linea1, boletin, Fecha'))
                    ->where('Folio', $fol[0])
                    ->get();

            foreach ($acuerdos as &$acuerdos) {
                 $firmaUser = $acuerdos ;
            }
            //return  $firmaUser->Nombre;
        }


        //Agregando factor de empaques
        foreach ($detprom as $key => $value) {
            //Buscando el articulo en tabla invart
            $factores = DB::table('invart')
                    ->where('art', $value['cve_art'])
                    ->where('alm', $promo['suc_prec_base'])
                    ->select('cant_pre0', 'cant_pre1', 'cant_pre2', 
                        'cant_pre3', 'cant_pre4', 'precio_vta0', 'precio_vta1',
                        'precio_vta2', 'precio_vta3', 'precio_vta4'
                    )
                    ->get()
                    ->first();
            $detprom[$key]['cant_pre0'] = $factores->cant_pre0;
            $detprom[$key]['cant_pre1'] = $factores->cant_pre1;
            $detprom[$key]['cant_pre2'] = $factores->cant_pre2;
            $detprom[$key]['cant_pre3'] = $factores->cant_pre3;
            $detprom[$key]['cant_pre4'] = $factores->cant_pre4;

            $detprom[$key]['precio_vta0'] = $factores->precio_vta0;
            $detprom[$key]['precio_vta1'] = $factores->precio_vta1;
            $detprom[$key]['precio_vta2'] = $factores->precio_vta2;
            $detprom[$key]['precio_vta3'] = $factores->precio_vta3;
            $detprom[$key]['precio_vta4'] = $factores->precio_vta4;
        }
        //return response()->json($promo);

        $user = "ADMIN";
        if(strtoupper($promo['u_alt']) != 'PYC' ){
            $capturo = UserMKS::where('nom_cto', $promo['u_alt'])->first();
            $user = $capturo['nombre_lar'];
        }
        $datos = array('prom' => $promo, 'arts' => $detprom, 'suc' => $suc );

        $data = [ 'titulo' => 'Formato de Ofertas y promociones'];
        $numHojas = count($detprom);

        if($promo['tpoProm'] == 1){
            //Sobre 9 porque son los art que caben en una hoja para prom de precio
            $residuo = $numHojas % 8;
            if($residuo == 0){
                $numHojas = intval($numHojas / 8);
            }else{
                $numHojas = intval(($numHojas / 8) + 1);
            }
            $pdf = \PDF::loadView('formatoPromocion', [
            'prom'=>$promo, 'arts'=>$detprom, 'sucs' => $suc, 'user' => $user, 
            'numHojas' => $numHojas, 'total' => (count($detprom) ), 'firmaUser'=>$firmaUser]);

            return $pdf->stream('formato.pdf');

            return view('formatoPromocion', [
                'prom'=>$promo, 'arts'=>$detprom, 'sucs' => $suc, 'user' => $user,
                'numHojas' => $numHojas, 'total' => (count($detprom)), 'firmaUser'=>$firmaUser
            ]);
        }
        if($promo['tpoProm'] == 5){
            //Sobre 32 porque son los art que caben en una hoja para prom de regalo
            $residuo = $numHojas % 22;
            if($residuo == 0){
                $numHojas = intval($numHojas / 23);
            }else{
                $numHojas = intval(($numHojas / 23) + 1);
            }
            $pdf = \PDF::loadView('formatoPromocionRegalo', [
                'prom'=>$promo, 'arts'=>$detprom, 'sucs' => $suc, 'user' => $user, 
                'numHojas' => $numHojas, 'total' => (count($detprom) ), 'firmaUser'=>$firmaUser]
            );

            return $pdf->stream('formato.pdf');

            return view('formatoPromocionRegalo', [
                'prom'=>$promo, 'arts'=>$detprom, 'sucs' => $suc, 'user' => $user,
                'numHojas' => $numHojas, 'total' => (count($detprom) ), 'firmaUser'=>$firmaUser
            ]);
        }
            
        if($promo['tpoProm'] == 6){
            $residuo = $numHojas % 29;
            if($residuo == 0){
                $numHojas = intval($numHojas / 29);
            }else{
                $numHojas = intval(($numHojas / 29) + 1);
            }

            $residuo2 = (count($detprom) ) % 8;
            $numHojas2 = count($detprom);
            if($residuo2 == 0){
                $numHojas2 = intval($numHojas2 / 8);
            }else{
                $numHojas2 = intval(($numHojas2 / 8) + 1);
            }


            $pdf = \PDF::loadView('formatoPromocionCombinada', [
                'prom'=>$promo, 'arts'=>$detprom, 'sucs' => $suc, 'user' => $user, 
                'numHojas' => $numHojas,'numHojas2' => $numHojas2, 'total' => (count($detprom) ), 'firmaUser'=>$firmaUser]
            );

            return $pdf->stream('formato.pdf');

            return view('formatoPromocionCombinada', [
                'prom'=>$promo, 'arts'=>$detprom, 'sucs' => $suc, 'user' => $user,
                'numHojas' => $numHojas, 'total' => (count($detprom) ), 'firmaUser'=>$firmaUser
            ]);
        }
        }

        if($tipo==2 || $tipo==3 ){

        if($tipo==2  ){
        $promo = CombosPYC::where('id',$id)
            ->get()->first()
            ->toArray();
        }
        if($tipo==3){
            $promo = CombosPYC::where('id',$id)
                ->leftJoin('cprprv', 'pyc_invhcm.proveedor','=','cprprv.proveedor')
                ->select('pyc_invhcm.*','cprprv.nom as nom_prov')
                ->get()->first()
                ->toArray();
            }
        
        $detprom = CombosDetPYC::where('id_pyc_cmb',$id)
                    ->get()->toArray();

                    //return $promo;
        $suc = CombosSucPYC::where('cmb_id',$id)->select('suc')->get()->pluck('suc')->toArray();
        //return response()->json($suc);

        $firmaUser="";
        if($tipo==3){
        if (strpos($promo['folio_ac'], '*') !== false) {
            $cadena = $promo['folio_ac'];
            $separador = " ";
            $fol = explode($separador, $cadena);
            $apoyos = DB::table('Rca_ApoyosDir')
            ->select(DB::raw("Folio + '*' as Folio,Comprador, Nombre, Linea1, 'APOYOS DIRECCION' as boletin, fecApoyo as Fecha"))
            ->where('Folio',$fol[0])
            ->get();

            foreach ($apoyos as &$apoyos) {
                $firmaUser = $apoyos ;
            }
            //return  $firmaUser->Nombre;
        }else{
            $cadena = $promo['folio_ac'];
            $separador = " ";
            $fol = explode($separador, $cadena);
            $acuerdos = DB::table('Rca_Acuerdos')
                     ->select(DB::raw('Folio, Comprador, Nombre, Linea1, boletin, Fecha'))
                    ->where('Folio', $fol[0])
                    ->get();

            foreach ($acuerdos as &$acuerdos) {
                 $firmaUser = $acuerdos ;
            }
            //return  $firmaUser->Nombre;
        
        }
        }else{
        
            //return $usu;
            $usuario = UserMKS::where('nom_cto',$promo['u_alt'])
                    ->select(DB::raw("nombre_lar"))
                     ->get()->toArray();

            $firmaUser = $usuario;
            //return $firmaUser;  
        }
        $user = "ADMIN";
        if(strtoupper($promo['u_alt']) != 'PYC' ){
            $capturo = UserMKS::where('nom_cto', $promo['u_alt'])->first();
            $user = $capturo['nombre_lar'];
        }
        $datos = array('prom' => $promo, 'arts' => $detprom, 'suc' => $suc );

        $data = [ 'titulo' => 'Formato de Ofertas y promociones'];
        $numHojas = count($detprom);

    }
        if($tipo == 3){
            //Sobre 9 porque son los art que caben en una hoja para prom de precio
            $residuo = $numHojas % 15;
            if($residuo == 0){
                $numHojas = intval($numHojas / 15);
            }else{
                $numHojas = intval(($numHojas / 15) + 1);
            }
            $pdf = \PDF::loadView('formatoPrecioCombo', [
            'prom'=>$promo, 'arts'=>$detprom, 'sucs' => $suc, 'user' => $user, 
            'numHojas' => $numHojas, 'total' => (count($detprom) ), 'firmaUser'=>$firmaUser]);

            return $pdf->stream('formato.pdf');

            return view('formatoPrecioCombo', [
                'prom'=>$promo, 'arts'=>$detprom, 'sucs' => $suc, 'user' => $user,
                'numHojas' => $numHojas, 'total' => (count($detprom)), 'firmaUser'=>$firmaUser
            ]);
        } 
        if($tipo == 2){
            //Sobre 9 porque son los art que caben en una hoja para prom de precio
            $residuo = $numHojas % 47;
            if($residuo == 0){
                $numHojas = intval($numHojas / 47);
            }else{
                $numHojas = intval(($numHojas / 47) + 1);
            }
            $pdf = \PDF::loadView('formatoCombo', [
            'prom'=>$promo, 'arts'=>$detprom, 'sucs' => $suc, 'user' => $user, 
            'numHojas' => $numHojas, 'total' => (count($detprom) ), 'firmaUser'=>$firmaUser]);

            return $pdf->stream('formato.pdf');

            return view('formatoCombo', [
                'prom'=>$promo, 'arts'=>$detprom, 'sucs' => $suc, 'user' => $user,
                'numHojas' => $numHojas, 'total' => (count($detprom)), 'firmaUser'=>$firmaUser
            ]);
        } 
        
        
            
        //return response()->json($datos);
    }
}
