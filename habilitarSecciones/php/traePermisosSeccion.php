<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    $resultado = array(
        'seccion' => new stdClass(),
        'permisos' => array()
    );

    if (!isset($_GET['idSeccion'])) {
        throw new Exception('No se especificó la sección');
    } else {
        $idSeccion = $_GET['idSeccion'];
    }

    $sqlSeccion = $con->prepare("SELECT * FROM secciones WHERE idSeccion = :idSeccion");
    $sqlSeccion->bindParam(':idSeccion', $idSeccion);
    $sqlSeccion->execute();
    if ($sqlSeccion == false) {
        throw new Exception($con->errorInfo());
    }

    $resultado['seccion'] = $sqlSeccion->fetch(PDO::FETCH_ASSOC);

    $sqlPermisos = $con->prepare("SELECT permi.idPermiso, perfil.idPerfil, perfil.perfil    
    FROM permisos permi
    LEFT JOIN perfiles perfil ON permi.idPerfil = perfil.idPerfil
    WHERE idSeccion = :idSeccion 
    GROUP BY permi.idPermiso");
    $sqlPermisos->bindParam(':idSeccion', $idSeccion);
    $sqlPermisos->execute();

    if ($sqlPermisos == false) {
        throw new Exception($con->errorInfo());
    }
    $listaPerfiles = $sqlPermisos->fetchAll(PDO::FETCH_ASSOC);
    // NOS CAMBIAMOS DE BASE DE DATOS
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

    foreach ($listaPerfiles as $perfil) {
        $query = $con->prepare("SELECT COUNT(usua.idUsuario) as usuarios
        FROM usuarios usua
        WHERE idEmpresa = :idEmpresa AND usua.oculto IS NULL AND usua.idPerfil = :idPerfil");
        $query->bindParam(':idEmpresa', $idEmpresa);
        $query->bindParam(':idPerfil', $perfil['idPerfil']);
        $query->execute();
        if ($query == false) {
            throw new Exception($con->errorInfo());
        }
        // LOS ASIGNAMOS AL PERFIL
        $usuarios_perfil = $query->fetch(PDO::FETCH_ASSOC);
        $perfil['usuarios'] = $usuarios_perfil['usuarios'];
        array_push($resultado['permisos'], $perfil);
    }

  // REGRESAMOS A NUESTRA BASE DE DATOS
    $sqlUseDB = $con->prepare("USE $database");
    $sqlUseDB->execute();
    if ($sqlUseDB == false) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'resultado' => $resultado]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
