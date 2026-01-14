<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    $con->beginTransaction();

    $idEntrega = $_GET["idEntrega"];

    $sqlEliminar = $con->prepare("DELETE FROM chequesentregados_detalle WHERE idEntrega = :idEntrega");
    $sqlEliminar->bindParam(':idEntrega', $idEntrega);
    $sqlEliminar->execute();
    if (!$sqlEliminar) {
        throw new Exception($con->errorInfo());
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha eliminado el registro.']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
