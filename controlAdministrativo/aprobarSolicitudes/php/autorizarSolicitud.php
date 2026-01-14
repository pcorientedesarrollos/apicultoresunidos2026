<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    if (!isset($_GET['id']) && !isset($_GET['tipo'])) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $tipo = $_GET['tipo'];
        $id = $_GET['id'];
        $estado = $_GET['estado'];
        if ($tipo == '1') {
            $table = "solicitudcompra_conceptos";
            $idDetalle = 'idDetalleCompra';
        } else if ($tipo == 2) {
            $table = "solicitudservicio_conceptos";
            $idDetalle = 'idDetalleServicio';
        }
    }

    $sql = "UPDATE $table SET estado = :estado WHERE $idDetalle = :idDetalle";
    $data = $con->prepare($sql);
    $data->bindParam(':idDetalle', $id);
    $data->bindParam(':estado', $estado);
    $data->execute();
    if (!$data) {
        throw new ErrorException($mensaje, 0, $severidad, $fichero, $línea);
    }

    echo json_encode(['error' => false, 'message' => 'Se cambió el estado del requerimiento']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
