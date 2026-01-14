<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idLoteExperimental = $_GET["idLoteExperimental"];

$existe = "";

switch($_GET['miel']){
    case '1':
    $calidad = 'calidad';
    break;
    case '2':
    $calidad = 'calidad_organico';    
    break;
    case '5':
    $calidad = 'calidad_mantequilla';    
    break;
    case '6':
    $calidad = 'calidad_altiplano';    
    break;
    case '7':
    $calidad = 'calidad_naranjo';    
    break;
    case '8':
    $calidad = 'calidad_aguacate';    
    break;
    case '9':
    $calidad = 'calidad_mezquite';    
    break;
}

$sql = "SELECT idLoteExperimental FROM $calidad WHERE idLoteExperimental = :idLoteExperimental";
$datos = $con->prepare($sql);
$datos->bindParam(':idLoteExperimental', $idLoteExperimental);
$datos->execute();

while ($row = $datos->fetch()) {
    $existe = $row["idLoteExperimental"];
};

if ($existe == null) {
    $respuesta = 0;
} else {
    $respuesta = 1;
}
echo json_encode($respuesta);
?>