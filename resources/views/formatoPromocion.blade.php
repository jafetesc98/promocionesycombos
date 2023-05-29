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
            <br>
                <table style="font-size:10px; border: 0px;" >
                    
                    <tr style="border: 0px;">
                        <td colspan="8"  align="center" id="1" >
                        <b><h2>FORMATO DE OFERTAS Y PROMOCIONES</h2></b>
                        <p>'.$preciosImprimir[0]["tipo"].': ID('.$preciosImprimir[0]["idtemp"].')</p>
                        </td>
                        <td colspan="3" align="center" id="2">
                        <img src="http://192.168.1.77:81/ofertasypromociones/assets/logo.png" alt="" height="60">
                        </td>
                    </tr>

                    <tr>
                        <td style="border: 0; font-size:3px" colspan="11" class="saltos">
                            <br>
                        </td>
                    </tr>

                    <tr>
                        <td style="width:15px;" align="center" class="margen"> </td>
                        <td scope="col" align="center" style="background-color: #ccc">PROVEEDOR</td>
                        <td scope="col" align="center" colspan="4">'.$preciosImprimir[0]["nom"].'</td>
                        <td scope="col" align="center" style="background-color: #ccc">DESCRIPCION DE OFERTA</td>
                        <td scope="col" align="center" colspan="3">'.$preciosImprimir[0]["linea1"].'</td>
                        <td style="width:15px;" align="center" class="margen"></td>
                    </tr>

                    

                    <tr>
                        <td style="width:15px;" align="center" class="margen"></td>
                        <td scope="col" align="center" style="background-color: #ccc">FOLIO DE ACUERDO</td>
                        <td scope="col" align="center" colspan="2">'.$preciosImprimir[0]["Folio"].'</td>
                        <td scope="col" align="center" style="background-color: #ccc">BOLETIN</td>
                        <td scope="col" align="center" colspan="2">'.$preciosImprimir[0]["boletin"].'</td>
                        <td scope="col" align="center" style="background-color: #ccc">PERIODO</td>
                        <td scope="col" align="center" colspan="2">Del '.date("d/m/Y", strtotime($preciosImprimir[0]["fech_inicio"])).' al '.date("d/m/Y", strtotime($preciosImprimir[0]["fech_fin"])).'</td>
                        <td style="width:15px;" align="center" class="margen"></td>
                    </tr>

                    

                    
                    <tr>
                        <td style="width:15px;" align="center" class="margen"></td>
                        <td scope="col" align="center" colspan="9" style="background-color: #ccc">FORMA DE COBRO PARA LA APLICACION</td>
                        <td style="width:15px;" align="center" class="margen"></td>
                    </tr>
                    <tr>
                        <td style="width:15px;" align="center" class="margen"> </td>
                        <td scope="col" rowspan="2" align="center" style="background-color: #ccc">PARA</td>
                        <td scope="col"  align="center" colspan="8" style="max-width:650px; border-bottom: 0;">'.$parte1.'</td>
                        <td style="width:15px;" align="center" class="margen"></td>
                    </tr>
                    <tr>
                        <td style="width:15px;" align="center" class="margen"> </td>
                         <td scope="col" align="center" colspan="8" style="max-width:650px; border-top: 0;">'.$parte2.'</td>
                        <td style="width:15px;" align="center" class="margen"></td>
                    </tr>
                    <tr>
                        <td style="width:15px;" align="center" class="margen"> </td>
                        <td scope="col" align="center" style="background-color: #ccc">APLICA A</td>
                        <td scope="col" align="center" colspan="8">'.$preciosImprimir[0]["aplicaa"].'</td>
                        <td style="width:15px;" align="center" class="margen"></td>
                    </tr>
                    <tr>
                        <td style="border: 0; font-size:3px" colspan="11" class="saltos">
                            <br>
                        </td>
                    </tr>
                
                    <tr>
                        <td style="width:15px;" align="center" class="margen"></td>
                        <th scope="col" align="center" style="background-color: #ccc">CODIGO</th>
                        <th scope="col" align="center" style="background-color: #ccc">DESCRIPCIÓN</th>
                        <th scope="col" align="center" style="background-color: #ccc" colspan="2">PRECIO CAT ART</th>
                        <th scope="col" align="center" style="background-color: #ccc">PRECIO DE OFTA</th>
                        <th scope="col" align="center" style="background-color: #ccc">DIFERENCIA</th>
                        <th scope="col" align="center" style="background-color: #ccc">CALCULO POR %</th>
                        <th scope="col" align="center" style="background-color: #ccc">PRECIO OFTA</th>
                        <th scope="col" align="center" style="background-color: #ccc">DIFERENCIA</th>
                        <td style="width:15px;" align="center" class="margen"></td>
                    </tr>

                    <!--Hasta aqui vamos aqui flta imprimir precios-->
                   <tr>
                        <td style="width:15px;" align="center" class="margen"></td>
                        <td align="center"><b>'.$preciosImprimir[$registrosimpresos]["art"].'</b></td>
                        <td align="center" rowspan="5" style="font-size:9px"><b>'.$preciosImprimir[$registrosimpresos]["des1"].'</b></td>
                        <td align="center">1</td>
                        <td align="center">$'.number_format($preciosImprimir[$registrosimpresos]["precio_vta0"],2,".",",").'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["primerPrecio_ofer0"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["primeraDifer0"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["porcentaje0"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["segundoPrecio_ofer0"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["segundaDifer0"].'</td>
                        <td style="width:15px;" align="center" class="margen"></td>
                    </tr>
                    <tr>
                        <td style="width:15px;" align="center" class="margen"></td>
                        <td rowspan="4" colspan="1" align="center"></td>
                        <td align="center">2</td>
                        <td align="center">$'.number_format($preciosImprimir[$registrosimpresos]["precio_vta1"],2,".",",").'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["primerPrecio_ofer1"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["primeraDifer1"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["porcentaje1"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["segundoPrecio_ofer1"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["segundaDifer1"].'</td>
                        <td style="width:15px;" align="center" class="margen"></td>
                    </tr>
                    <tr>
                        <td style="width:15px;" align="center" class="margen"></td>
                        <td align="center">3</td>
                        <td align="center">$'.number_format($preciosImprimir[$registrosimpresos]["precio_vta2"],2,".",",").'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["primerPrecio_ofer2"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["primeraDifer2"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["porcentaje2"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["segundoPrecio_ofer2"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["segundaDifer2"].'</td>
                        <td style="width:15px;" align="center" class="margen"></td>
                    </tr>
                    <tr>
                        <td style="width:15px;" align="center" class="margen"></td>
                        <td align="center">4</td>
                       <td align="center">$'.number_format($preciosImprimir[$registrosimpresos]["precio_vta3"],2,".",",").'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["primerPrecio_ofer3"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["primeraDifer3"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["porcentaje3"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["segundoPrecio_ofer3"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["segundaDifer3"].'</td>
                        <td style="width:15px;" align="center" class="margen"></td>
                    </tr>
                    <tr>
                        <td style="width:15px;" align="center" class="margen"></td>
                        <td align="center">5</td>
                        <td align="center">$'.number_format($preciosImprimir[$registrosimpresos]["precio_vta4"],2,".",",").'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["primerPrecio_ofer4"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["primeraDifer4"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["porcentaje4"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["segundoPrecio_ofer4"].'</td>
                        <td align="center">'.$preciosImprimir[$registrosimpresos]["segundaDifer4"].'</td>
                        <td style="width:15px;" align="center" class="margen"></td>
                    </tr>
                    <tr style="border: 0;">
                        <td style="border: 0; font-size:3px" colspan="11" class="saltos">
                            .
                        </td>
                    </tr>

                    <!--aqui termino un for-->
        
                    <tr style="border: 0;">
                        <td style="border: 0; font-size:3px" colspan="11" class="saltos">
                            .
                        </td>
                    </tr>
                    <tr style="border: 0;">
                        <td colspan="4" class="saltos" align="center" style="height:5px; border: 0;">___________________________</td>
                        <td colspan="3" class="saltos" align="center" style="height:5px; border: 0;">___________________________</td>
                        <td colspan="4" class="saltos" align="center" style="height:5px; border: 0;">___________________________</td>
                    </tr>
                    <tr style="border: 0; ">
                        <td colspan="4" class="saltos" align="center" style="height:5px; border: 0;">AUTORIZO</td>
                        <td colspan="3" class="saltos" align="center" style="height:5px; border: 0;">COMPRAS</td>
                        <td colspan="4" class="saltos" align="center" style="height:5px; border: 0;">APLICA</td>
                    </tr>
                    <tr style="border: 0; ">
                        <td colspan="4" class="saltos" align="center" style="height:5px; border: 0;">ISRAEL DOMINGUEZ VELA</td>
                        <td colspan="3" class="saltos" align="center" style="height:5px; border: 0;">'.$preciosImprimir[0]["vendedor"].'</td>
                        <td colspan="4" class="saltos" align="center" style="height:5px; border: 0;"></td>
                    </tr>


                </table>
                
            </div>
        </div>
        <p style="margin: 0; padding:0; border:0px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Páginas '.($i+1).' de '.$hojas.'.</p>
        </div>





        <style>@page {
        
         margin: 0;
         padding:0;
        }
        table{
            table-layout: fixed;
            width:750px;
            margin: 50px;
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

        </style>
</body>
</html>