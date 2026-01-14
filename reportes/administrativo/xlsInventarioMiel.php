<?php

// include_once '../DAOConeccion/conePDO.php';
include_once '../../controlAdministrativo/inventarioMiel/php/obtenerInventarioMiel.php';

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename = Inventario de Miel.xls");
header("Prafma: no-cache");
header("Expires:0");

try {
    if (!isset($_GET['opcion'])) {
        throw new Exception('No se recibieron parámetros');
    } else {

        $opcionMes = new stdClass();
        $opcionMes->opcion = $_GET['opcion'];

        if ($opcionMes->opcion == '2') {
            if (!isset($_GET['mes'])) {
                throw new Exception('No se recibieron parámetros');
            } else {
                $opcionMes->mes = $_GET['mes'];
            }
        }
        $acumulado = $opcionMes->opcion == '1' ? true : false;
    }
    $resultado = array();
    if ($acumulado) {
        $resultado = obtenerInventarioMielAdministracion(false, true);
    } else if ($fechas) {
        $fechaUno = $opcionMes->fechaUno;
        $fechaDos = $opcionMes->fechaDos;
        $resultado = obtenerInventarioMielAdministracion(false, false, $fechaUno, $fechaDos);
    } else if ($mes) {
        $idMes = $opcionMes->mes;
        $resultado = obtenerInventarioMielAdministracion($idMes, false);
    }


    echo '
    <table style="font-size: 10px" width="100%">
    <thead>
        <tr style="font-size:18px">
            <th colspan=7>
                Apicultores Unidos de la Peninsula S.A. de C.V.
            </th>
        </tr>
        <tr style="font-size:16px">
            <th colspan=7>
            INVENTARIO DE MIEL
            </th>
        </tr>
        <tr>
            <th width="5%">Producto</th>
            <th width="5%">Saldo inicial</th>
            <th width="5%">Entrada</th>
            <th width="5%">Salida</th>
            <th width="5%">Saldo Final</th>
            <th width="5%">Precio Venta</th>
            <th width="5%">Importe Venta</th>
        </tr>
    </thead>';
        
        foreach ($resultado['productos'] as $inventario) {
       echo '<tr>
                <td width="5%" style="text-align: left">' . utf8_decode($inventario['concepto']) . '</td>
                <td width="5%" style="text-align: right">' . number_format($inventario['existencia'], 2, '.', ',') . '</td>
                <td width="5%" style="text-align: right">' . number_format($inventario['entradas'], 2, '.', ',') . '</td>
                <td width="5%" style="text-align: right">' . number_format($inventario['salidas'], 2, '.', ',') . '</td>
                <td width="5%" style="text-align: right">' . number_format($inventario['saldo'], 2, '.', ',') . '</td>
                <td width="5%" style="text-align: right">$ ' . number_format($inventario['precioVenta'], 2, '.', ',') . '</td>
                <td width="5%" style="text-align: right">$ ' . number_format($inventario['importeVenta'], 2, '.', ',') . '</td>
            </tr>';
        }

        echo '<tr>
        <td width="5%" style="text-align: left"> Totales </td>
        <td width="5%" style="text-align: right">' . number_format($resultado['totalProductos'], 2, '.', ',') . '</td>
        <td width="5%" style="text-align: right">' . number_format($resultado['totalEntradas'], 2, '.', ',') . '</td>
        <td width="5%" style="text-align: right">' . utf8_decode($resultado['totalSalidas']) . '</td>
        <td width="5%" style="text-align: right">$ ' . number_format($resultado['totalSaldo'], 2, '.', ',') . '</td>
        <td width="5%" style="text-align: right"></td>
        <td width="5%" style="text-align: right">$ ' . number_format($resultado['totalImporteVenta'], 2, '.', ',') . '</td>
        </tr>
        </tbody>
        </table>';
        
        echo ' <br> ';

        echo '<table width="100%">
        <thead>
            <tr style="font-size:16px">
            <th colspan=4>
                COBRANZA
            </th>
            </tr>
            <tr>
                <th width="25%" style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Bancos</th>
                <th width="25%" style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Caja</th>
                <th width="25%" style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Pendientes</th>
                <th width="25%" style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Total</th>
            </tr>
        </thead>
        <tbody>
        <tr>
        <td  width="25%"></td>
        <td  width="25%"></td>
        <td  width="25%"></td>
        <td  width="25%"></td>
        </tr>
        </tbody>
        </table>

        <br>
        <table>
        <thead>
        <tr>
        <tr style="font-size:16px">
        </tr>
            <th>&nbsp;</th>

            <th>
            <p>__________________________</p>
            <p>C.P David Santos Redondo</p>
            <p>Reviso</p>
            </th>

            <th>
            <p>__________________________</p>
            <p>C.P. Ibis Banderas Couoh</p>
            <p>' . utf8_decode('Autorizó') . '</p>
            </th>

        </tr>

        </thead>
        </table>

        <br> <br>
        <table>
        <thead>
        <tr>
        <tr style="font-size:16px">
        </tr>

        <th>&nbsp;</th>

        <th>
        <p>__________________________</p>
        <p>' . utf8_decode('Laura Sofía López Domínguez') . '</p>
        </th>

        <th>
        <p>__________________________</p>
        <p>Marleny Dzul Canul</p>
        <p>' . utf8_decode('Almacén') . '</p>
        </th>
        <th>&nbsp;</th>
        </tr>
        </thead>
        </table>
     
        ';

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
