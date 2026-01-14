<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if(isset($_GET["organica"])){
    $sql = "SELECT l.localidad, l.idlocalidad
    FROM localidades l
    INNER JOIN direccion d  
    ON d.idLocalidad = l.idLocalidad
    INNER JOIN proveedor p 
    ON p.idDireccion = d.idDireccion
    INNER JOIN almacenencabezado_organico al
    ON al.idProveedor = p.idProveedor
    INNER JOIN laboratorio_organico lab ON lab.entradaNo = al.idAlmacen
    GROUP BY l.idlocalidad
    ORDER BY l.localidad";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    $listaLocalidad = Array();
    if ($datos == false) {
        echo "Error al ingresar";
    } else {
        while ($rs = $datos->fetch()) {
            $localidades = new stdClass();
            $localidades->idlocalidad = $rs["idlocalidad"];
            $localidades->localidad = $rs["localidad"];
            $listaLocalidad[] = $localidades;
        }
        echo json_encode($listaLocalidad);
    }
}else{
    $sql = "SELECT l.localidad, l.idlocalidad
    FROM localidades l
    INNER JOIN direccion d  
    ON d.idLocalidad = l.idLocalidad
    INNER JOIN proveedor p 
    ON p.idDireccion = d.idDireccion
    INNER JOIN almacenencabezado al
    ON al.idProveedor = p.idProveedor
    INNER JOIN laboratorio lab ON lab.entradaNo = al.idAlmacen
    GROUP BY l.idlocalidad
    ORDER BY l.localidad";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    $listaLocalidad = Array();
    if ($datos == false) {
        echo "Error al ingresar";
    } else {
        while ($rs = $datos->fetch()) {
            $localidades = new stdClass();
            $localidades->idlocalidad = $rs["idlocalidad"];
            $localidades->localidad = $rs["localidad"];
            $listaLocalidad[] = $localidades;
        }
        echo json_encode($listaLocalidad);
    }
}
?>