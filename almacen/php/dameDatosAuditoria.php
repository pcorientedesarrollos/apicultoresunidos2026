<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function datosAuditoria($idAuditoria)
{
    global $con;
    $sqlDatos = $con->prepare("SELECT aud.fecha, aud.finalizado, aud.usuario, 
    CASE aud.estado WHEN 1 THEN 'Activo' WHEN 2 THEN 'Finalizado' WHEN -1 THEN 'Cancelado' 
    WHEN 0 THEN 'Pendiente' END AS estado 
    FROM auditorias aud WHERE aud.idAuditoria = :idAuditoria");
    $sqlDatos->bindParam(':idAuditoria', $idAuditoria);
    $sqlDatos->execute();

    if ($sqlDatos == false) {
        throw new Exception($con->errorInfo());
    }

    $resultado = $sqlDatos->fetch(PDO::FETCH_ASSOC);

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

    // SELECCIONAR AL USUARIO DE LA BASE DE CONTROL

    $sqlSeleccionaUsuario = $con->prepare("SELECT usuario FROM usuarios WHERE idUsuario = :idUsuario");
    $sqlSeleccionaUsuario->bindParam(':idUsuario', $resultado['usuario']);
    $sqlSeleccionaUsuario->execute();

    if ($sqlSeleccionaUsuario == false) {
        throw new Exception($con->errorInfo());
    }
    $resultadoUsuario = $sqlSeleccionaUsuario->fetch(PDO::FETCH_ASSOC);
    $resultado['usuario'] = $resultadoUsuario['usuario'];

    // volver a cambiar base de datos
    $sqlUseDB = $con->prepare("USE $database");
    $sqlUseDB->execute();
    if ($sqlUseDB == false) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
}
function datosZona($idZona)
{
    global $con;
    $sqlDatos = $con->prepare("SELECT zt.nombre, tdm.tipoDeMiel FROM zonastambores zt
    LEFT JOIN tiposdemiel tdm ON zt.tipoMiel = tdm.idTipoDeMiel
    WHERE idZonaTambor = :idZona");
    $sqlDatos->bindParam(':idZona', $idZona);
    $sqlDatos->execute();

    if ($sqlDatos == false) {
        throw new Exception($con->errorInfo());
    }

    $resultado = $sqlDatos->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
}

try {

    if (isset($_GET['idAuditoria'])) {
        datosAuditoria($_GET['idAuditoria']);
    } else if (isset($_GET['idZona'])) {
        datosZona($_GET['idZona']);
    } else {
        throw new Exception('No se recibieron parámetros');
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => []]);
    exit();
}
