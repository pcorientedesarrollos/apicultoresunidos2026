<?php
include_once './DAOConeccion/conePDO.php';
$pdo = new conePDO();

$postdata = file_get_contents('php://input');

try {

    if (!$postdata) {
        throw new Exception('No se recibieron datos');
    } else {
        $postdata = json_decode($postdata);
        $usuario = $postdata->user;
        $contra = $postdata->password;
        $selectYear = $postdata->selectedYear;
    }

    if (!$selectYear) {
        throw new Exception('Imposible conectarse a la base de datos, $selectYear no está definido');
    }

    $con = $pdo->conectar($selectYear);


    // Comprobar y validar usuario

    $password = md5($contra);
    $sql = "SELECT idUsuario, idPerfil, idEmpresa FROM usuarios WHERE usuario = :usuario  AND password = :password AND idEmpresa IS NOT NULL AND idEmpresa > 0";

    $datos = $con->prepare($sql);
    $datos->bindParam(':usuario', $usuario);
    $datos->bindParam(':password', $password);
    $datos->execute();

    if ($datos == false) {
        throw new Exception($con->errorInfo());
    }

    $res = $datos->fetch(PDO::FETCH_ASSOC);

    if ($res == false) {
        // ya sea que no existe el usuario o no tiene empresa
        throw new Exception('Usuario no encontrado');
    }

    // HASTA ESTE PUNTO: USUARIO VALIDO
    // LOCALIZAR Y VALIDAR EMPRESA
    $acceso = $res;
    switch ($acceso['idEmpresa']) {
        case '1':
            $acceso['portada'] = "images/portada_aup.jpg";
            break;
        case '2':
            $acceso['portada'] = "images/portada_asas.jpg";
            break;
        case '3':
            $acceso['portada'] = "images/portada_om.jpg";
            break;
    }
    // if ($acceso["idPerfil"] == 9) {
    //     $acceso['portada'] = "images/adminis.jpg";
    // } else {
    //     $acceso['portada'] = "images/portadaom.jpg";
    // }

    // Comprobar y validar base de datos

    $sqlEmpresa = $con->prepare("SELECT e.idEmpresa, e.nombre, be.nombre as nombreBase, be.idBase, be.anio FROM empresas e
    LEFT JOIN basesempresa be on e.idEmpresa = be.IdEmpresa
    WHERE e.idEmpresa = :idEmpresa AND anio = :anio;");
    $sqlEmpresa->bindParam(':idEmpresa', $acceso['idEmpresa']);
    $sqlEmpresa->bindParam(':anio', $selectYear);
    $sqlEmpresa->execute();

    if ($sqlEmpresa == false) {
        throw new Exception($con->errorInfo());
    }

    $resultadoEmpresa = $sqlEmpresa->fetch(PDO::FETCH_ASSOC);
    if ($resultadoEmpresa == false) {
        throw new Exception('Usuario no asignado a ningunda empresa');
    }

    if (!$resultadoEmpresa['nombreBase'] || $resultadoEmpresa['nombreBase'] == ''
        || !$resultadoEmpresa['anio'] || $resultadoEmpresa['anio'] == '') {

        // Si no tiene los campos
        throw new Exception('No existe una base asignada para la empresa');
    }

    // HASTA AQUI, EMPRESA VALIDA
    // AGREGAR VALORES DE EMPRESA Y BASE AL RESULTADO
    $acceso['database'] = $resultadoEmpresa['nombreBase'];
    $acceso['tituloEmpresa'] = $resultadoEmpresa['nombre'] . " " . $resultadoEmpresa['anio'];
    // USAR LA BASE DE DATOS
    $sqlUseDb = $con->prepare("USE " . $acceso['database']);
    $sqlUseDb->execute();
    if ($sqlUseDb == false) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'acceso' => $acceso]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
