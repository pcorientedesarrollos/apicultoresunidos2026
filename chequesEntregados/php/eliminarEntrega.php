<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    if (!isset($_GET['idEncabezado'])) {
        throw new Exception('No se recibió parámetro');
    } else {
        $idEncabezado = $_GET['idEncabezado'];
    }

    $sql = "DELETE FROM chequesentregados_encabezado WHERE idEncabezado = :idEncabezado;
    DELETE FROM chequesentregados_detalle WHERE idEncabezado = :idEncabezado";

    $data = $con->prepare($sql);
    $data->bindParam(':idEncabezado', $idEncabezado);
    $data->execute();
    if (!$data) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'message' => 'El registro fue eliminado']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
