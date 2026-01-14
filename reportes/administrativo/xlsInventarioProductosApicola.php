<?php

include_once '../../inventarios/php/obtenerInventarioApicola.php';
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename = Inventario de Productos apícolas.xls");
header("Prafma: no-cache");
header("Expires:0");

try {
     if(!isset($_GET['opcion'])) {
         throw new Exception('No se recibieron parámetros');
     } else {

        $opcionMes = new stdClass();
        $opcionMes->opcion = $_GET['opcion'];

        if($opcionMes->opcion == '2'){
            if(!isset($_GET['mes'])){
                throw new Exception('No se recibieron parámetros');
            } else {
                $opcionMes->mes = $_GET['mes'];
            }
        }
        $acumulado = $opcionMes->opcion == '1' ? true : false;
     }
     
     if($acumulado){
        $informacionInventarioApicola = obtenerInventarioApicola(FALSE, TRUE);
     } else {
        $idMes = $opcionMes->mes;
        $informacionInventarioApicola = obtenerInventarioApicola($idMes, FALSE);
     }


    //  Imprimir el formato del excel


    echo '
    <table>
        <tr>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="9">' . utf8_decode('Apicultores Unidos de la Peninsula S.A. de C.V.') . '</th>
        </tr>
        <tr>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="9">' . utf8_decode('CONTROL DE INVENTARIO PRODUCTO APICOLA') . '</th>
        </tr>
        <tr>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="9">' . utf8_decode('Planta Mérida, Yucatán') . '</th>
        </tr>
    </table>' . "<br>";


     foreach($informacionInventarioApicola['productos'] as $inventario) {
         if(count($inventario['registros']) > 0) {

             echo '<table border="1">
             <thead>
                <tr>
                    <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="9">' . utf8_decode($inventario['subconceptoCC']) .'</th>
                </tr>
                 <tr>
                     <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1">' . utf8_decode('Fecha') . '</th>
                     <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1">' . utf8_decode('Nombre') . '</th>
                     <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1">' . utf8_decode('Concepto/Descripción') . '</th>
                     <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1">' . utf8_decode('Entrada') . '</th>
                     <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1">' . utf8_decode('Salida') . '</th>
                     <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1">' . utf8_decode('Costo unitario') . '</th>
                     <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1">' . utf8_decode('Importe Entrada') . '</th>
                     <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1">' . utf8_decode('Importe Salida') . '</th>
                     <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1">' . utf8_decode('Existencia') . '</th>
                 </tr>
             </thead>
             <tbody>
                 <tr>
                     <td style="text-align: left; font-size: 12px;">Inicial</td>
                     <td style="text-align: left; font-size: 12px;"></td>
                     <td style="text-align: left; font-size: 12px;"></td>
                     <td style="text-align: right; font-size: 12px;">' . number_format($inventario['existenciaPasada'], 2, '.', ',') . '</td>
                     <td style="text-align: left; font-size: 12px;"></td>
                     <td style="text-align: left; font-size: 12px;"></td>
                     <td style="text-align: right; font-size: 12px;">' . number_format($inventario['importePasado'], 2, '.', ',') . '</td>
                     <td style="text-align: left; font-size: 12px;"></td>
                     <td style="text-align: right; font-size: 12px;">' . number_format($inventario['existenciaPasada'], 2, '.', ',') . '</td>
                 </tr>';
                 foreach($inventario['registros'] as $registro) {
                     echo '<tr>
                        <td style="text-align: left; font-size: 12px;">' . $registro['fecha'] . '</td>
                        <td style="text-align: left; font-size: 12px;">' . utf8_decode($registro['nombre']) . '</td>
                        <td style="text-align: left; font-size: 12px;">' . utf8_decode($registro['descripcion']) . '</td>
                        <td style="text-align: right; font-size: 12px;">' . number_format($registro['entrada'], 2, '.', ',') . '</td>
                        <td style="text-align: right; font-size: 12px;">' . number_format($registro['salida'], 2, '.', ',') . '</td>
                        <td style="text-align: right; font-size: 12px;">' . number_format($registro['costoUnitario'], 2, '.', ',') . '</td>
                        <td style="text-align: right; font-size: 12px;">' . number_format($registro['importeEntrada'], 2, '.', ',') . '</td>
                        <td style="text-align: right; font-size: 12px;">' . number_format($registro['importeSalida'], 2, '.', ',') . '</td>
                        <td style="text-align: right; font-size: 12px;">' . number_format($registro['existencia'], 2, '.', ',') . '</td>
                    </tr>';
                }

            echo '<tr>
                     <td style="text-align: left; font-size: 12px;"></td>
                     <td style="text-align: right; font-size: 12px;">Total</td>
                     <td style="text-align: left; font-size: 12px;"></td>
                     <td style="text-align: right; font-size: 12px;">' . number_format($inventario['entradas'], 2, '.', ',') . '</td>
                     <td style="text-align: right; font-size: 12px;">' . number_format($inventario['salidas'], 2, '.', ',') . '</td>
                     <td style="text-align: right; font-size: 12px;"></td>
                     <td style="text-align: right; font-size: 12px;">' . number_format($inventario['importeEntrada'], 2, '.', ',') . '</td>
                     <td style="text-align: right; font-size: 12px;">' . number_format($inventario['importeSalida'], 2, '.', ',') . '</td>
                     <td style="text-align: right; font-size: 12px;">' . number_format($inventario['acumulado'], 2, '.', ',') . '</td>
                 </tr>
             </tbody>
         </table>' . "<br>";



         } else {
             continue;
         }
     }












} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}
