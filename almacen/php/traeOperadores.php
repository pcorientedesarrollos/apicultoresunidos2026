<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$query = "SELECT * FROM choferes ORDER BY operador ASC";
$datos = $con->prepare($query);
$datos->execute();

$arrayChofer = array();
while ($row = $datos->fetch()) {
    $choferes = new stdClass();
    $choferes->idOperador = $row["idOperador"];
    $choferes->operador = $row["operador"];
    $choferes->licencia = $row["licencia"];
    $choferes->vigencia = $row["vigencia"];
    $choferes->compania = $row["compania"];
   $choferes->remolque = $row["remolque"];

    $arrayChofer[] = $choferes;
}
echo json_encode($arrayChofer);
?>