<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idEntradaMateria = $_GET["idEntradaMateria"];
$cantidadTotal = $_GET["cantidadTotal"];
$importeTotal = $_GET["importeTotal"];

if (isset($_GET['tipoDeMiel'])) {
    switch ($_GET['tipoDeMiel']) {
        case '1':
            $encabezado = 'materiaprimaencabezadoentradas';
            break;
        case '2':
            $encabezado = 'materiaprimaencabezadoentradas_organico';
            break;
        case '7':
            $encabezado = 'materiaprimaencabezadoentradas_naranjo';
            break;
        default:
            throw new Exception('Tipo de miel inválido');
            break;
    }
}

$sql = "UPDATE $encabezado SET cantidadTotal = :cantidadTotal, importeTotal = :importeTotal WHERE materiaprimaencabezadoentradas.idEntradaMateria = :idEntradaMateria";
$datos = $con->prepare($sql);
$datos->bindParam(':idEntradaMateria', $idEntradaMateria);
$datos->bindParam(':cantidadTotal', $cantidadTotal);
$datos->bindParam(':importeTotal', $importeTotal);
$datos->execute();
