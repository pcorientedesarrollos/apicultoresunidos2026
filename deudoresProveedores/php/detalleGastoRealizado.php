<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$error = "";

if (!isset($_GET['idGasto'])) {
    echo $error = "Falta el codigo";
    die;
}

$idGasto = $_GET['idGasto'];

$query = "SELECT idGasto, idProveedor, fecha, cantidad, concepto FROM gastosrealizados WHERE idGasto = :idGasto ";
$datos = $con->prepare($query);
$datos->bindParam(':idGasto', $idGasto);
$datos->execute();

while ($row = $datos->fetch()) {
    $gasto = new stdClass();
    $gasto->idGasto = $row["idGasto"];    
    $gasto->idProveedor = $row["idProveedor"];
    $gasto->fecha = $row["fecha"];
    $gasto->cantidad = $row["cantidad"];
    $gasto->concepto = $row["concepto"];    
}

echo $json_response = json_encode($gasto);
?>