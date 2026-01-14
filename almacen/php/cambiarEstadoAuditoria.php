<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
date_default_timezone_set('America/Merida');
$fechaHoy = Date('Y-m-d');
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$postdata = file_get_contents('php://input');

try {

    if (!$postdata) {
        throw new Exception('No se recibieron parámetros');
    }

    $data = json_decode($postdata);
    $sql = "UPDATE auditorias SET estado = :nuevoEstado";
    if ($data->nuevoEstado == '2' || $data->nuevoEstado == '-1') {
        $sql .= ", finalizado = :fechaFinalizado";
    }
    $sql .= " WHERE idAuditoria = :idAuditoria";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idAuditoria', $data->idAuditoria);
    $datos->bindParam(':nuevoEstado', $data->nuevoEstado);
    if ($data->nuevoEstado == '2' || $data->nuevoEstado == '-1') {
        $datos->bindParam(':fechaFinalizado', $fechaHoy);
    }
    $datos->execute();
    if ($datos == false) {
        throw new Exception($con->errorInfo());
    }
    echo json_encode(['error' => false, 'message' => 'Se han actualizado los datos']);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit();
}
