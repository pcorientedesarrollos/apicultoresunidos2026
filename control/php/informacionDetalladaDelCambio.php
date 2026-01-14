<?php

include_once '../../DAOConeccion/coneccion.php';
$cn = new Coneccion();
$cn->Conectarse();

$idControl = $_GET['idControl'];

$query = "  
    SELECT cc.idControl, cc.fecha, cc.observaciones, om.idPersonalOM, om.idArea, om.nombre, a.area
FROM controlcambios cc
LEFT JOIN personaloaxaca om ON om.idPersonalOM = cc.idPersonalOM
LEFT JOIN areas a ON a.idArea = om.idArea
WHERE cc.idControl = '$idControl'";

$result = mysql_query($query);

while ($row = mysql_fetch_array($result)) {
    $cambios = new stdClass();
    $cambios->idControl = $row["idControl"];
    $cambios->fecha = $row["fecha"];
    $cambios->observaciones = $row["observaciones"];
    $cambios->idPersonalOM = $row["idPersonalOM"];
    $cambios->idArea = $row["idArea"];
    $cambios->nombre = $row["nombre"];
    $cambios->area = $row["area"];
}

# JSON-encode the response
echo $json_response = json_encode($cambios);
$cn->cerrarBd();
