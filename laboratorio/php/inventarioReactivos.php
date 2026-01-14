<?php
include_once './obtenerInventarioReactivoss.php';

$pdo = new conePDO();
$con = $pdo->conectar();

try {
    $resultado = array();
    if (isset($_GET['acumulado'])) {
        $resultado = getInventarioReactivos(TRUE);
    } else if(isset($_GET['idMes'])){
        $idMes = $_GET['idMes'];
        $resultado = getInventarioReactivos(FALSE, $idMes);
    }
    echo json_encode(['error' => false, 'resultado' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}