<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idAlmacenEncabezado = $_GET['idAlmacenEncabezado'];

$query = " SELECT idImagen, idAlmacen, archivoRelacion FROM archivospdf WHERE idAlmacen = :idAlmacenEncabezado";
$datos = $con->prepare($query);
$datos->bindParam(':idAlmacenEncabezado', $idAlmacenEncabezado);
$datos->execute();

$arrayPdf = array();
while ($row = $datos->fetch()) {
    $imgLista = new stdClass();
    $imgLista->idImagen = $row["idImagen"];
    $imgLista->idAlmacen = $row["idAlmacen"];
    $imgLista->archivoRelacion = $row["archivoRelacion"];
    $arrayPdf[] = $imgLista;
}

//# JSON-encode the response
echo $json_response = json_encode($arrayPdf);
?>