<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if (isset($_GET["idPerfil"])) {

    try {

        $perfil = $_GET["idPerfil"];
        $con->beginTransaction();

        $actualizarUsuarios = "UPDATE usuarios SET idPerfil = '' WHERE idPerfil = :idPerfil";
        $datos = $con->prepare($actualizarUsuarios);
        $datos->bindParam(':idPerfil', $perfil);
        $datos->execute();
        if($datos == false){
            throw new Exception($con->errorInfo());
        }

        $borrarPermisos = "DELETE FROM permisos WHERE idPerfil = :idPerfil";
        $datosPermiso = $con->prepare($borrarPermisos);
        $datosPermiso->bindParam(':idPerfil', $perfil);
        $datosPermiso->execute();
        if ($datosPermiso == false) {
            throw new Exception($con->errorInfo());
        }

        $borrarPerfil = "DELETE FROM perfiles WHERE idPerfil = :idPerfil";
        $datosPerfil = $con->prepare($borrarPerfil);
        $datosPerfil->bindParam(':idPerfil', $perfil);
        $datosPerfil->execute();
        if ($datosPerfil == false) {
            throw new Exception($con->errorInfo());
        }

        $con->commit();
        echo json_encode(['error' => false, 'message' => 'Consulta realizada']);
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
    }

}
?>