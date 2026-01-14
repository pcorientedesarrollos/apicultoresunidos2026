<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    $resultado = array(
        'perfil' => new stdClass(),
        'usuarios' => array(),
        'secciones' => array()
    );

    if (!isset($_GET['idPerfil'])) {
        throw new Exception('No se especificó el perfil');
    } else {
        $idPerfil = $_GET['idPerfil'];
    }

    $sqlPerfil = $con->prepare("SELECT * FROM perfiles WHERE idPerfil = :idPerfil");
    $sqlPerfil->bindParam(':idPerfil', $idPerfil);
    $sqlPerfil->execute();
    if ($sqlPerfil == false) {
        throw new Exception($con->errorInfo());
    }

    $resultado['perfil'] = $sqlPerfil->fetch(PDO::FETCH_ASSOC);

    // CAMBIAR LA BASE DE DATOS

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

    // seleccionar usuarios, IDUSUARIO, USUARIO, NOMBRE (DEL PERSONAL)
    $sqlUsuarios = $con->prepare("SELECT idUsuario, usuario, idPersonalOM
    FROM usuarios usuario
    WHERE idPerfil = :idPerfil AND usuario.oculto IS NULL AND idEmpresa = :idEmpresa");
    $sqlUsuarios->bindParam(':idPerfil', $idPerfil);
    $sqlUsuarios->bindParam(':idEmpresa', $idEmpresa);
    $sqlUsuarios->execute();

    if ($sqlUsuarios == false) {
        throw new Exception($con->errorInfo());
    }

    $listaUsuarios = $sqlUsuarios->fetchAll(PDO::FETCH_ASSOC);

    // REGRESAR A LA BASE
    $sqlUseDB = $con->prepare("USE $database");
    $sqlUseDB->execute();
    if ($sqlUseDB == false) {
        throw new Exception($con->errorInfo());
    }

    // para el personal tenemos que recorrer el arreglo de usuarios

    $arregloUsuarios = array();
    foreach ($listaUsuarios as $usuario) {
        $sqlNombrePersonal = $con->prepare("SELECT nombre
        FROM personaloaxaca
        WHERE idPersonalOM = :idPersonal");
        $sqlNombrePersonal->bindParam(':idPersonal', $usuario['idPersonalOM']);
        $sqlNombrePersonal->execute();

        if ($sqlNombrePersonal == false) {
            throw new Exception($con->errorInfo());
        }
        $resultadoPersonal = $sqlNombrePersonal->fetch(PDO::FETCH_ASSOC);
        $usuario['nombre'] = $resultadoPersonal['nombre'];

        array_push($arregloUsuarios, $usuario);
    }
    
    // asignarlos al resultado

    $resultado['usuarios'] = $arregloUsuarios;

    // Permisos:
    $sqlSecciones = $con->prepare("SELECT secc.idSeccion, secc.seccion
    FROM permisos per
    LEFT JOIN secciones secc ON per.idSeccion = secc.idSeccion
    WHERE idPerfil = :idPerfil");
    $sqlSecciones->bindParam(':idPerfil', $idPerfil);
    $sqlSecciones->execute();

    if ($sqlSecciones == false) {
        throw new Exception($con->errorInfo());
    }

    $resultado['secciones'] = $sqlSecciones->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['error' => false, 'detallePerfil' => $resultado]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
