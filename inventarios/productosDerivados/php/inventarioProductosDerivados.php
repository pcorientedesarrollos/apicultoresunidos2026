<?php

include_once './obtenerInventarioProductosDerivados.php';


function realizarFuncionesInventarioDerivados($data)
{
    if (!$data) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $opcionMes = $data;
        $acumulado = $opcionMes->opcion == '1' ? true : false;
        $mes = $opcionMes->opcion == '2' ? true : false;
        $fechas = $opcionMes->opcion == '3' ? true : false;
    }

    if ($acumulado) {
        $informacionInventarioDerivado = obtenerInventarioDerivado(false, true);
    } else if ($fechas) {
        $fechaUno = $opcionMes->fechaUno;
        $fechaDos = $opcionMes->fechaDos;
        $informacionInventarioDerivado = obtenerInventarioDerivado(false, false, $fechaUno, $fechaDos);
    } else if ($mes) {
        $idMes = $opcionMes->mes;
        $informacionInventarioDerivado = obtenerInventarioDerivado($idMes, false);
    }

    return $informacionInventarioDerivado;
}

try {
    if (!isset($_GET['informeFinanciero'])) {
        $data = file_get_contents('php://input');
        $data = json_decode($data);
        $result = realizarFuncionesInventarioDerivados($data);
        echo json_encode(['error' => false, 'resultado' => $result]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit();
}
