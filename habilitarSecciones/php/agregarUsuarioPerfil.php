<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

// Guarda los datos del usuario del sistema incluyendo la clave de acceso
$postdata = file_get_contents('php://input');

try {
    if (!$postdata) {
        throw new Exception('No se recibieron datos');
    } else {
        $perfil = json_decode($postdata);
    }

    $sqlUpdate = $con->prepare("UPDATE usuarios SET idPerfil = :idPerfil WHERE idUsuario = :idUsuario");
    $sqlUpdate->bindParam(':idPerfil', $perfil->idPerfil);
    $sqlUpdate->bindParam(':idUsuario', $perfil->nuevoUsuario->idUsuario);
    $sqlUpdate->execute();
    if ($sqlUpdate == false) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'message' => 'Se ha modificado el perfil del usuario']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
