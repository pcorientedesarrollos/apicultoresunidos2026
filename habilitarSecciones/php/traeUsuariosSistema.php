<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    // Nueva actualización:
    // Obtener los usuarios de la empresa que tenga iniciada sesión

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
    $sqlSeleccionarUsuarios = $con->prepare("SELECT us.idUsuario, us.usuario, us.idPerfil, us.app, us.idPersonalOM
    FROM usuarios us WHERE us.idEmpresa = :idEmpresa AND us.oculto IS NULL ORDER BY usuario");
    $sqlSeleccionarUsuarios->bindParam(':idEmpresa', $idEmpresa);
    $sqlSeleccionarUsuarios->execute();
    if ($sqlSeleccionarUsuarios == false) {
        throw new Exception($con->errorInfo());
    }
    $listaDeUsuarios = $sqlSeleccionarUsuarios->fetchAll(PDO::FETCH_ASSOC);

    // volver a cambiar base de datos
    $sqlUseDB = $con->prepare("USE $database");
    $sqlUseDB->execute();
    if ($sqlUseDB == false) {
        throw new Exception($con->errorInfo());
    }

    // Ahora por cada usuario, buscamos su nombre y perfil
    $informacionTabla = array();
    foreach ($listaDeUsuarios as $usuario) {
        $query = $con->prepare("SELECT po.nombre, per.perfil
        FROM personaloaxaca po, perfiles per
        WHERE po.idPersonalOM = :idPersonal AND per.idPerfil = :idPerfil
        GROUP BY po.idPersonalOM");
        $query->bindParam(':idPersonal', $usuario['idPersonalOM']);
        $query->bindParam(':idPerfil', $usuario['idPerfil']);
        $query->execute();
        if ($query == false) {
            throw new Exception($con->errorInfo());
        }
        $resultadoUsuario = $query->fetch(PDO::FETCH_ASSOC);
        $usuario['nombre'] = $resultadoUsuario['nombre'];
        $usuario['perfil'] = $resultadoUsuario['perfil'];
        array_push($informacionTabla, $usuario);
    }

    // $informacionTabla = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['error' => false, 'usuarios' => $informacionTabla]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
