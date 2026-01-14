<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    if (!isset($_GET['idRequisicion'])) {
        throw new Exception('No se recibió parámetro');
    } else {
        $idRequisicion = $_GET['idRequisicion'];
    }

    $sql = "DELETE FROM requisicionencabezado WHERE idRequisicion = :idRequisicion;
    DELETE FROM requisiciondetalle WHERE idRequisicion = :idRequisicion";

    $data = $con->prepare($sql);
    $data->bindParam(':idRequisicion', $idRequisicion);
    $data->execute();
    if (!$data) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'message' => 'El registro fué eliminado']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
