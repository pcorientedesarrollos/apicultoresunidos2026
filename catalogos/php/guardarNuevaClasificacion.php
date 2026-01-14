<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$post = file_get_contents('php://input');

try {
    if (!$post) {
        throw new Exception('No se recibieron datos');
    } else {
        $nuevaClasificacion = json_decode($post);
    }

    if (isset($nuevaClasificacion->idSobrante)) {
        $success_message = 'Se ha editado el registro';
        $sqlInsert = $con->prepare("UPDATE sobrantes SET nombre = :nombre, codigo = :codigo WHERE idSobrante = :idSobrante");
        $sqlInsert->bindParam(':idSobrante', $nuevaClasificacion->idSobrante);
    } else {
        $success_message = 'Se ha agregado una nueva clasificación';
        $sqlInsert = $con->prepare("INSERT INTO sobrantes (nombre, codigo) VALUES (:nombre, :codigo)");
    }

    $sqlInsert->bindParam(':nombre', $nuevaClasificacion->nombre);
    $sqlInsert->bindParam(':codigo', $nuevaClasificacion->codigo);            
    $sqlInsert->execute();

    if ($sqlInsert == false) {
        throw new Exception($con->errorInfo());
    }
    echo json_encode(['error' => false, 'message' => $success_message]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}

