<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$error = "";

if (!isset($_GET['idlocalidad'])) {
    echo $error = "Falta el codigo";
    die;
}

$codigo = $_GET['idlocalidad'];

$query = "SELECT idlocalidad, localidad, idzona  FROM localidades WHERE idlocalidad = :idlocalidad ";
$data = $con->prepare($query);
$data->bindParam(':idlocalidad', $codigo);
$data->execute();

while ($row = $data->fetch()) {
    $ciudades = new stdClass();
    $ciudades->idlocalidad = $row["idlocalidad"];
    $ciudades->localidad = $row["localidad"];
    $ciudades->idzona = $row["idzona"];
}

echo $json_response = json_encode($ciudades);
