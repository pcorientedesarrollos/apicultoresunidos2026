<?php

include_once '../../DAOConeccion/conePDO.php';
include_once '../../mensajes/Mensajes.php';
$pdo = new conePDO();
$msg = new Mensajes();
$con = $pdo->conectar();

$id = $_GET["id"];
$json = file_get_contents("php://input");
$datos = json_decode($json);
$info = $datos->valor;

$sql = "UPDATE almacen SET zona = :zona, pesoLista = :pesoLista, bruto = :bruto, tara = :tara, neto = :neto, diferencia = :diferencia, humedad = :humedad WHERE almacen.idAlmacen = :id";
$datos->bindParam(':zona', $info->zona);
$datos->bindParam(':pesoLista', $info->pesoLista);
$datos->bindParam(':bruto', $info->bruto);
$datos->bindParam(':tara', $info->tara);
$datos->bindParam(':neto', $info->neto);
$datos->bindParam(':diferencia', $info->diferencia);
$datos->bindParam(':humedad', $info->humedad);
$datos->bindParam(':id', $id);
$datos->execute();
if ($datos == false) {
    throw new Exception($con->errorInfo());
} else {
    echo json_encode($msg->succes("Almacen eliminado satisfactoriamente"));
}