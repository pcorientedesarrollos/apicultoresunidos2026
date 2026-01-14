<?php
include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = "SELECT idzona, zona, referencia FROM zonas
ORDER BY CONVERT(SUBSTR(zona FROM 6 FOR 2), UNSIGNED INTEGER) ASC;";

try {

    $query = $con->prepare($sql);
    $query->execute();
    if (!$query) {
        throw new Exception($con->errorInfo());
    }

    $resultadoListaZonas = $query->fetchAll(PDO::FETCH_ASSOC);
    $resultado = array();

    // Por cada zona, crearle su valor proyeccion y real los dos en cero

    foreach ($resultadoListaZonas as $zona) {
        $zona['proyeccion'] = 0;
        $zona['kilogramos'] = 0;
        $zona['real'] = 0;
        array_push($resultado, $zona);
    }

    echo json_encode(['error' => false, 'resultado' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
