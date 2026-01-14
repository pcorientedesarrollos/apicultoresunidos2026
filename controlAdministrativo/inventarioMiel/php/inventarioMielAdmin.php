<?php

include_once './obtenerInventarioMiel.php';


function realizarInventarioAdmin($data)
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
        $informacionInventarioMiel = obtenerInventarioMielAdministracion(false, true);
    } else if ($fechas) {
        $fechaUno = $opcionMes->fechaUno;
        $fechaDos = $opcionMes->fechaDos;
        $informacionInventarioMiel = obtenerInventarioMielAdministracion(false, false, $fechaUno, $fechaDos);
    } else if ($mes) {
        $idMes = $opcionMes->mes;
        $informacionInventarioMiel = obtenerInventarioMielAdministracion($idMes, false);
    }

    return $informacionInventarioMiel;
}

try {
    if (!isset($_GET['informeFinanciero'])) {
        $data = file_get_contents('php://input');
        $data = json_decode($data);
        $result = realizarInventarioAdmin($data);
        echo json_encode(['error' => false, 'resultado' => $result]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit();
}
