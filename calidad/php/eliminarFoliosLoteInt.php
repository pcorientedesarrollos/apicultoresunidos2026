<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idAlmacen = $_GET["idAlmacen"];

if(isset($_GET["organica"])){
    $sqlAl = "UPDATE almacen_organico set estado = '0' WHERE idAlmacen = :idAlmacen";
    $datTA = $con->prepare($sqlAl);
    $datTA->bindParam(':idAlmacen', $idAlmacen);
    $datTA->execute();
}else{
    $sqlAl = "UPDATE almacen set estado = '0' WHERE idAlmacen = :idAlmacen";
    $datTA = $con->prepare($sqlAl);
    $datTA->bindParam(':idAlmacen', $idAlmacen);
    $datTA->execute();
}