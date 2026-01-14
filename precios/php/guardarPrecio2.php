<?php

include_once '../../DAOConeccion/coneccion.php';
$cn = new Coneccion();
$cn->Conectarse();

$folio = $_GET["folio"];
$totalCompra = $_GET["totalCompra"];
$id = $_GET["id"];

$json = file_get_contents("php://input");
$datos = json_decode($json);

$info = $datos->valor;

//$sql1= "UPDATE almacenencabezado set folio = '$folio' WHERE almacenencabezado.idAlmacen = '" . $info->idAlmacenEncabezado . "'";
$sql1= "UPDATE almacenencabezado set folio = '$folio', totalCompra= '$totalCompra' WHERE almacenencabezado.idAlmacen = '$id'";
mysql_query($sql1);

foreach ($info as $i) {
    $sql = "UPDATE almacen set precio = '" . $i->precio . "', costoTotal = '" .$i->costoTotal."' WHERE almacen.idAlmacen = '" . $i->idAlmacen . "'";
    mysql_query($sql);
}




