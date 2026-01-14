<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

// Guarda los datos de la empresa 
$nuevaEmpresa = file_get_contents('php://input');

try {
    if (!$nuevaEmpresa) {
        throw new Exception('No se recibieron datos');
    } else {
        $nuevaEmpresa = json_decode($nuevaEmpresa);
    }   

    // Usar la base control:
    $sqlUseDB = $con->prepare("USE apicultorescontrol");
    $sqlUseDB->execute();
    if ($sqlUseDB == false) {
        throw new Exception($con->errorInfo());
    }
        
    // bajar variables de la sesion
    session_start();
    $database = $_SESSION['database'];
    session_write_close();

    // SI NO EXISTE, REGISTRARLO EN LA BASE

    if (isset($nuevaEmpresa->idEmpresa)) {
        $sqlInsert = $con->prepare("UPDATE empresas SET nombre = :nombre WHERE idEmpresa = :idEmpresa");
        $sqlInsert->bindParam(':nombre', $nuevaEmpresa->nombre);
        $sqlInsert->bindParam(':idEmpresa', $nuevaEmpresa->idEmpresa);
        $sqlInsert->execute();
        if ($sqlInsert == false) {
            throw new Exception($con->errorInfo());
        }
    } else {
        $sqlInsert = $con->prepare("INSERT INTO empresas (nombre) VALUES (:nombre)");
        $sqlInsert->bindParam(':nombre', $nuevaEmpresa->nombre);
        $sqlInsert->execute();
        if ($sqlInsert == false) {
            throw new Exception($con->errorInfo());
        }
    }

    // volver a cambiar base de datos
    $sqlUseDB = $con->prepare("USE $database");
    $sqlUseDB->execute();
    if ($sqlUseDB == false) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'message' => 'Se ha registrado una nueva empresa']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
