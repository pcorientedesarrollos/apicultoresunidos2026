<?php

include_once '../../DAOConeccion/coneccion.php';
$cn = new Coneccion();
$cn->Conectarse();

$query = "             
SELECT cc.idControl, cc.fecha, cc.observaciones, om.idPersonalOM, om.idArea, om.nombre, a.area
FROM controlcambios cc
LEFT JOIN personaloaxaca om ON om.idPersonalOM = cc.idPersonalOM
LEFT JOIN areas a ON a.idArea = om.idArea
WHERE om.idPersonalOM != '26'
ORDER BY cc.fecha DESC
";

$data = mysql_query($query);
$informacionTabla = array();
while ($row = mysql_fetch_array($data)) {
    $controlDeCambios = new stdClass();
    $controlDeCambios->idControl = $row["idControl"];
    $controlDeCambios->fecha = $row["fecha"];
    $controlDeCambios->observaciones = $row["observaciones"];
    $controlDeCambios->idPersonalOM = $row["idPersonalOM"];
    $controlDeCambios->idArea = $row["idArea"];
    $controlDeCambios->nombre = $row["nombre"];
    $controlDeCambios->area = $row["area"];
    $informacionTabla[] = $controlDeCambios;
}

echo json_encode($informacionTabla);
$cn->cerrarBd();
?>