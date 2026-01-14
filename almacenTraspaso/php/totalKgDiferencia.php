<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$id = $_GET["id"];
$miel = $_GET["miel"];
if (isset($miel)) {
    switch ($miel) {
        case '1':
            $detalle = 'almacentraspaso';
            break;
        case '2':
            $detalle = 'almacentraspaso_organico';
        default:
            throw new Exception('Tipo de miel inválido');
            break;
    }
}

$sql = "SELECT SUM(diferencia) AS totalKgDiferencia,
        SUM(bruto) AS totalKgBruto, 
        SUM(tara) AS totalKgTara, 
        SUM(neto) AS totalKgNeto 
        FROM $detalle WHERE idAlmacenEncabezado = :id";
$datos = $con->prepare($sql);
$datos->bindParam(':id', $id);
$datos->execute();


$almacen = new stdClass();
while ($rs = $datos->fetch()) {
    $almacen->totalKgDiferencia = $rs["totalKgDiferencia"];
    $almacen->totalKgBruto = $rs["totalKgBruto"];
    $almacen->totalKgTara = $rs["totalKgTara"];
    $almacen->totalKgNeto = $rs["totalKgNeto"];
}
echo json_encode($almacen);
