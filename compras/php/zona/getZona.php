<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$error = "";

if (!isset($_GET['idzona'])) {
    echo $error = "Falta el codigo";
    die;
}

$codigo = $_GET['idzona'];

$query = "SELECT idzona, zona, idcomprador  FROM zonas WHERE idzona = :idzona ";
$data = $con->prepare($query);
$data->bindParam(':idzona', $codigo);
$data->execute();

while ($row = $data->fetch()) {
    $zonaComp = new stdClass();
    $zonaComp->idzona = $row["idzona"];
    $zonaComp->zona = $row["zona"];
    $zonaComp->idcomprador = $row["idcomprador"];
}

echo $json_response = json_encode($zonaComp);
?>