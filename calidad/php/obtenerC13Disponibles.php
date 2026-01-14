<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo= new conePDO();
$con= $pdo->conectar();

if(isset($_GET["organica"])){
    $sql = "SELECT lab.adulteracionDescripcion
    FROM laboratorio_organico lab
    INNER JOIN almacen_organico al 
    ON al.idAlmacen = lab.idAlmacen
    INNER JOIN almacenencabezado_organico ale
    ON ale.idAlmacen = al.idAlmacenEncabezado
    INNER JOIN proveedor pr 
    on pr.idProveedor = ale.idProveedor
    GROUP BY lab.adulteracionDescripcion";
    //$datos = mysql_query($sql);
    
    $datos = $con->prepare($sql);
    $datos->execute();
    
    $listaC13 = Array();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $c13 = new stdClass();
            $c13->adulteracionDescripcion = $rs["adulteracionDescripcion"];
            $listaC13[] = $c13;
        }
        echo json_encode($listaC13);
    }
}else{
    $sql = "SELECT lab.adulteracionDescripcion
    FROM laboratorio lab
    INNER JOIN almacen al 
    ON al.idAlmacen = lab.idAlmacen
    INNER JOIN almacenencabezado ale
    ON ale.idAlmacen = al.idAlmacenEncabezado
    INNER JOIN proveedor pr 
    on pr.idProveedor = ale.idProveedor
    GROUP BY lab.adulteracionDescripcion";
    //$datos = mysql_query($sql);
    
    $datos = $con->prepare($sql);
    $datos->execute();
    
    $listaC13 = Array();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $c13 = new stdClass();
            $c13->adulteracionDescripcion = $rs["adulteracionDescripcion"];
            $listaC13[] = $c13;
        }
        echo json_encode($listaC13);
    }
}