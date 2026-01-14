<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$id = $_GET['id'];

$query = "SELECT idRFC, idProveedor, archivoRFC FROM archivosrfc WHERE idProveedor =:id";
$result = $conexion->prepare($query);
$result->bindParam(':id', $id);
$result->execute();

$arrayRFC = array();

while ($row = $result->fetch()) {
    $docRFC = new stdClass();
    $docRFC->idRFC = $row["idRFC"];
    $docRFC->idProveedor = $row["idProveedor"];
    $docRFC->archivoRFC = utf8_encode($row["archivoRFC"]);
    $arrayRFC[] = $docRFC;
}

//# JSON-encode the response
echo $json_response = json_encode($arrayRFC);

?>