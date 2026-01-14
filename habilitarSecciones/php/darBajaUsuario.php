<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idUsuario = file_get_contents('php://input');

try {

    if (!$idUsuario) {
        throw new Exception('No se recibió parámetro');
    }

    // Usar la base control:
    $sqlUseDB = $con->prepare("USE apicultorescontrol");
    $sqlUseDB->execute();
    if ($sqlUseDB == false) {
        throw new Exception($con->errorInfo());
    }
    
    // bajar usuarios
    session_start();
    $idEmpresa = $_SESSION['idEmpresa'];
    $database = $_SESSION['database'];
    session_write_close();

    $query = $con->prepare("DELETE FROM usuarios WHERE idUsuario = :idUsuario AND oculto IS NULL");
    $query->bindParam(':idUsuario', $idUsuario);
    $query->execute();
    if ($query == false) {
        throw new Exception($con->errorInfo());
    }

    // volver a cambiar base de datos
    $sqlUseDB = $con->prepare("USE $database");
    $sqlUseDB->execute();
    if ($sqlUseDB == false) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'message' => 'Se ha dado de baja al usuario']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
