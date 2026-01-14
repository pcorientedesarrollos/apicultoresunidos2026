<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$error = "";

if (!isset($_GET['idcomprador'])) {
    echo $error = "Falta el codigo";
    die;
}

$codigo = $_GET['idcomprador'];

$query = "SELECT idcomprador, nombre, telefono  FROM compradores WHERE idcomprador = :idcomprador ";
$data = $con->prepare($query);
$data->bindParam(':idcomprador', $codigo);
$data->execute();

while ($row = $data->fetch()) {
    $compra = new stdClass();
    $compra->idcomprador = $row["idcomprador"];
    $compra->nombre = $row["nombre"];
    $compra->telefono = $row["telefono"];
}

echo $json_response = json_encode($compra);
