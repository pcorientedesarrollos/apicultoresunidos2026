<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$error = "";

if (!isset($_GET['idRetencion'])) {
    echo $error = "Falta el codigo";
    die;
}

$idRetencion = $_GET['idRetencion'];

$query = "SELECT idRetencion, idProveedor, fecha, cantidad, concepto FROM retencionesisr WHERE idRetencion = :idRetencion ";
$datos = $con->prepare($query);
$datos->bindParam(':idRetencion', $idRetencion);
$datos->execute();

while ($row = $datos->fetch()) {
    $gasto = new stdClass();
    $gasto->idRetencion = $row["idRetencion"];    
    $gasto->idProveedor = $row["idProveedor"];
    $gasto->fecha = $row["fecha"];
    $gasto->cantidad = $row["cantidad"];
    $gasto->concepto = $row["concepto"];    
}

echo $json_response = json_encode($gasto);
?>