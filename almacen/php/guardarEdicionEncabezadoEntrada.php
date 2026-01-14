<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$json = file_get_contents("php://input");
$datos = json_decode($json);
$info = $datos->valor;

$datosEncabezado = $info;
$idEntradaMateria = $_GET["idEntradaMateria"];

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

$sqlEncabezado = "UPDATE $encabezado SET fecha = :fecha, idProveedor = :idProveedor, idMotivo = :idMotivo, cantidadTotal = :cantidadTotal, importeTotal = :importeTotal, tipoCliente = :tipoCliente WHERE idEntradaMateria = :idEntradaMateria";
$dato = $con->prepare($sqlEncabezado);
$dato->bindParam(':fecha', $info->fecha);
$dato->bindParam(':idProveedor', $info->idProveedor);
$dato->bindParam(':idMotivo', $info->idMotivo);
$dato->bindParam(':cantidadTotal', $info->cantidadTotal);
$dato->bindParam(':importeTotal', $info->importeTotal);
$dato->bindParam(':tipoCliente', $info->tipoCliente);
$dato->bindParam(':idEntradaMateria', $idEntradaMateria);
$dato->execute();