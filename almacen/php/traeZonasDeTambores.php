<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$tipoMiel = $_GET['tipoMiel'];

$sql ="SELECT idZonaTambor, nombre FROM zonastambores WHERE tipoMiel = $tipoMiel";
    if (isset($_GET['sobrante'])) {
        $sql .= " AND (idZonaTambor = 1 OR idZonaTambor = 10) ORDER BY idZonaTambor ASC;";
    }else{
        $sql .= " ORDER BY idZonaTambor ASC;";  
    }
$datos = $con->prepare($sql);
$datos->execute();

$arrayZonas = array();
while ($row = $datos->fetch()) {
    $listaZonasTambos = new stdClass();
    $listaZonasTambos->idZonaTambor = $row["idZonaTambor"];
    $listaZonasTambos->nombre = $row["nombre"];
    $arrayZonas[] = $listaZonasTambos;
}

echo $json_response = json_encode($arrayZonas);
?>