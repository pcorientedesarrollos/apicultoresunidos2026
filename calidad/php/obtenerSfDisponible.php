<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if(isset($_GET["organica"])){
    $sql = "SELECT lab.sfDescripcion
    FROM laboratorio_organico lab
    INNER JOIN almacen_organico al 
    ON al.idAlmacen = lab.idAlmacen
    INNER JOIN almacenencabezado_organico ale
    ON ale.idAlmacen = al.idAlmacenEncabezado
    INNER JOIN proveedor pr 
    ON pr.idProveedor = ale.idProveedor
    GROUP BY lab.sfDescripcion";
    
    $datos = $con->prepare($sql);
    $datos->execute();
    
    $listaSf = Array();
    
    if ($datos == false) {
            echo "Error al ingresar";
    } else {
        while ($rs = $datos->fetch()) {
            $sf = new stdClass();
            $sf->sf = $rs["sfDescripcion"];
            $listaSf[] = $sf;
        }
        echo json_encode($listaSf);
    }
}else{
    $sql = "SELECT lab.sfDescripcion
    FROM laboratorio lab
    INNER JOIN almacen al 
    ON al.idAlmacen = lab.idAlmacen
    INNER JOIN almacenencabezado ale
    ON ale.idAlmacen = al.idAlmacenEncabezado
    INNER JOIN proveedor pr 
    ON pr.idProveedor = ale.idProveedor
    GROUP BY lab.sfDescripcion";
    
    $datos = $con->prepare($sql);
    $datos->execute();
    
    $listaSf = Array();
    
    if ($datos == false) {
            echo "Error al ingresar";
    } else {
        while ($rs = $datos->fetch()) {
            $sf = new stdClass();
            $sf->sf = $rs["sfDescripcion"];
            $listaSf[] = $sf;
        }
        echo json_encode($listaSf);
    }   
}