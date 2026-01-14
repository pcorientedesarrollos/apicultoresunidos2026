<?php

include_once '../../inventarios/php/obtenerInventarioMielSobranteDisponible.php';
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
        $InformacionInventarioSobrante = obtenerInventarioMielSobranteDisponible($idTipoDeMiel);
    } else {
        $idSobrante = $opcionVista->sobrante;
        $InformacionInventarioSobrante = obtenerInventarioMielSobranteDisponible($idTipoDeMiel, $idSobrante);
    }

    echo json_encode(['error' => false, 'resultado' => $InformacionInventarioSobrante]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}