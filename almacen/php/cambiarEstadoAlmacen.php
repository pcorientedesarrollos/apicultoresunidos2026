<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$valor = $_GET["valor"];
$id = $_GET["id"];

if(isset($_GET["organica"])){
    $sql = "UPDATE almacen_organico set autorizado = :valor WHERE idAlmacen = :id";
    $datos = $con->prepare($sql);
    $datos->bindParam(':valor', $valor);
    $datos->bindParam(':id', $id);
    $datos->execute();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        echo "Estado cambiado satisfactoriamente";
    }
}else{
    $sql = "UPDATE almacen set autorizado = :valor WHERE idAlmacen = :id";
    $datos = $con->prepare($sql);
    $datos->bindParam(':valor', $valor);
    $datos->bindParam(':id', $id);
    $datos->execute();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        echo "Estado cambiado satisfactoriamente";
    }
}