<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    // Nueva actualización: empresas:
    // PRIMERO SELECCIONAMOS LOS PERFILES
    $query = $con->prepare("SELECT per.idPerfil, per.perfil
    FROM perfiles per
    GROUP BY per.idPerfil");
    $query->execute();
    if ($query == false) {
        throw new Exception($con->errorInfo());
    }
    $listaPerfiles = $query->fetchAll(PDO::FETCH_ASSOC);
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

    // SLEECCIONAMOS Y CONTAMOS LOS USUARIOS POR CADA PERFIL
    $informacionTabla = array();
    foreach ($listaPerfiles as $perfil) {
        $query = $con->prepare("SELECT COUNT(usua.idUsuario) as usuarios
        FROM usuarios usua
        WHERE usua.oculto IS NULL AND usua.idPerfil = :idPerfil");
        $query->bindParam(':idPerfil', $perfil['idPerfil']);
        $query->execute();
        if ($query == false) {
            throw new Exception($con->errorInfo());
        }

        // LOS ASIGNAMOS AL PERFIL
        $usuarios_perfil = $query->fetch(PDO::FETCH_ASSOC);
        $perfil['usuarios'] = $usuarios_perfil['usuarios'];
        array_push($informacionTabla, $perfil);
    }

    // REGRESAMOS A NUESTRA BASE DE DATOS
    $sqlUseDB = $con->prepare("USE $database");
    $sqlUseDB->execute();
    if ($sqlUseDB == false) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'perfiles' => $informacionTabla]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
