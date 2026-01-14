<?php

include_once '../../inventarios/php/obtenerInventarioMielDisponible.php';
$data = file_get_contents('php://input');

try {
    if (!$data) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $opcionVista = json_decode($data);
        $idTipoDeMiel = $opcionVista->tipoMiel;
        $acumulado = $opcionVista->opcion == '1' ? true : false;
    }
    if ($acumulado) {
        $InformacionInventarioCera = obtenerInventarioMielDisponible($idTipoDeMiel);
    } else {
        $idZona = $opcionVista->zona;
        $InformacionInventarioCera = obtenerInventarioMielDisponible($idTipoDeMiel, $idZona);
    }

    echo json_encode(['error' => false, 'resultado' => $InformacionInventarioCera]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}