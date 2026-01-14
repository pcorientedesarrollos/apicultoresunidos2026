<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idDetalleEntrada = $_GET["idDetalleEntrada"];

if (isset($_GET['tipoDeMiel'])) {
    switch ($_GET['tipoDeMiel']) {
        case '1':
            $tbl_detalle = 'materiaprimadetalleentradas';
            break;
        case '2':
            $tbl_detalle = 'materiaprimadetalleentradas_organico';
            break;
        case '7':
            $tbl_detalle = 'materiaprimadetalleentradas_naranjo';
            break;
        default:
            throw new Exception('Tipo de miel inválido');
            break;
    }
}

$sql = "DELETE FROM $tbl_detalle WHERE idDetalleEntrada = :idDetalleEntrada";
$datos = $con->prepare($sql);
$datos->bindParam(':idDetalleEntrada', $idDetalleEntrada);
$datos->execute();
