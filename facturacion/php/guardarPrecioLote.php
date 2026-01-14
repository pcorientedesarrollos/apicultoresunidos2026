<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$post = file_get_contents('php://input');

try {
    if (!$post) {
        throw new Exception('No se recibieron datos');
    } else {
        $precio = json_decode($post);
    }

    $success_message = 'Se ha editado el registro';
    $sqlInsert = $con->prepare("UPDATE listadepesos SET precio = :precio, condicionPago = :condicionPago, tiempoPago = :tiempoPago, importe = :importe, fechaFactura = :fechaFactura WHERE idTamborPeso = :idTamborPeso");
    $sqlInsert->bindParam(':idTamborPeso', $precio->idTamborPeso);
    $sqlInsert->bindParam(':precio', $precio->precio); 
    $sqlInsert->bindParam(':condicionPago', $precio->condicionPago);           
    $sqlInsert->bindParam(':tiempoPago', $precio->tiempoPago);           
    $sqlInsert->bindParam(':importe', $precio->importe);   
    $sqlInsert->bindParam(':fechaFactura', $precio->fechaFactura);           
    $sqlInsert->execute();

    if ($sqlInsert == false) {
        throw new Exception($con->errorInfo());
    }
    echo json_encode(['error' => false, 'message' => $success_message]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
