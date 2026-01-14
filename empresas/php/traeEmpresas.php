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
    $database = $_SESSION['database'];
    session_write_close();
    $sqlSeleccionarEmpresas = $con->prepare("SELECT * FROM empresas");
    $sqlSeleccionarEmpresas->execute();
    if ($sqlSeleccionarEmpresas == false) {
        throw new Exception($con->errorInfo());
    }
    $listaDeEmpresas = $sqlSeleccionarEmpresas->fetchAll(PDO::FETCH_ASSOC);

    // volver a cambiar base de datos
    $sqlUseDB = $con->prepare("USE $database");
    $sqlUseDB->execute();
    if ($sqlUseDB == false) {
        throw new Exception($con->errorInfo());
    }

    // $informacionTabla = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['error' => false, 'empresas' => $listaDeEmpresas]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
