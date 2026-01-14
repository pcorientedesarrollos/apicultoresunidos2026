<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
date_default_timezone_set('America/Merida');
$fecha = date('Y-m-d');
$miInfo = file_get_contents("php://input");
$info = json_decode($miInfo);

if (isset($_GET['idClienteExportador'])) {
    $sqlUp = "UPDATE clientesexportadores SET datosCliente = :datosCliente, datosDestino = :datosDestino WHERE idClienteExportador = :idClienteExportador";
    $datos = $con->prepare($sqlUp);
    $datos->bindParam(':datosCliente', $info->datosCliente);
    $datos->bindParam(':datosDestino', $info->datosDestino);
    $datos->bindParam(':idClienteExportador', $_GET['idClienteExportador']);
    $datos->execute();
    if ($datos == false) {
        echo 'Hubo un error';
    } else {
        echo 'Se modificó';
    }
} else {
    $sql = "INSERT INTO clientesexportadores (datosCliente, datosDestino, fechaAlta) VALUES (:datosCliente, :datosDestino, :fecha)";
    $dato = $con->prepare($sql);
    $dato->bindParam(':datosCliente', $info->datosCliente);
    $dato->bindParam(':datosDestino', $info->datosDestino);
    $dato->bindParam(':fecha', $fecha);
    $dato->execute();
}
?>