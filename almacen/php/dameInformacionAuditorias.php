<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {

    $sql = "SELECT a.idAuditoria, a.fecha, a.estado, a.usuario FROM auditorias a
            ORDER BY a.fecha DESC";
    $datos = $con->prepare($sql);
    $datos->execute();
    if ($datos == false) {
        throw new Exception($con->errorInfo());
    }

    $listaAuditorias = $datos->fetchAll(PDO::FETCH_ASSOC);

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


    // Por cada auditoria, selecciona el usuario
    $arregloFinal = array();
    foreach ($listaAuditorias as $auditoria) {
        $sqlSeleccionaUsuario = $con->prepare("SELECT usuario FROM usuarios WHERE idUsuario = :idUsuario");
        $sqlSeleccionaUsuario->bindParam(':idUsuario', $auditoria['usuario']);
        $sqlSeleccionaUsuario->execute();

        if ($sqlSeleccionaUsuario == false) {
            throw new Exception($con->errorInfo());
        }
        $resultadoUsuario = $sqlSeleccionaUsuario->fetch(PDO::FETCH_ASSOC);
        $auditoria['usuario'] = $resultadoUsuario['usuario'];
        array_push($arregloFinal, $auditoria);
    }


    // volver a cambiar base de datos
    $sqlUseDB = $con->prepare("USE $database");
    $sqlUseDB->execute();
    if ($sqlUseDB == false) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $arregloFinal]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => []]);
    exit();
}
