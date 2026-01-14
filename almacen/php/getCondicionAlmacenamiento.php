<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$mes = $_GET["mes"];
$sql = "SELECT * FROM condicionesalmacenamiento ca  
        INNER JOIN temas t
        ON t.idTema = ca.idTema WHERE mes = :mes";
$datos = $con->prepare($sql);
$datos->bindParam(':mes', $mes);
$datos->execute();
if ($datos == false) {
    echo mysql_error();
} else {
    $arrayCondicion = array();
    while ($respuAbono = $datos->fetch()) {
        $condicion = new stdClass();
        $condicion->idCondicionAlmacenamiento = $respuAbono["idCondicionAlmacenamiento"];
        $condicion->almacen = $respuAbono["almacen"];
        $condicion->mes = $respuAbono["mes"];
        $condicion->idTema = $respuAbono["idTema"];
        $condicion->tema = $respuAbono["tema"];
        $condicion->semana1 = $respuAbono["semana1"];
        $condicion->semana2 = $respuAbono["semana2"];
        $condicion->semana3 = $respuAbono["semana3"];
        $condicion->semana4 = $respuAbono["semana4"];
        $condicion->semana5 = $respuAbono["semana5"];

        $arrayCondicion[] = $condicion;
    }

    echo json_encode($arrayCondicion);
}
?>