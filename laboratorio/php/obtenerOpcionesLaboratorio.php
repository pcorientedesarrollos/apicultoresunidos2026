<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();

$sql = "SELECT * FROM opcioneslaboratorio";
$datos = $con->prepare($sql);
$datos->execute();

if ($datos == false) {
    echo mysql_error();
} else {
    $listaOpciones = array();
    while ($rs = $datos->fetch()) {
        $opciones = new stdClass();
        $opciones->id = $rs["idopcionesLaboratorio"];
        $opciones->opcion = $rs["opciones"];
        $opciones->signo = $rs["signo"];
        $listaOpciones[] = $opciones;
    }

    echo json_encode($listaOpciones);
}
?>