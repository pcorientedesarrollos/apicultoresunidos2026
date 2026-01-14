<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idAlmacenEncabezado = $_GET['idAlmacenEncabezado'];

$query = "SELECT idPdf, idAlmacen, archivoPago FROM archivospdfpagos WHERE idAlmacen = :idAlmacenEncabezado";
$datos = $con->prepare($query);
$datos->bindParam(':idAlmacenEncabezado', $idAlmacenEncabezado);
$datos->execute();

$arrayImagen = array();

while ($row = $datos->fetch()) {
    $imgPago = new stdClass();
    $imgPago->idPdf = $row["idPdf"];
    $imgPago->idAlmacen = $row["idAlmacen"];
    $imgPago->archivoPago = $row["archivoPago"];
    $arrayImagen[] = $imgPago;
}

//# JSON-encode the response
echo $json_response = json_encode($arrayImagen);
?>