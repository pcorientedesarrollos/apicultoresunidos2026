<?php
include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    if (!isset($_GET['idProyeccion'])) {
        throw new Exception('No se recibió parámetro');
    } else {
        $idProyeccion = $_GET['idProyeccion'];
    }

    $sql = "DELETE FROM proyeccionencabezado WHERE idProyeccion = :idProyeccion;
    DELETE FROM proyecciondetalle WHERE idProyeccion = :idProyeccion";

    $data = $con->prepare($sql);
    $data->bindParam(':idProyeccion', $idProyeccion);
    $data->execute();
    if (!$data) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'message' => 'El registro fué eliminado']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
