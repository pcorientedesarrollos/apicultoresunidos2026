<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idProceso = $_GET["idProceso"];

if(isset($_GET["organica"])){
    switch ($_GET['organica']) {
        case 0:
            $procesoinocuo = 'procesozonainocua_organico';
            break;
        case 1:
            $procesoinocuo = 'procesozonainocua_mantequilla';
            break;
        case 2:
            $procesoinocuo = 'procesozonainocua_altiplano';
            break;
        case 3:
            $procesoinocuo = 'procesozonainocua_naranjo';
            break;
        case 4:
            $procesoinocuo = 'procesozonainocua_aguacate';
            break;
        case 5:
            $procesoinocuo = 'procesozonainocua_mezquite';
            break;
    }
    $sql = "DELETE FROM $procesoinocuo WHERE idProceso = :idProceso";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idProceso', $idProceso);
    $datos->execute();
}else{
    $sql = "DELETE FROM procesozonainocua WHERE idProceso = :idProceso";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idProceso', $idProceso);
    $datos->execute();
}

?>