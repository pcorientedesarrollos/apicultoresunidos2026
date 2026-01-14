<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$id = $_GET['id'];

if (isset($_GET['pdf'])) {
    $query = "SELECT idIne, idProveedor, archivoINE, tipo FROM archivosine WHERE idProveedor =:id AND tipo = 2";
    $result = $conexion->prepare($query);
    $result->bindParam(':id', $id);
    $result->execute();

    $arrayRFC = array();

    while ($row = $result->fetch()) {
        $docRFC = new stdClass();
        $docRFC->idIne = $row["idIne"];
        $docRFC->idProveedor = $row["idProveedor"];
        $docRFC->archivoINE = $row["archivoINE"];
        $docRFC->tipo = $row["tipo"];
        $arrayRFC[] = $docRFC;
    }
} else {
    $query = "SELECT idIne, idProveedor, archivoINE, tipo FROM archivosine WHERE idProveedor =:id AND tipo = 1";
    $result = $conexion->prepare($query);
    $result->bindParam(':id', $id);
    $result->execute();

    $arrayRFC = array();

    while ($row = $result->fetch()) {
        $docRFC = new stdClass();
        $docRFC->idIne = $row["idIne"];
        $docRFC->idProveedor = $row["idProveedor"];
        $docRFC->archivoINE = $row["archivoINE"];
        $docRFC->tipo = $row["tipo"];
        $arrayRFC[] = $docRFC;
    }
}



//# JSON-encode the response
echo $json_response = json_encode($arrayRFC);
?>