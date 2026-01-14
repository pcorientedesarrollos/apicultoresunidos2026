<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();
$idSubcuenta = $_GET["idSubcuenta"];

$sql = "SELECT idSubcuenta, subcuenta FROM subcuentas 
WHERE idSubcuenta = :idSubcuenta";

$datos = $conexion->prepare($sql);
$datos->bindParam(':idSubcuenta', $idSubcuenta);
$datos->execute();

if ($datos == false) {
    echo mysql_error();
} else {
    while ($rs = $datos->fetch()) {
        $subcuenta = new stdClass();
        $subcuenta->idSubcuenta = $rs["idSubcuenta"];
        $subcuenta->subcuenta = $rs["subcuenta"];
    }

    echo json_encode($subcuenta);
}