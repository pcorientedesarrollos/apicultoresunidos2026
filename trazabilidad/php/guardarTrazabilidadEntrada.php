<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idLoteInterno = $_GET["idLoteInterno"];
$lote = $_GET["lote"];

//$sqlTelefono = "UPDATE entradaysalida SET lote = :lote WHERE idLoteInterno = :idLoteInterno";
//$datos = $conexion->prepare($sqlTelefono);
//$datos->bindParam(':lote', $lote);
//$datos->bindParam(':idLoteInterno', $idLoteInterno);
//$datos->execute();

if ($datos == false) {
    echo mysql_error();
} else {
    echo 'Nuevo Telefono disponible';
}
