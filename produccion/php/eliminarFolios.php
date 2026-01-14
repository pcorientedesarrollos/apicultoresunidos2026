<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idBusqueda = $_GET["idBusqueda"];

if(isset($_GET["organica"])){
    switch ($_GET['organica']) {
        case 0:
            $busqueda = 'busquedadefolios_organico';
            break;
        case 1:
            $busqueda = 'busquedadefolios_mantequilla';
            break;
        case 2:
            $busqueda = 'busquedadefolios_altiplano';
            break;
        case 3:
            $busqueda = 'busquedadefolios_naranjo';
            break;
        case 4:
            $busqueda = 'busquedadefolios_aguacate';
            break;
        case 5:
            $busqueda = 'busquedadefolios_mezquite';
            break;
    }
    $sql = "DELETE FROM $busqueda WHERE idBusqueda = :idBusqueda";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idBusqueda', $idBusqueda);
    $datos->execute();
}else{
    $sql = "DELETE FROM busquedadefolios WHERE idBusqueda = :idBusqueda";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idBusqueda', $idBusqueda);
    $datos->execute();
}
?>