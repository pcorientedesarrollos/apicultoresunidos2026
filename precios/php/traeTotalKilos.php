<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$sql = "SELECT * FROM laboratorio WHERE idLaboratorio =  :idLaboratorio";
//$datos = mysql_query($sql, $cn->Conectarse());
$datos = $conexion->prepare($sql);
$datos->bindParam(':idLaboratorio', $idLaboratorio);
$datos->execute();

if ($datos == false) {
    throw new Exception('No se recibieron parámetros');
} else {
    $laboratorio = new stdClass();
    while ($rs = $datos->fetch()) {
        $laboratorio->idLaboratorio = $rs["idLaboratorio"];
        $laboratorio->idAlmacen = $rs["idAlmacen"];
        $laboratorio->porcentaje = $rs["porcentaje"];
        $laboratorio->sf = $rs["sf"];
        $laboratorio->st = $rs["st"];
        $laboratorio->c13 = $rs["c13"];
        $laboratorio->hmf = $rs["hmf"];
        $laboratorio->resultadoFinal = $rs["resultadoFinal"];
        $laboratorio->marcaInterna = $rs["marcaInterna"];
    }
    echo json_encode($laboratorio);
}
