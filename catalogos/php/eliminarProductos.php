<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idSubSubcuenta = $_GET["idSubSubcuenta"];

$sql = "UPDATE subsubcuentas SET ocultar = 1 WHERE subsubcuentas.idSubSubcuenta = :idSubSubcuenta";
$datos = $con->prepare($sql);
$datos->bindParam(':idSubSubcuenta', $idSubSubcuenta);
$datos->execute();
