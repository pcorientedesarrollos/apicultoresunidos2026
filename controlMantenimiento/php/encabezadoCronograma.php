<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$error = "";

if (!isset($_GET['idArea'])) {
    echo $error = "Falta el codigo";
    die;
}

$idArea = $_GET['idArea'];

$query = "SELECT area, idArea FROM areas WHERE idArea = :idArea";
$datos = $con->prepare($query);
$datos->bindParam(':idArea', $idArea);
$datos->execute();

while ($row = $datos->fetch()) {
    $nameArea = new stdClass();
    $nameArea->area = $row["area"];
    $nameArea->idArea = $row["idArea"];
}
echo $json_response = json_encode($nameArea);
?>