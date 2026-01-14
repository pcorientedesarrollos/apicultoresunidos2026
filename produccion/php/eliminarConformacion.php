<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idConformacionLote = $_GET["idConformacionLote"];

if(isset($_GET["organica"])){
    switch ($_GET['organica']) {
        case 0:
            $almacen = 'conformaciondelotes_organico';
            break;
        case 1:
            $almacen = 'conformaciondelotes_mantequilla';
            break;
        case 2:
            $almacen = 'conformaciondelotes_altiplano';
            break;
        case 3:
            $almacen = 'conformaciondelotes_altiplano';
            break;
        case 4:
            $almacen = 'conformaciondelotes_aguacate';
            break;
        case 5:
            $almacen = 'conformaciondelotes_mezquite';
            break;
    }
    $sql = "DELETE FROM $almacen WHERE idConformacionLote = :idConformacionLote";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idConformacionLote', $idConformacionLote);
    $datos->execute();
}else{
    $sql = "DELETE FROM conformaciondelotes WHERE idConformacionLote = :idConformacionLote";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idConformacionLote', $idConformacionLote);
    $datos->execute();
}
?>