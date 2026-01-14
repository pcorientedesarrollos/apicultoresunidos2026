<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if(isset($_GET["organica"])){
    $sql = "SELECT lab.hmf
    FROM laboratorio_organico lab
    INNER JOIN almacen_organico al 
    ON al.idAlmacen = lab.idAlmacen
    INNER JOIN almacenencabezado_organico ale
    ON ale.idAlmacen = al.idAlmacenEncabezado
    INNER JOIN proveedor pr 
    ON pr.idProveedor = ale.idProveedor
    GROUP BY lab.hmf";
    $datos = $con->prepare($sql);
    $datos->execute();
    $listaHmf = Array();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        $hmf0 = new stdClass();
        $hmf0->hmf = "Por rangos";
        $listaHmf[] = $hmf0;
        echo json_encode($listaHmf);
    }
}else{
    $sql = "SELECT lab.hmf
    FROM laboratorio lab
    INNER JOIN almacen al 
    ON al.idAlmacen = lab.idAlmacen
    INNER JOIN almacenencabezado ale
    ON ale.idAlmacen = al.idAlmacenEncabezado
    INNER JOIN proveedor pr 
    ON pr.idProveedor = ale.idProveedor
    GROUP BY lab.hmf";
    $datos = $con->prepare($sql);
    $datos->execute();
    $listaHmf = Array();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        $hmf0 = new stdClass();
        $hmf0->hmf = "Por rangos";
        $listaHmf[] = $hmf0;
        echo json_encode($listaHmf);
    }
}