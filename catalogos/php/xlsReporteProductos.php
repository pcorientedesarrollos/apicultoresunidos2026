<?php

include_once '../../catalogos/php/productosReporte.php';
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename = Productos.xls");
header("Prafma: no-cache");
header("Expires:0");

try {

    //  if(!isset($_GET['opcion'])) {
    //      throw new Exception('No se recibieron parámetros');
    //  } else 

    //     $opcionMes = new stdClass();
    //     $opcionMes->opcion = $_GET['opcion'];

    //     if($opcionMes->opcion == '2'){
    //         if(!isset($_GET['mes'])){
    //             throw new Exception('No se recibieron parámetros');
    //         } else {
    //             $opcionMes->mes = $_GET['mes'];
    //         }
    //     }
    //     $acumulado = $opcionMes->opcion == '1' ? true : false;
    //  }
     
    //  if($acumulado){
        $productossubcuentas = ObtenerSubCuentas();
    //  } else {
    //     $idMes = $opcionMes->mes;
    //     $productossubcuentas = obtenerInventarioApicola($idMes, FALSE);
    //  }


    //  Imprimir el formato del excel


    echo '
    <table>
        <tr>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="3">' . utf8_decode('Apicultores Unidos de la Peninsula S.A. de C.V.') . '</th>
        </tr>
        <tr>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="3">' . utf8_decode('PRODUCTOS') . '</th>
        </tr>
        <tr>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="3">' . utf8_decode('Planta Mérida, Yucatán') . '</th>
        </tr>
    </table>' . "<br>";


     foreach($productossubcuentas as $inventario) {

             echo '<table border="1">
             <thead>
                <tr>
                    <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="3">' . utf8_decode($inventario['subcuenta']) .'</th>
                </tr>
                <tr>
                  <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1">' . utf8_decode('CLAVE') . '</th>
                  <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1">' . utf8_decode('PRODUCTO') . '</th>
                  <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1">' . utf8_decode('PRECIO VENTA') . '</th>
                  <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1">' . utf8_decode('EXISTENCIA') . '</th>
              </tr>
               
             </thead>

             <tbody>';


             foreach($inventario['subSubcuentas'] as $registro) {
                                 echo '<tr>
                                    <td style="text-align: left; font-size: 12px;">' . utf8_decode($registro['clave']) . '</td>
                                    <td style="text-align: left; font-size: 12px;">' . utf8_decode($registro['subcuentaConcepto']) . '</td>
                                    <td style="text-align: left; font-size: 12px;">' . utf8_decode($registro['precioUnitario']) . '</td>
                                    <td style="text-align: left; font-size: 12px;">' . utf8_decode($registro['existencia']) . '</td>
                                </tr>';
                            }

             echo '
             </tbody>
             </table>' . "<br>";
     }












} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}
