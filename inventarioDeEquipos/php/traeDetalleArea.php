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

$query = "SELECT idArea, area FROM areas WHERE idArea = :idArea";
$datos = $con->prepare($query);
$datos->bindParam(':idArea', $idArea);
$datos->execute();

while ($row = $datos->fetch()) {
    $area = new stdClass();
    $area->idArea = $row["idArea"];
    $area->area = $row["area"];

    $area->arregloSubareas = array();
    $sqlArea = "SELECT * FROM subareas WHERE idArea = :idArea ";
    $datosAreas = $con->prepare($sqlArea);
    $datosAreas->bindParam(':idArea', $idArea);
    $datosAreas->execute();
    while ($row = $datosAreas->fetch()) {
        $areas = new stdClass();
        $areas->idSubarea = $row["idSubarea"];
        $areas->subarea = $row["subarea"];
        $areas->nombre = $row["nombre"];
        $area->arregloSubareas[] = $areas;
    }
}
echo $json_response = json_encode($area);
?>