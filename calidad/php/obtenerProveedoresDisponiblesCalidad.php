<?php

//include_once '../../DAOConeccion/coneccion.php';
//$cn = new Coneccion();
//$cn->Conectarse();

include_once '../../DAOConeccion/conePDO.php';
$pdo= new conePDO();
$con= $pdo->conectar();

if(isset($_GET["organica"])){
    $sql = "SELECT pr.idProveedor, pr.nombre
    FROM laboratorio_organico lab
    INNER JOIN almacen_organico al 
    on al.idAlmacen = lab.idAlmacen
    INNER JOIN almacenencabezado_organico ale
    on ale.idAlmacen = al.idAlmacenEncabezado
    INNER JOIN proveedor_organico pr 
    on pr.idProveedor = ale.idProveedor
    group by pr.idProveedor";
    //$datos = mysql_query($sql);
    $datos = $con->prepare($sql);
    $datos->execute();
    
    $listaProveedor = Array();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $proveedor = new stdClass();
            $proveedor->idProveedor = $rs["idProveedor"];
            $proveedor->nombre = $rs["nombre"];
            $listaProveedor[] = $proveedor;
        }
        echo json_encode($listaProveedor);
    }
}else{
    $sql = "SELECT pr.idProveedor, pr.nombre
    FROM laboratorio lab
    INNER JOIN almacen al 
    on al.idAlmacen = lab.idAlmacen
    INNER JOIN almacenencabezado ale
    on ale.idAlmacen = al.idAlmacenEncabezado
    INNER JOIN proveedor pr 
    on pr.idProveedor = ale.idProveedor
    group by pr.idProveedor";
    //$datos = mysql_query($sql);
    $datos = $con->prepare($sql);
    $datos->execute();
    
    $listaProveedor = Array();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $proveedor = new stdClass();
            $proveedor->idProveedor = $rs["idProveedor"];
            $proveedor->nombre = $rs["nombre"];
            $listaProveedor[] = $proveedor;
        }
        echo json_encode($listaProveedor);
    }
}