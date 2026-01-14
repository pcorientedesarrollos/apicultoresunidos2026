<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    if (!isset($_GET['idEquipo'])) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $idEquipo = $_GET["idEquipo"];
    }

    $con->beginTransaction();

    $sqlEliminarEquipo = "DELETE FROM equipos WHERE idEquipo = :idEquipo";
    $respEliminarEquipo = $con->prepare($sqlEliminarEquipo);
    $respEliminarEquipo->bindParam(':idEquipo', $idEquipo);
    $respEliminarEquipo->execute();
    if ($respEliminarEquipo == false) {
        throw new ErrorException($mensaje, 0, $severidad, $fichero, $línea);
    } else {
        $sqlHistoriales = "DELETE FROM historiales WHERE idEquipo = :idEquipo";
        $respEliminarHistoriales = $con->prepare($sqlHistoriales);
        $respEliminarHistoriales->bindParam(':idEquipo', $idEquipo);
        $respEliminarHistoriales->execute();
        if ($respEliminarHistoriales == false) {
            throw new ErrorException($mensaje, 0, $severidad, $fichero, $línea);
        }
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Activo eliminado']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
}
