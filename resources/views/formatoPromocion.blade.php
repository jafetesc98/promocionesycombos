<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="panel" style="width:814px; height:1056px; border:0px;">
        <div class="panel" style="width:814px; height:1025px; border:0px;">
            <div class="panel-body">
                @for ($i = 0; $i < $numHojas; $i++)
                <table style="font-size:10px; border: 0px;" >
                    
                    <tr style="border: 0px;">
                        <td colspan="8"  align="center" id="1" >
                        <b><h2>FORMATO DE OFERTAS Y PROMOCIONES</h2></b>
                        <!--<p>'.$preciosImprimir[0]["tipo"].': ID('.$preciosImprimir[0]["idtemp"].')</p>-->
                        
                        <p><b>PROMOCIÓN DE PRECIO : ID({{$prom['id']}})</b></p>
                        
                        </td>
                        <td colspan="2" align="center" id="2">
                        <img src="logo3.png" alt="" height="40">
                        </td>
                    </tr>

                    <tr>
                        <td style="border: 0; font-size:3px" colspan="11" class="saltos">
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <!--<td style="width:15px;" align="center" class="margen"> </td>-->
                        <td scope="col" rowspan = "1" colspan="2" align="center" style="background-color: #ccc">NOMBRE DE LA PROMOCION</td>
                        <td scope="col"  align="center" colspan="8" style="max-width:650px; border-bottom: 0;">{{$prom['desProm']}}</td>
                        <!--<td style="width:15px;" align="center" class="margen"></td>-->
                    </tr>

                    <tr>
                        <!--<td style="width:15px;" align="center" class="margen"> </td>-->
                        <td scope="col" align="center" style="background-color: #ccc">PROVEEDOR</td>
                        <td scope="col" align="center" colspan="4">{{$prom['proveedor']}} - {{$prom['nom_prov']}}</td>
                        <td scope="col" align="center" colspan="2" style="background-color: #ccc">DESCRIPCION DE OFERTA</td>
                        <td scope="col" align="center" colspan="3">
                            @if($prom['paga'] == 1)
                            PAGA PROVEEDOR
                            @elseif($prom['paga'] == 2)
                            PAGA MINIABASTOS
                            @elseif($prom['paga'] == 4)
                            PAGA MKT
                            @else
                            PAGAN AMBOS
                            @endif
                        </td>
                        <td style="width:15px;" align="center" class="margen"></td>
                    </tr>

                    

                    <tr>
                        <!--<td style="width:15px;" align="center" class="margen"></td>-->
                        <td scope="col" align="center" style="background-color: #ccc">FOLIO DE ACUERDO</td>

                        <td scope="col" align="center" colspan="1">{{$prom['folio_ac']}}</td>
                        
                        <td scope="col" align="center" style="background-color: #ccc">BOLETIN</td>
                        <td scope="col" align="center" colspan="2">{{$prom['boletin']}}</td>
                        <td scope="col" align="center" style="background-color: #ccc">PERIODO</td>
                        <td scope="col" align="center" colspan="4">Del {{date("d/m/Y", strtotime($prom['fec_ini']))}} al {{date("d/m/Y", strtotime($prom["fec_fin"]))}}</td>
                        <td style="width:15px;" align="center" class="margen"></td>
                    </tr>

                    

                    
                    <tr>
                        <!--<td style="width:15px;" align="center" class="margen"></td>-->
                        <td scope="col" align="center" colspan="10" style="background-color: #ccc">FORMA DE COBRO PARA LA APLICACION</td>
                        <td style="width:15px;" align="center" class="margen"></td>
                    </tr>
                    <tr>
                        <!--<td style="width:15px;" align="center" class="margen"> </td>-->
                        <td scope="col" rowspan="2" align="center" style="background-color: #ccc">PARA</td>
                        <td scope="col"  align="center" colspan="9" style="max-width:650px; border-bottom: 0;">{{implode(", ", $sucs)}}</td>
                        <!--<td style="width:15px;" align="center" class="margen"></td>-->
                    </tr>
                    <tr>
                        <!--<td style="width:15px;" align="center" class="margen"> </td>-->
                         <td scope="col" align="center" colspan="9" style="max-width:650px; border-top: 0;"></td>
                        <!--<td style="width:15px;" align="center" class="margen"></td>-->
                    </tr>
                    <tr>
                        <!--<td style="width:15px;" align="center" class="margen"> </td>-->
                        <td scope="col" align="center" colspan="2" style="background-color: #ccc">APLICA A</td>
                        
                        <td scope="col" align="center" colspan="3">{{$prom['mostrador'] ? "MOSTRADOR" : ""}} {{$prom['retail'] ? " RETAIL" : ""}}</td>

                        <td scope="col" align="center" colspan="2" style="background-color: #ccc">TIPO INDICADOR</td>
                        
                        <td scope="col" align="center" colspan="3">
                            @if($prom['indicador'] == 0)
                            BAJA DE PRECIO
                            @elseif($prom['indicador'] == 1)
                            PROMOCION
                            @else
                            CAPAS
                            @endif
                        </td>
                        
                        <td style="width:15px;" align="center" class="margen"></td>
                    </tr>
                    <tr>
                        <td style="border: 0; font-size:3px" colspan="12" class="saltos">
                            <br>
                        </td>
                    </tr>

                    <tr>
                        <!--<td style="width:15px;" align="center" class="margen"></td>-->
                        <th scope="col" align="center" style="background-color: #ccc">CODIGO</th>
                        <th scope="col" align="center" colspan="4" style="background-color: #ccc">DESCRIPCIÓN</th>
                        <th scope="col" align="center" style="background-color: #ccc" colspan="2">PRECIO CAT ART</th>
                        <th scope="col" align="center" style="background-color: #ccc">PRECIO DE OFTA</th>
                        <th scope="col" align="center" style="background-color: #ccc">DIFERENCIA</th>
                        <th scope="col" align="center" style="background-color: #ccc">% DESCUENTO</th>
                        <!--<th scope="col" align="center" style="background-color: #ccc">PRECIO OFTA</th>
                        <th scope="col" align="center" style="background-color: #ccc">DIFERENCIA</th>
                        <td style="width:15px;" align="center" class="margen"></td>-->
                    </tr>
                
                    {{--@foreach ($arts as $art)--}}
                    @for ($j = $i*8; $j < (($i+1) * 8); $j++)
                    @if($j == $total)
                    
                    @break
                    @endif
                    <!--Hasta aqui vamos aqui flta imprimir precios-->
                    <tr>
                        <!--<td style="width:15px;" align="center" class="margen"></td>-->
                        <td align="center"><b>{{$arts[$j]['cve_art']}}</b></td>
                        <td align="center" rowspan="5" colspan="4" style="font-size:9px"><b>{{$arts[$j]['des_art']}}</b></td>
                        <!--Cantidad de piezas-->
                        <td align="center">{{$arts[$j]['cant_pre0']}}</td>
                        <!--Precio de catalogo de articulo -->
                        <td align="center">${{number_format($arts[$j]['precio_vta0'], 2, '.', '')}}</td>
                        <!--Precio de promocion distinto de 0 -->
                        @if( $arts[$j]['precio_0']!=0)
                        <td align="center">${{number_format($arts[$j]['precio_0'], 2, '.', '')}}</td>
                        @else
                        <td align="center">------</td>
                        @endif
                        <!--Diferencia en precios de catalogo y de promocion  -->
                        @if((doubleval($arts[$j]['precio_vta0']) - doubleval($arts[$j]['precio_0'])) ==doubleval($arts[$j]['precio_vta0']))
                        <td align="center">------</td>
                        @else
                        <td align="center">${{number_format(($arts[$j]['precio_vta0'] - $arts[$j]['precio_0']), 2, '.','')}} </td>
                        @endif
                        <!--Porcentaje de esa diferencia entre catalogo y promocion-->
                        @if(doubleval(100 - (($arts[$j]['precio_0'] * 100 ) / $arts[$j]['precio_vta0']) != 0) && doubleval(100 - (($arts[$j]['precio_0'] * 100 ) / $arts[$j]['precio_vta0']) !=100))
                        <td align="center">
                        {{number_format(100 - (($arts[$j]['precio_0'] * 100 ) / $arts[$j]['precio_vta0']), 2, '.', '')}} %</td>

                        @elseif(doubleval(100 - (($arts[$j]['precio_0'] * 100 ) / $arts[$j]['precio_vta0']) ==100))
                        <td align="center">------</td>
                        @endif
                        
                        <!--<td align="center">'.$preciosImprimir[$registrosimpresos]["segundoPrecio_ofer0"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["segundaDifer0"].'</td>
                        <td style="width:15px;" align="center" class="margen"></td>-->
                    </tr>
                    
                    <tr>
                        <!--<td style="width:15px;" align="center" class="margen"></td>-->
                        <td rowspan="4" colspan="1" align="center"></td>
                        
                        <td align="center">{{$arts[$j]['cant_pre1']}}</td>
                        <td align="center">${{number_format($arts[$j]['precio_vta1'], 2, '.', '')}}</td>
                        
                        @if($arts[$j]['precio_1'] != 0)
                        <td align="center">${{number_format($arts[$j]['precio_1'], 2, '.', '')}}</td>
                        @else
                        <td align="center">------</td>
                        @endif


                        @if((doubleval($arts[$j]['precio_vta1']) - doubleval($arts[$j]['precio_1'])) ==doubleval($arts[$j]['precio_vta1']))
                        <td align="center">------</td>
                        @else
                        <td align="center">${{number_format(($arts[$j]['precio_vta1'] - $arts[$j]['precio_1']), 2, '.','')}} </td>
                        @endif
                        
                        
                        @if(doubleval(100 - (($arts[$j]['precio_1'] * 100 ) / $arts[$j]['precio_vta1']) != 0) && doubleval(100 - (($arts[$j]['precio_1'] * 100 ) / $arts[$j]['precio_vta1']) !=100))
                        <td align="center">{{number_format(100 - (($arts[$j]['precio_1'] * 100 ) / $arts[$j]['precio_vta1']), 2, '.', '')}} %</td>

                        @elseif(doubleval(100 - (($arts[$j]['precio_1'] * 100 ) / $arts[$j]['precio_vta1']) == 100))
                        <td align="center"> ------ </td>
                        @endif
                        

                    </tr>
                    <tr>
                        <!--<td style="width:15px;" align="center" class="margen"></td>-->
                        <td align="center">{{$arts[$j]['cant_pre2']}}</td>
                        <td align="center">${{number_format($arts[$j]['precio_vta2'], 2, '.', '')}}</td>
                        
                        @if($arts[$j]['precio_2']!=0)
                        <td align="center">${{number_format($arts[$j]['precio_2'], 2, '.', '')}}</td>
                        @else
                        <td align="center">------</td>
                        @endif

                        @if((doubleval($arts[$j]['precio_vta2']) - doubleval($arts[$j]['precio_2'])) ==doubleval($arts[$j]['precio_vta2']))
                        <td align="center"> ------ </td>
                        @else
                        <td align="center">
                        ${{number_format(($arts[$j]['precio_vta2'] - $arts[$j]['precio_2']), 2, '.','')}}</td>
                        @endif

                        @if(doubleval(100 - (($arts[$j]['precio_2'] * 100 ) / $arts[$j]['precio_vta2']) != 0) && doubleval(100 - (($arts[$j]['precio_2'] * 100 ) / $arts[$j]['precio_vta2']) != 100))
                        <td align="center">
                        {{number_format(100 - (($arts[$j]['precio_2'] * 100 ) / $arts[$j]['precio_vta2']), 2, '.', '')}} %</td>
                        @elseif(doubleval(100 - (($arts[$j]['precio_2'] * 100 ) / $arts[$j]['precio_vta2']) == 100)) <td align="center"> ------ </td>
                        @endif
                        
                    </tr>
                    <tr>
                        <!--<td style="width:15px;" align="center" class="margen"></td>-->
                        <td align="center">{{$arts[$j]['cant_pre3']}}</td>
                        <td align="center">${{number_format($arts[$j]['precio_vta3'], 2, '.', '')}}</td>
                        @if( $arts[$j]['precio_3']!=0)
                        <td align="center">${{number_format($arts[$j]['precio_3'], 2, '.', '')}}</td>
                        @else
                         <td align="center">------</td>
                        @endif

                        @if((doubleval($arts[$j]['precio_vta3']) - doubleval($arts[$j]['precio_3'])) == doubleval($arts[$j]['precio_vta3']))
                        <td align="center"> ------ </td>
                        @else
                        <td align="center">
                        ${{number_format(($arts[$j]['precio_vta3'] - $arts[$j]['precio_3']), 2, '.','')}}</td>
                        @endif
                        @if(doubleval(100 - (($arts[$j]['precio_3'] * 100 ) / $arts[$j]['precio_vta3']) != 0) && doubleval(100 - (($arts[$j]['precio_3'] * 100 ) / $arts[$j]['precio_vta3']) != 100))
                        <td align="center">
                        {{number_format(100 - (($arts[$j]['precio_3'] * 100 ) / $arts[$j]['precio_vta3']), 2, '.', '')}} %</td>
                        @elseif(doubleval(100 - (($arts[$j]['precio_3'] * 100 ) / $arts[$j]['precio_vta3']) == 100))
                        <td align="center"> ------ </td>
                        @endif
                    </tr>
                    <tr>
                        <!--<td style="width:15px;" align="center" class="margen"></td>-->
                        <td align="center">{{$arts[$j]['cant_pre4']}}</td>
                        <td align="center">${{number_format($arts[$j]['precio_vta4'], 2, '.', '')}}</td>
                        @if( $arts[$j]['precio_4']!=0)
                        <td align="center">${{number_format($arts[$j]['precio_4'], 2, '.', '')}}</td>
                        @else
                         <td align="center">------</td>
                        @endif
                        

                        @if((doubleval($arts[$j]['precio_vta4']) - doubleval($arts[$j]['precio_4'])) == doubleval($arts[$j]['precio_vta4']))
                        <td align="center"> ------ </td>
                        @else
                        <td align="center">
                        ${{number_format(($arts[$j]['precio_vta4'] - $arts[$j]['precio_4']), 2, '.','')}}</td>
                        @endif
                        
                        @if(doubleval(100 - (($arts[$j]['precio_4'] * 100 ) / $arts[$j]['precio_vta4']) != 0) && doubleval(100 - (($arts[$j]['precio_4'] * 100 ) / $arts[$j]['precio_vta4']) != 100))
                        <td align="center">
                        {{number_format(100 - (($arts[$j]['precio_4'] * 100 ) / $arts[$j]['precio_vta4']), 2, '.', '')}} %</td>

                        @elseif(doubleval(100 - (($arts[$j]['precio_4'] * 100 ) / $arts[$j]['precio_vta4']) == 100))
                         <td align="center"> ------ </td>
                        @endif
                    </tr>
                    <tr style="border: 0;">
                        <td style="border: 0; font-size:3px" colspan="11" class="saltos">
                            .
                        </td>
                    </tr>
                    @endfor
                    <!--aqui termino un for-->
        
                    <tr style="border: 0;">
                        <td style="border: 0; font-size:3px" colspan="11" class="saltos">
                            .
                        </td>
                    </tr>

                     <tr style="border: 0;">
                        <td style="border: 0; font-size:3px" colspan="11" class="saltos">
                            .
                        </td>
                    </tr>

                    
                    <tr style="border: 0;">
                        <td colspan="3" class="saltos" align="center" style="height:5px; border: 0;">___________________________</td>
                        <td colspan="4" class="saltos" align="center" style="height:5px; border: 0;">___________________________</td>
                        <td colspan="3" class="saltos" align="center" style="height:5px; border: 0;">___________________________</td>
                    </tr>
                    <tr style="border: 0; ">
                        <td colspan="3" class="saltos" align="center" style="height:5px; border: 0;">AUTORIZÓ</td>
                        <td colspan="4" class="saltos" align="center" style="height:5px; border: 0;">COMPRAS</td>
                        <td colspan="3" class="saltos" align="center" style="height:5px; border: 0;">APLICA</td>
                    </tr>
                    <tr style="border: 0; ">
                        <td colspan="3" class="saltos" align="center" style="height:5px; border: 0;">ISRAEL DOMINGUEZ VELA</td>
                       <td colspan="4" class="saltos" align="center" style="height:5px; border: 0;">{{$firmaUser->Nombre}}</td>
                        <td colspan="3" class="saltos" align="center" style="height:5px; border: 0;"></td>
                    </tr>


                </table>
                <p style="margin: 0; padding:0; border:0px; font-family: monospace; font-size: 13px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Página {{$i+1}} de {{$numHojas}}</p>
                @if($i +1 != $numHojas )
                <div class="page-break"></div>
                @endif
                @endfor
                
            </div>
        </div>
    </div>
        
        <style>@page {
        
         margin: 0;
         padding:0;
        }
        table{
            table-layout: fixed;
            width:750px;
            margin: 35px 50px 30px 50px;
            border: 0.5px solid #000;
            font-family: monospace;
        }
        th, td {
            border: 0.5px solid #000;
            color: #000;
            
        }
        .margen
        {
            border-top: 0;
            border-bottom: 0;
            border-right: 0px;
            border-left: 0px;
        }
        .fecha
        {
            border-top: 0;
            border-bottom: 0;
            border-right: 0px;
            border-left: 0px;
        }
        #1{
            border-right: 0;
        }
        #2{
            border-left: 0;
        }
        .saltos{
            border-right: 0px;
            border-left: 0px;
        }

        h2,p{
            margin-top: 0px;
            margin-bottom: 2px;
        }
        .page-break {
            page-break-after: always;
        }
        </style>
</body>
</html>