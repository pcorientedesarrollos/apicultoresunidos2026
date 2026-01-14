<?php
include_once './obtenerInventarioMP.php';

$pdo = new conePDO();
$con = $pdo->conectar();

try {
    $resultado = array();
    $tipoMiel = $_GET['tipoMiel'];
    if (isset($_GET['acumulado'])) {
        $resultado = getInventarioMP($tipoMiel, TRUE);
    } else if(isset($_GET['idMes'])){
        $idMes = $_GET['idMes'];
        $resultado = getInventarioMP($tipoMiel, FALSE, $idMes);
    }
    echo json_encode(['error' => false, 'resultado' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
