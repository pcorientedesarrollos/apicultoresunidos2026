<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$query = "SELECT e.idEvaluacion, e.fecha, e.idProveedorMantto, p.nombreProveedor, p.telefono
         FROM evaluaciones e 
         INNER JOIN proveedoresmantto p ON p.idProveedorMantto = e.idProveedorMantto
         ORDER BY p.nombreProveedor ASC";
$datos = $con->prepare($query);
$datos->execute();

$array = array();
while ($row = $datos->fetch()) {
    $infoMantto = new stdClass();
    $infoMantto->idEvaluacion = $row["idEvaluacion"];
    $infoMantto->idProveedorMantto = $row["idProveedorMantto"];
    $infoMantto->nombreProveedor = $row["nombreProveedor"];
    $infoMantto->telefono = $row["telefono"];
    $infoMantto->fecha = $row["fecha"];
    $array[] = $infoMantto;
}
echo json_encode($array);
?>