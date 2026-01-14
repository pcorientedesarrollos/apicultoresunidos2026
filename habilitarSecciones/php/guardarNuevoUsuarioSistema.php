<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

// Guarda los datos del usuario del sistema incluyendo la clave de acceso
$nuevoUsuario = file_get_contents('php://input');

try {
    if (!$nuevoUsuario) {
        throw new Exception('No se recibieron datos');
    } else {
        $nuevoUsuario = json_decode($nuevoUsuario);
    }   

    // Usar la base control:
    $sqlUseDB = $con->prepare("USE apicultorescontrol");
    $sqlUseDB->execute();
    if ($sqlUseDB == false) {
        throw new Exception($con->errorInfo());
    }
        
    // bajar variables de la sesion
    session_start();
    $idEmpresa = $_SESSION['idEmpresa'];
    $database = $_SESSION['database'];
    session_write_close();

    // VERIFICAR QUE NO EXISTE OTRO ACCESO CON EL MISMO USUARIO

    $sqlVerificar = $con->prepare("SELECT idUsuario FROM usuarios WHERE usuario = :usuario");
    $sqlVerificar->bindParam(':usuario', $nuevoUsuario->usuario);
    $sqlVerificar->execute();
    if ($sqlVerificar == false) {
        throw new Exception($con->errorInfo());
    } else {
        $usuario_ya_existe = $sqlVerificar->fetch(PDO::FETCH_ASSOC);
    }
    if ($usuario_ya_existe == true) {
        throw new Exception('El usuario "' . $nuevoUsuario->usuario . '" ya está registrado.');
    }

    // Verificar clave

    if (!$nuevoUsuario->password || !$nuevoUsuario->passwordConfirm) {
        throw new Exception('Se requiere la contraseña y la confirmación');
    } else {
        if ($nuevoUsuario->password != $nuevoUsuario->passwordConfirm) {
            throw new Exception('La contraseña no coincide con la confirmación');
        }
    }

    $nuevoUsuario->password = md5($nuevoUsuario->password);

    // SI NO EXISTE, REGISTRARLO EN LA BASE

    $sqlInsert = $con->prepare("INSERT INTO usuarios (usuario, password, idPersonalOM, idPerfil, app, idEmpresa) VALUES (:usuario, :password, :idPersonalOM, :idPerfil, :app, :idEmpresa)");
    $sqlInsert->bindParam(':usuario', $nuevoUsuario->usuario);
    $sqlInsert->bindParam(':password', $nuevoUsuario->password);
    $sqlInsert->bindParam(':idPersonalOM', $nuevoUsuario->idPersonalOM);
    $sqlInsert->bindParam(':idPerfil', $nuevoUsuario->idPerfil);
    $sqlInsert->bindParam(':app', $nuevoUsuario->app);
    $sqlInsert->bindParam(':idEmpresa', $idEmpresa);
    $sqlInsert->execute();
    if ($sqlInsert == false) {
        throw new Exception($con->errorInfo());
    }

    // volver a cambiar base de datos
    $sqlUseDB = $con->prepare("USE $database");
    $sqlUseDB->execute();
    if ($sqlUseDB == false) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'message' => 'Se ha registrado un nuevo usuario al sistema']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
