<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    $resultado = array(
        'usuario' => new stdClass(),
        'permisos' => array()
    );

    if (!isset($_GET['idUsuario'])) {
        throw new Exception('No se especificó el usuario');
    } else {
        $idUsuario = $_GET['idUsuario'];
    }

    // Nueva actualización: Selecciona el usuario de la base de control


    $sqlUseDB = $con->prepare("USE apicultorescontrol");
    $sqlUseDB->execute();
    if ($sqlUseDB == false) {
        throw new Exception($con->errorInfo());
    }

    // Bajar información del usuario:
    session_start();
    $idEmpresa = $_SESSION['idEmpresa'];
    $database = $_SESSION['database'];
    session_write_close();

    // aqui 
    $sqlSeleccionaUsuario = $con->prepare("SELECT us.idUsuario, us.usuario, us.idPerfil, us.idPersonalOM, us.app
    FROM usuarios us
    WHERE us.idUsuario = :idUsuario AND idEmpresa = :idEmpresa AND us.oculto IS NULL");
    $sqlSeleccionaUsuario->bindParam(':idUsuario', $idUsuario);
    $sqlSeleccionaUsuario->bindParam(':idEmpresa', $idEmpresa);
    $sqlSeleccionaUsuario->execute();

    if ($sqlSeleccionaUsuario == false) {
        throw new Exception($con->errorInfo());
    }
    $resultadoUsuario = array();
    $resultadoUsuario = $sqlSeleccionaUsuario->fetch(PDO::FETCH_ASSOC);

    // Regresar a la base del sistema
    $sqlUseDB = $con->prepare("USE $database");
    $sqlUseDB->execute();
    if ($sqlUseDB == false) {
        throw new Exception($con->errorInfo());
    }

    $perfilUsuario = array();
    $query = $con->prepare("SELECT po.nombre, per.perfil
        FROM personaloaxaca po, perfiles per
        WHERE po.idPersonalOM = :idPersonal AND per.idPerfil = :idPerfil
        GROUP BY po.idPersonalOM");
    $query->bindParam(':idPersonal', $resultadoUsuario['idPersonalOM']);
    $query->bindParam(':idPerfil', $resultadoUsuario['idPerfil']);
    $query->execute();
    if ($query == false) {
        throw new Exception($con->errorInfo());
    }
    $perfilUsuario = $query->fetch(PDO::FETCH_ASSOC);


    // Segundo parametro error no es un array
    if(!$perfilUsuario) {
        $perfilUsuario = array();
    }
    $resultado['usuario'] = array_merge($resultadoUsuario, $perfilUsuario);


    $sqlPermisos = $con->prepare("SELECT per.*, secc.seccion
    FROM permisos per
    LEFT JOIN secciones secc ON per.idSeccion = secc.idSeccion
    WHERE idPerfil = :idPerfil");
    $sqlPermisos->bindParam(':idPerfil', $resultado['usuario']['idPerfil']);
    $sqlPermisos->execute();

    if ($sqlPermisos == false) {
        throw new Exception($con->errorInfo());
    }

    $resultado['permisos'] = $sqlPermisos->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['error' => false, 'detalleUsuario' => $resultado]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
