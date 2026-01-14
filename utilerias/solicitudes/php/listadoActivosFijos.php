<?php
include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {

    if (!isset($_GET['idArea'])) {
        throw new Exception('No se recibieron datos');
    } else {
        $idArea = $_GET['idArea'];
    }

    $sql = "SELECT eq.idEquipo, CONCAT('AF-MID-', eq.idEquipo, ' | ', eq.nombre) as codigo
    FROM equipos eq
    WHERE eq.idArea = '$idArea'";

    $datos = $con->prepare($sql);
    $datos->execute();
    if ($datos == FALSE) {
        throw new ErrorException($mensaje, 0, $severidad, $fichero, $línea);
    }

    $resultado = array();
    $resultado = $datos->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => []]);
    exit();
}
