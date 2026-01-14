<?php

include_once '../../DAOConeccion/coneccion.php';
$cn = new Coneccion();
$cn->Conectarse();
$miInfo = file_get_contents("php://input");

$datos = json_decode($miInfo);
$datos = (array) ($datos);

$fecha = date("Y-m-d", strtotime($datos['fecha']));
$idArea = $datos['idArea'];
$idPersonalOM = $datos['idPersonalOM'];
$observaciones = $datos['observaciones'];
$idControl = $datos['idControl'];

if (isset($datos['idControl'])) {

    $sql = "UPDATE controlcambios SET fecha = '$fecha', idArea = '$idArea', idPersonalOM = '$idPersonalOM', observaciones = '$observaciones' WHERE controlcambios.idControl = '$idControl'";

    $result = mysql_query($sql);
    if ($result == false) {
        echo 'Error al ingresar el cambio';
    } else {
        echo 'El cambio se agregó exitosamente';
    }
}
$cn->cerrarBd();
