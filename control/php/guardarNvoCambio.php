<?php

include_once '../../DAOConeccion/coneccion.php';
$cn = new Coneccion();
$cn->Conectarse();

$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);
$fecha = date("Y-m-d", strtotime($datos->fecha));

$sql = "INSERT INTO controlcambios (fecha, idArea, idPersonalOM, observaciones) VALUES ('$fecha','$datos->idArea', '$datos->idPersonalOM', '$datos->observaciones')";

$result = mysql_query($sql);
if ($result == false) {
    echo 'Error al ingresar el cambio';
} else {
    echo 'El cambio se agregó exitosamente';
}
$cn->cerrarBd();
