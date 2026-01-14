<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = "SELECT a.*, b.banco, c.numDeCuenta
        FROM auxiliardebancos a
        LEFT JOIN bancos b ON b.idBanco = a.idBanco
        LEFT JOIN cuentasbancarias c ON c.idCuenta = a.idCuenta
        WHERE a.tipoDePersona = 1 AND a.nombreDe = 163 OR a.tipoDePersona = 4 AND a.nombreDe = 102
        ORDER BY a.fecha ASC";
$datos = $con->prepare($sql);
$datos->execute();
$array = array();
while ($row = $datos->fetch()) {
    $info = new stdClass();
    $info->fecha = $row["fecha"];
    $info->hora = $row["hora"];
    $info->movimiento = $row["movimiento"];
    $info->concepto = $row["concepto"];
    $info->descripcion = $row["descripcion"];
    $info->referencia = $row["referencia"];
    $info->cantidad = $row["cantidad"];
    $info->ingresoEgreso = $row["ingresoEgreso"];
    $info->tipoDePersona = $row["tipoDePersona"];
    $info->nombreDe = $row["nombreDe"];
    $info->banco = $row["banco"];
    $info->numDeCuenta = $row["numDeCuenta"];            
    $lista[] = $info;
}
echo $json_response = json_encode($lista);


