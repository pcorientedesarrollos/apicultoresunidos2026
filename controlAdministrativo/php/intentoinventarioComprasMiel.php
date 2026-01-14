<?php
include_once '../../inventarios/php/obtenerInventarioMiel.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    $resultado = array();
    $tipoMiel = isset($_GET['miel']) ? $_GET['miel'] : '1';

    if (isset($_GET['acumulado'])) {
        $resultado = getInventarioMiel($tipoMiel, TRUE);
    } else if (isset($_GET['idMes'])) {
        $idMes = $_GET['idMes'];
        $resultado = getInventarioMiel($tipoMiel, FALSE, $idMes);
    }
    echo json_encode(['error' => false, 'resultado' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
