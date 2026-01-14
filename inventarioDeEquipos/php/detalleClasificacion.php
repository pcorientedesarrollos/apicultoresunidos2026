<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$error = "";

if (!isset($_GET['idClasificacion'])) {
    echo $error = "Falta el codigo";
    die;
}

$idClasificacion = $_GET['idClasificacion'];

$query = "SELECT idClasificacion, clasificacion FROM clasificaciones WHERE idClasificacion = :idClasificacion";
$datos = $con->prepare($query);
$datos->bindParam(':idClasificacion', $idClasificacion);
$datos->execute();

while ($row = $datos->fetch()) {
    $clas = new stdClass();
    $clas->idClasificacion = $row["idClasificacion"];
    $clas->clasificacion = $row["clasificacion"];
}
echo $json_response = json_encode($clas);
?>