<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);

if (isset($_GET['update'])) {

    $sql = "UPDATE gastosrealizados SET idProveedor = :idProveedor, fecha = :fecha, idMes = :idMes, cantidad = :cantidad, concepto = :concepto WHERE idGasto = :idGasto";
    $dats = $con->prepare($sql);
    $dats->bindParam(':idProveedor', $datos->idProveedor);
    $dats->bindParam(':fecha', $datos->fecha);
    $dats->bindParam(':idMes', $datos->idMes);
    $dats->bindParam(':cantidad', $datos->cantidad);
    $dats->bindParam(':idGasto', $datos->idGasto); 
    $dats->bindParam(':concepto', $datos->concepto);        
    $dats->execute();
    if ($dats == false) {
        echo 'Error al ingresar';
    } else {
        echo 'Se agregó exitosamente';
    }

} else {

    $sql = "INSERT INTO gastosrealizados (idProveedor, fecha, idMes, cantidad, concepto) VALUES (:idProveedor, :fecha, :idMes, :cantidad, :concepto)";
    $dats = $con->prepare($sql);
    $dats->bindParam(':idProveedor', $datos->idProveedor);
    $dats->bindParam(':fecha', $datos->fecha);
    $dats->bindParam(':idMes', $datos->idMes);
    $dats->bindParam(':cantidad', $datos->cantidad);
    $dats->bindParam(':concepto', $datos->concepto);    
    $dats->execute();
    if ($dats == false) {
        echo 'Error al ingresar';
    } else {
        echo 'Se agregó exitosamente';
    }

}