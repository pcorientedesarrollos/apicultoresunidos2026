<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$id = $_GET['id'];
 
if (isset($_GET['pdf'])) {
    $query = "SELECT idPagare, idProveedor, archivoPagare FROM archivospagares WHERE idProveedor =:id AND tipo = 2";
    $result = $conexion->prepare($query);
    $result->bindParam(':id', $id);
    $result->execute();

    $arrayContratos = array();
    while ($row = $result->fetch()) {
        $docContrato = new stdClass();
        $docContrato->idPagare = $row["idPagare"];
        $docContrato->idProveedor = $row["idProveedor"];
        $docContrato->archivoPagare = $row["archivoPagare"];
        $arrayContratos[] = $docContrato;
    }
}else{
    $query = " SELECT idPagare, idProveedor, archivoPagare FROM archivospagares WHERE idProveedor =:id AND tipo = 1";
    $result = $conexion->prepare($query);
    $result->bindParam(':id', $id);
    $result->execute();

    $arrayContratos = array();
    while ($row = $result->fetch()) {
        $docContrato = new stdClass();
        $docContrato->idPagare = $row["idPagare"];
        $docContrato->idProveedor = $row["idProveedor"];
        $docContrato->archivoPagare = $row["archivoPagare"];
        $arrayContratos[] = $docContrato;
    }
}

echo $json_response = json_encode($arrayContratos);
?>