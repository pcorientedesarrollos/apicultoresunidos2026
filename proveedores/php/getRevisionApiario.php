<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$id = $_GET['id'];

if (isset($_GET['pdf'])) {
    $query = " SELECT idApiario, idProveedor, archivoApiario FROM archivosapiarios WHERE idProveedor =:id AND tipo = 2";
    $result = $conexion->prepare($query);
    $result->bindParam(':id', $id);
    $result->execute();

    $arrayApiarios = array();

    while ($row = $result->fetch()) {
        $docApiario = new stdClass();
        $docApiario->idApiario = $row["idApiario"];
        $docApiario->idProveedor = $row["idProveedor"];
        $docApiario->archivoApiario = utf8_encode($row["archivoApiario"]);
        $arrayApiarios[] = $docApiario;
    }
} else {
    $query = " SELECT idApiario, idProveedor, archivoApiario FROM archivosapiarios WHERE idProveedor =:id AND tipo = 1";
    $result = $conexion->prepare($query);
    $result->bindParam(':id', $id);
    $result->execute();

    $arrayApiarios = array();

    while ($row = $result->fetch()) {
        $docApiario = new stdClass();
        $docApiario->idApiario = $row["idApiario"];
        $docApiario->idProveedor = $row["idProveedor"];
        $docApiario->archivoApiario = utf8_encode($row["archivoApiario"]);
        $arrayApiarios[] = $docApiario;
    }
}


//# JSON-encode the response
echo $json_response = json_encode($arrayApiarios);
?>