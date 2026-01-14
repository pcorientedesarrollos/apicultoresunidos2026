<?php

include_once '../../inventarios/php/obtenerInventarioCera.php';
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename = Inventario de Cera.xls");
header("Prafma: no-cache");
header("Expires:0");

try {
    if (!isset($_GET['opcion']) || !isset($_GET['tipoCera'])) {
        throw new Exception('No se recibieron parámetros');
    } else {

        $opcionMes = new stdClass();
        $opcionMes->opcion = $_GET['opcion'];
        $tipoCera = $_GET['tipoCera'];

        $nombreCera = $tipoCera == '1' ? 'Cera convencional' : 'Cera orgánica';

        if ($opcionMes->opcion == '2') {
            if (!isset($_GET['mes'])) {
                throw new Exception('No se recibieron parámetros');
            } else {
                $opcionMes->mes = $_GET['mes'];
            }
        }
        $acumulado = $opcionMes->opcion == '1' ? true : false;
    }

    if ($acumulado) {
        $InformacionInventarioCera = dameInventarioCera($opcionMes, $acumulado, false, false, false, $tipoCera);
    } else {
        $idMes = $opcionMes->mes;
        if ($idMes > 1) {
            $saldoPasado = array(
                'importeAcumulado' => 0,
                'existenciaAcumulada' => 0
            );
            for ($i = intval($idMes) - 1; $i > 0; $i--) {
                $EncabezadoMesPasado = dameInventarioCera($i, $acumulado, false, true, false, $tipoCera);
                $saldoPasado['importeAcumulado'] += $EncabezadoMesPasado['importeAcumulado'];
                $saldoPasado['existenciaAcumulada'] += $EncabezadoMesPasado['existenciaAcumulada'];
            }
            $InformacionInventarioCera = dameInventarioCera($idMes, $acumulado, $saldoPasado, false, false, $tipoCera);
        } else {
            $InformacionInventarioCera = dameInventarioCera($idMes, $acumulado, false, false, false, $tipoCera);
        }
    }


    //  Imprimir el formato del excel

    echo '<table style="font-size: 10px">
    <thead>
        <tr style="font-size:18px">
            <th colspan=10>
                Apicultores Unidos de la Peninsula S.A. de C.V.
            </th>
        </tr>
        <tr style="font-size:16px">
            <th colspan=10>
                CONTROL DE INVENTARIO DE COMPRAS CERA
            </th>
        </tr>
        <tr style="font-size:16px">
            <th colspan=10>
                ' . utf8_decode($nombreCera) . '
            </th>
        </tr>
        <tr>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Fecha</th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Nombre</th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Movimiento</th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Concepto</th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Subconcepto</th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="2">Kilogramos</th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Existencia (kg.)</th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Precio por kg.</th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="2">Importe</th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Importe acumulado</th>
        </tr>
        <tr>
            <th style="text-transform: none; font-weight: 600;">Entradas</th>
            <th style="text-transform: none; font-weight: 600;">Salidas</th>
            <th style="text-transform: none; font-weight: 600;">Entradas</th>
            <th style="text-transform: none; font-weight: 600;">Salidas</th>
        </tr>
    </thead>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="text-align: right">' . number_format($InformacionInventarioCera['encabezado']['existenciaAcumuladaPasada'], 2, '.', ',') . '</td>
        <td></td>
        <td></td>
        <td></td>
        <td style="text-align: right">$ ' . number_format($InformacionInventarioCera['encabezado']['importeAcumuladoPasado'], 2, '.', ',') . '</td>
    </tr>';

    foreach ($InformacionInventarioCera['registros'] as $inventario) {
        echo '<tr>
            <td style="text-align: left">' . $inventario['fecha'] . '</td>
            <td style="text-align: left">' . utf8_decode($inventario['nombre']) . '</td>
            <td style="text-align: left">' . utf8_decode($inventario['movimiento']) . '</td>
            <td style="text-align: left">' . utf8_decode($inventario['subcuenta']) . '</td>
            <td style="text-align: left">' . utf8_decode($inventario['concepto']) . '</td>
            <td style="text-align: right">' . number_format($inventario['kgEntrada'], 2, '.', ',') . '</td>
            <td style="text-align: right">' . number_format($inventario['kgSalida'], 2, '.', ',') . '</td>
            <td style="text-align: right">' . number_format($inventario['existenciakg'], 2, '.', ',') . '</td>
            <td style="text-align: right">$ ' . number_format($inventario['precioKg'], 2, '.', ',') . '</td>
            <td style="text-align: right">$ ' . number_format($inventario['importeEntrada'], 2, '.', ',') . '</td>
            <td style="text-align: right">$ ' . number_format($inventario['importeSalida'], 2, '.', ',') . '</td>
            <td style="text-align: right">$ ' . number_format($inventario['importeAcumulado'], 2, '.', ',') . '</td>
        </tr>';
    }

    echo '<tr style="font-size:15px">
            <th style="text-align: right" colspan="4">
                <b>Total</b>
            </th>
            <td style="text-align: center"></td>
            <td style="text-align: right">' . number_format($InformacionInventarioCera['encabezado']['totalKgEntradas'], 2, '.', ',') . '</td>
            <td style="text-align: right">' . number_format($InformacionInventarioCera['encabezado']['totalKgSalidas'], 2, '.', ',') . '</td>
            <td style="text-align: right">' . number_format($InformacionInventarioCera['encabezado']['existenciaAcumulada'], 2, '.', ',') . '</td>
            <td style="text-align: right">$ ' . number_format($InformacionInventarioCera['encabezado']['totalPrecioKg'], 2, '.', ',') . '</td>
            <td style="text-align: right">$ ' . number_format($InformacionInventarioCera['encabezado']['totalEntradas'], 2, '.', ',') . '</td>
            <td style="text-align: right">$ ' . number_format($InformacionInventarioCera['encabezado']['totalSalidas'], 2, '.', ',') . '</td>
            <td style="text-align: right">$ ' . number_format($InformacionInventarioCera['encabezado']['importeAcumulado'], 2, '.', ',') . '</th>
        </tr>';

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}