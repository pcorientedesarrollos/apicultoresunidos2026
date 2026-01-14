<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$informacion = array();

$sql = "SELECT * FROM configuracionlaboratorio cl 
INNER JOIN rangos r 
on cl.signo = r.idRangos
INNER JOIN opcioneslaboratorio ol
on cl.idOpcionLab = ol.idopcionesLaboratorio 
";
$data = $con->prepare($sql);
$data->execute();

if ($data == false) {
    echo mysql_error();
} else {
    while ($rs = $data->fetch()) {
        $valoresConfi = new stdClass();
        $valoresConfi->idOpcionLab = $rs["idOpcionLab"];
        $valoresConfi->simbolo = $rs["simbolo"];
        $valoresConfi->rango1 = $rs["rango1"];
        $valoresConfi->rango2 = $rs["rango2"];
        $valoresConfi->idRango = $rs["idRangos"];
        $valoresConfi->descripcion = $rs["descripcion"];
        $valoresConfi->idOpcionLaboratorio = $rs["idopcionesLaboratorio"];
        if ($valoresConfi->descripcion == null) {
            $valoresConfi->descripcion = "";
        }
        $informacion[] = $valoresConfi;
    }

    echo json_encode($informacion);
}
