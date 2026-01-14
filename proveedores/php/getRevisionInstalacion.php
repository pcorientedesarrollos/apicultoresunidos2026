<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$id = $_GET['id'];

if (isset($_GET['pdf'])) {
    $query = " SELECT idInstalacion, idProveedor, archivoInstalacion FROM archivosinstalaciones WHERE idProveedor =:id AND tipo = 2";
    $result = $conexion->prepare($query);
    $result->bindParam(':id', $id);
    $result->execute();

    $arrayInsta = array();

    while ($row = $result->fetch()) {
        $docInstalacion = new stdClass();
        $docInstalacion->idInstalacion = $row["idInstalacion"];
        $docInstalacion->idProveedor = $row["idProveedor"];
        $docInstalacion->archivoInstalacion = utf8_encode($row["archivoInstalacion"]);
        $arrayInsta[] = $docInstalacion;
    }
} else {
    $query = " SELECT idInstalacion, idProveedor, archivoInstalacion FROM archivosinstalaciones WHERE idProveedor =:id AND tipo = 1";
    $result = $conexion->prepare($query);
    $result->bindParam(':id', $id);
    $result->execute();

    $arrayInsta = array();

    while ($row = $result->fetch()) {
        $docInstalacion = new stdClass();
        $docInstalacion->idInstalacion = $row["idInstalacion"];
        $docInstalacion->idProveedor = $row["idProveedor"];
        $docInstalacion->archivoInstalacion = utf8_encode($row["archivoInstalacion"]);
        $arrayInsta[] = $docInstalacion;
    }
}

//# JSON-encode the response
echo $json_response = json_encode($arrayInsta);
?>