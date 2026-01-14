<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$query = "SELECT idSubcuenta, subcuenta FROM subcuentas";
$datos = $con->prepare($query);
$datos->execute();

$arraySub = array();
while ($row = $datos->fetch()) {
    $subcuenta = new stdClass();
    $subcuenta->idSubcuenta = $row["idSubcuenta"];
    $subcuenta->subcuenta = $row["subcuenta"];
    $arraySub[] = $subcuenta;
}

# JSON-encode the response
echo $json_response = json_encode($arraySub);
?>