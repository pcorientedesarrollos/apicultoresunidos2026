<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo= new conePDO();
$con= $pdo->conectar();

if(isset($_GET["organica"])){
    $sql = "SELECT lab.porcentaje
    FROM laboratorio_organico lab
    INNER JOIN almacen_organico al 
    ON al.idAlmacen = lab.idAlmacen
    INNER JOIN almacenencabezado_organico ale
    ON ale.idAlmacen = al.idAlmacenEncabezado
    INNER JOIN proveedor pr 
    ON pr.idProveedor = ale.idProveedor
    GROUP BY lab.porcentaje";

    $datos =$con->prepare($sql);
    $datos->execute();
    $listaPorcentaje = Array();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        $porcentaje = new stdClass();
        $porcentaje->porcentaje = "Por rangos";
        $listaPorcentaje[] = $porcentaje;
        echo json_encode($listaPorcentaje);
    }
}else{
    $sql = "SELECT lab.porcentaje
    FROM laboratorio lab
    INNER JOIN almacen al 
    ON al.idAlmacen = lab.idAlmacen
    INNER JOIN almacenencabezado ale
    ON ale.idAlmacen = al.idAlmacenEncabezado
    INNER JOIN proveedor pr 
    ON pr.idProveedor = ale.idProveedor
    GROUP BY lab.porcentaje";
    
    $datos =$con->prepare($sql);
    $datos->execute();
    $listaPorcentaje = Array();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        $porcentaje = new stdClass();
        $porcentaje->porcentaje = "Por rangos";
        $listaPorcentaje[] = $porcentaje;
        echo json_encode($listaPorcentaje);
    }
}