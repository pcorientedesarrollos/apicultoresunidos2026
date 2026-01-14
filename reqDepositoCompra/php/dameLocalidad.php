<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$error = "";

if (!isset($_GET['idProveedor'])) {
    echo $error = "Falta el codigo";
    die;
}

$idProveedor = $_GET['idProveedor'];

$query = "SELECT l.localidad FROM localidades l
         LEFT JOIN direccion d ON d.idlocalidad = l.idlocalidad
         LEFT JOIN proveedor p ON p.idDireccion = d.idDireccion
         WHERE p.idProveedor = :idProveedor ";
$data = $con->prepare($query);
$data->bindParam(':idProveedor', $idProveedor);
$data->execute();

while ($row = $data->fetch()) {
    $proveeLocalidad = new stdClass();
    $proveeLocalidad->localidad = $row["localidad"];
}

echo $json_response = json_encode($proveeLocalidad);
?>