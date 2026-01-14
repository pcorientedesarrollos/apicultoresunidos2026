<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$error = "";

if (!isset($_GET['idCuenta'])) {
    echo $error = "Falta el codigo";
    die;
}
$param = $_GET['idCuenta'];

$query = "SELECT idCuenta, numDeCuenta FROM cuentasbancarias WHERE idCuenta = :idCuenta";
$datos = $con->prepare($query);
$datos->bindParam(':idCuenta', $param);
$datos->execute();

while ($row = $datos->fetch()) {
    $cuenta = new stdClass();
    $cuenta->idCuenta = $row["idCuenta"];
    $cuenta->numDeCuenta = $row["numDeCuenta"];
}
echo $json_response = json_encode($cuenta);
?>