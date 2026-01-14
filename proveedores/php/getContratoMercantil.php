<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$id = $_GET['id'];
 
if (isset($_GET['pdf'])) {
    $query = "SELECT idContrato, idProveedor, archivoContrato FROM archivoscontratos WHERE idProveedor =:id AND tipo = 2";
    $result = $conexion->prepare($query);
    $result->bindParam(':id', $id);
    $result->execute();

    $arrayContratos = array();
    while ($row = $result->fetch()) {
        $docContrato = new stdClass();
        $docContrato->idContrato = $row["idContrato"];
        $docContrato->idProveedor = $row["idProveedor"];
        $docContrato->archivoContrato = $row["archivoContrato"];
        $arrayContratos[] = $docContrato;
    }
}else{
    $query = " SELECT idContrato, idProveedor, archivoContrato FROM archivoscontratos WHERE idProveedor =:id AND tipo = 1";
    $result = $conexion->prepare($query);
    $result->bindParam(':id', $id);
    $result->execute();

    $arrayContratos = array();
    while ($row = $result->fetch()) {
        $docContrato = new stdClass();
        $docContrato->idContrato = $row["idContrato"];
        $docContrato->idProveedor = $row["idProveedor"];
        $docContrato->archivoContrato = $row["archivoContrato"];
        $arrayContratos[] = $docContrato;
    }
}
//$query = " SELECT idContrato, idProveedor, archivoContrato FROM archivoscontratos WHERE idProveedor =:id";
//$result = $conexion->prepare($query);
//$result->bindParam(':id', $id);
//$result->execute();
//
//$arrayContratos = array();
//while ($row = $result->fetch()) {
//    $docContrato = new stdClass();
//    $docContrato->idContrato = $row["idContrato"];
//    $docContrato->idProveedor = $row["idProveedor"];
//    $docContrato->archivoContrato = $row["archivoContrato"];
//    $arrayContratos[] = $docContrato;
//}

//# JSON-encode the response
echo $json_response = json_encode($arrayContratos);
?>