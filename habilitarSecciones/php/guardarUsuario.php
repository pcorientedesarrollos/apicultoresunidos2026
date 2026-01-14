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
        $usuario = json_decode($postdata);
    }

    // Usar la base control:
    $sqlUseDB = $con->prepare("USE apicultorescontrol");
    $sqlUseDB->execute();
    if ($sqlUseDB == false) {
        throw new Exception($con->errorInfo());
    }

    // bajar usuarios
    session_start();
    $database = $_SESSION['database'];
    session_write_close();


    // VERIFICAR QUE NO EXISTE OTRO ACCESO CON EL MISMO USUARIO

    $sqlVerificar = $con->prepare("SELECT * FROM usuarios WHERE usuario = :usuario AND idUsuario != :idUsuario");
    $sqlVerificar->bindParam(':usuario', $usuario->usuario);
    $sqlVerificar->bindParam(':idUsuario', $usuario->idUsuario);
    $sqlVerificar->execute();
    if ($sqlVerificar == false) {
        throw new Exception($con->errorInfo());
    } else {
        $usuario_ya_existe = $sqlVerificar->fetch(PDO::FETCH_ASSOC);
    }
    if ($usuario_ya_existe == true) {
        throw new Exception('El usuario "' . $usuario->usuario . '" ya existe.');
    }

    if ($usuario->cambiarClave) {
        $update_pass = md5($usuario->nuevaPassword->password);
        $sqlUpdate = $con->prepare("UPDATE usuarios SET usuario = :usuario, password = :password, idPersonalOM = :idPersonalOM, app = :app WHERE idUsuario = :idUsuario");
        $sqlUpdate->bindParam(':usuario', $usuario->usuario);
        $sqlUpdate->bindParam(':password', $update_pass);
        $sqlUpdate->bindParam(':idPersonalOM', $usuario->idPersonalOM);
        $sqlUpdate->bindParam(':idUsuario', $usuario->idUsuario);
        $sqlUpdate->bindParam(':app', $usuario->app);
    } else {
        $sqlUpdate = $con->prepare("UPDATE usuarios SET usuario = :usuario, idPersonalOM = :idPersonalOM, app = :app WHERE idUsuario = :idUsuario");
        $sqlUpdate->bindParam(':usuario', $usuario->usuario);
        $sqlUpdate->bindParam(':idPersonalOM', $usuario->idPersonalOM);
        $sqlUpdate->bindParam(':idUsuario', $usuario->idUsuario);
        $sqlUpdate->bindParam(':app', $usuario->app);
    }

    $sqlUpdate->execute();
    if ($sqlUpdate == false) {
        throw new Exception($con->errorInfo());
    }

    // volver a cambiar base de datos
    $sqlUseDB = $con->prepare("USE $database");
    $sqlUseDB->execute();
    if ($sqlUseDB == false) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'message' => 'Se ha actualizado la información del usuario']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
