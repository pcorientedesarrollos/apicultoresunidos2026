<?php

include_once '../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();

$sqlModulos = "SELECT * FROM modulos";
$datosMdl = $con->prepare($sqlModulos);
$datosMdl->execute();

$rutas = array();
if ($datosMdl == false) {
    echo mysql_error();
} else {
    while ($rsModulos = $datosMdl->fetch()) {
        $datosModulos = new stdClass();
        $datosModulos->ruta = $rsModulos["ruta"];
        $rutas[] = $datosModulos;
    }
}
echo json_encode($rutas);
