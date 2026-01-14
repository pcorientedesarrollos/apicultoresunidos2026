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

$query = "SELECT idcomprador, nombre FROM compradores WHERE idcomprador = :idcomprador ";
$data = $con->prepare($query);
$data->bindParam(':idcomprador', $codigo);
$data->execute();

while ($row = $data->fetch()) {
    $zonsComp2 = new stdClass();
    $zonsComp2->idcomprador = $row["idcomprador"];
    $zonsComp2->nombre = $row["nombre"];
}

echo $json_response = json_encode($zonsComp2);
?>