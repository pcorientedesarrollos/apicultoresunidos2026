<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    if (!isset($_GET['id'])) {
        throw new Exception('No se recibió parámetro');
    } else {
        $id = $_GET['id'];
        $valor = $_GET["valor"];
    }

    $sql = "UPDATE otrassalidasdetalle SET cajaChica = :valor WHERE idOtrasSalidasDetalle = :id";
    $data = $con->prepare($sql);
    $data->bindParam(':valor', $valor);
    $data->bindParam(':id', $id);
    $data->execute();
    if (!$data) {
        throw new Exception('Error');
    }

    echo json_encode(['error' => false, 'message' => 'Cambio guardado']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
