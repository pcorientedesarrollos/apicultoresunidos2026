<?php

include_once '../../inventarios/productosDerivados/php/obtenerInventarioProductosDerivados.php';
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename = Inventario de Envases y frascos.xls");
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
        $resultado = obtenerInventarioDerivado(false, true);
    } else if (isset($opcionMes->mes)) {
        $idMes = $opcionMes->mes;
        $resultado = obtenerInventarioDerivado($idMes, false);
    }
    echo '<table>
        <thead>
            <tr style="font-size:18px">
                <th colspan=10>
                    Apicultores Unidos de la Peninsula S.A. de C.V.
                </th>
            </tr>
            <tr style="font-size:16px">
                <th colspan=10>
                    INVENTARIO DE ENVASES Y FRASCOS
                </th>
            </tr>
            <tr></tr>
            <tr></tr>
            <tr></tr>
            <tr>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Fecha</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Nombre</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Catalogo</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Concepto</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="2">Cantidad</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Existencia (kg.)</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Precio unitario</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="2">Importe</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Importe acumulado</th>
            </tr>
            <tr>
                <th style="text-transform: none; font-weight: 600;">Entradas</th>
                <th style="text-transform: none; font-weight: 600;">Salidas</th>
                <th style="text-transform: none; font-weight: 600;">Entradas</th>
                <th style="text-transform: none; font-weight: 600;">Salidas</th>
            </tr>
        </thead>';

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
