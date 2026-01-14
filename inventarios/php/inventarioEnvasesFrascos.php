<?php
include_once './obtenerInventarioEnvasesFrascos.php';

$pdo = new conePDO();
$con = $pdo->conectar();

try {
    $resultado = array();
    if (isset($_GET['acumulado'])) {
        $resultado = getInventarioEnvases(TRUE);
    } else if(isset($_GET['idMes'])){
        $idMes = $_GET['idMes'];
        $resultado = getInventarioEnvases(FALSE, $idMes);
    }
    echo json_encode(['error' => false, 'resultado' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
