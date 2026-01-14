<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$id = $_GET['id'];

$query = "SELECT idSagarpaId, idProveedor, archivoSagarpa FROM archivossagarpa WHERE idProveedor =:id";
$result = $conexion->prepare($query);
$result->bindParam(':id', $id);
$result->execute();

$arrayIdSagarpa = array();

while ($row = $result->fetch()) {
    $docIdSagarpa = new stdClass();
    $docIdSagarpa->idSagarpaId = $row["idSagarpaId"];
    $docIdSagarpa->idProveedor = $row["idProveedor"];
    $docIdSagarpa->archivoSagarpa = utf8_encode($row["archivoSagarpa"]);
    $arrayIdSagarpa[] = $docIdSagarpa;
}

//# JSON-encode the response
echo $json_response = json_encode($arrayIdSagarpa);

?>