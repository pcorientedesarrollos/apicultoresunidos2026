<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo= new conePDO();
$con= $pdo->conectar();

if(isset($_GET["organica"])){
    $sql = "SELECT lab.stDescripcion
    FROM laboratorio_organico lab
    INNER JOIN almacen_organico al 
    ON al.idAlmacen = lab.idAlmacen
    INNER JOIN almacenencabezado_organico ale
    ON ale.idAlmacen = al.idAlmacenEncabezado
    INNER JOIN proveedor pr 
    ON pr.idProveedor = ale.idProveedor
    GROUP BY lab.stDescripcion";
    
    $datos=$con->prepare($sql);
    $datos->execute();
    
    $listaSt = Array();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $st = new stdClass();
            $st->st = $rs["stDescripcion"];
            $listaSt[] = $st;
        }
        echo json_encode($listaSt);
    }
}else{
    $sql = "SELECT lab.stDescripcion
    FROM laboratorio lab
    INNER JOIN almacen al 
    ON al.idAlmacen = lab.idAlmacen
    INNER JOIN almacenencabezado ale
    ON ale.idAlmacen = al.idAlmacenEncabezado
    INNER JOIN proveedor pr 
    ON pr.idProveedor = ale.idProveedor
    GROUP BY lab.stDescripcion";
    
    $datos=$con->prepare($sql);
    $datos->execute();
    
    $listaSt = Array();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $st = new stdClass();
            $st->st = $rs["stDescripcion"];
            $listaSt[] = $st;
        }
        echo json_encode($listaSt);
    }
}