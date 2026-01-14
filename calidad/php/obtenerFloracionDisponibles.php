<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = "SELECT lab.idFloracion, f.floracion, f.idFloracion
FROM laboratorio lab
INNER JOIN almacen al 
ON al.idAlmacen = lab.idAlmacen
INNER JOIN almacenencabezado ale
ON ale.idAlmacen = al.idAlmacenEncabezado
INNER JOIN proveedor pr 
ON pr.idProveedor = ale.idProveedor
INNER JOIN floraciones f ON f.idFloracion = lab.idFloracion
GROUP BY lab.idFloracion";
$datos = $con->prepare($sql);
$datos->execute();

$listaFloracion = Array();
if ($datos == false) {
    echo "Error al ingresar";
} else {
    while ($rs = $datos->fetch()) {
        $floraciones = new stdClass();
        $floraciones->idFloracion = $rs["idFloracion"];
        $floraciones->floracion = $rs["floracion"];
        $listaFloracion[] = $floraciones;
    }
    echo json_encode($listaFloracion);
}
?>