<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$postdata = file_get_contents('php://input');

try {
    $con->beginTransaction();
    if (!$postdata) {
        throw new Exception('No se recibieron datos');
    } else {
        $perfil = json_decode($postdata);
    }

    // Primero eliminar todos los permisos que tiene este perfil
    $sqlDelete = $con->prepare("DELETE FROM permisos WHERE idPerfil = :idPerfil");
    $sqlDelete->bindParam(':idPerfil', $perfil->idPerfil);
    $sqlDelete->execute();
    if ($sqlDelete == false) {
        throw new Exception($con->errorInfo());
    }

    // Agregar los permisos que vienen en el arreglo
    foreach ($perfil->secciones as $seccion) {
        if (isset($seccion->onProfile) && $seccion->onProfile == true) {
            $sqlInsert = $con->prepare("INSERT INTO permisos (idSeccion, idPerfil) VALUES (:idSeccion, :idPerfil)");
            $sqlInsert->bindParam(':idSeccion', $seccion->idSeccion);
            $sqlInsert->bindParam(':idPerfil', $perfil->idPerfil);
            $sqlInsert->execute();
            if ($sqlInsert == false) {
                throw new Exception($con->errorInfo());
            }
        }
    }


    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha actualizado los permisos del perfil']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
