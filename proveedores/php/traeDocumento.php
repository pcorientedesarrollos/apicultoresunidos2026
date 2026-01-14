<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

//$id = $_GET['id'];

if (isset($_GET['idContrato'])) {
    $sql = "SELECT archivoContrato FROM archivoscontratos WHERE idContrato = :idContrato";
    $result = $conexion->prepare($sql);
    $result->bindParam(':idContrato', $_GET['idContrato']);
    $result->execute();
    $result->bindColumn('archivoContrato', $respuesta);
    $result->fetch(PDO::FETCH_BOUND);
}
if (isset($_GET['idIne'])) {
    $sql = "SELECT archivoINE FROM archivosine WHERE idIne = :idIne";
    $result = $conexion->prepare($sql);
    $result->bindParam(':idIne', $_GET['idIne']);
    $result->execute();
    $result->bindColumn('archivoINE', $respuesta);
    $result->fetch(PDO::FETCH_BOUND);
}
if (isset($_GET['idInstalacion'])) {
    $sql = "SELECT archivoInstalacion FROM archivosinstalaciones WHERE idInstalacion = :idInstalacion";
    $result = $conexion->prepare($sql);
    $result->bindParam(':idInstalacion', $_GET['idInstalacion']);
    $result->execute();
    $result->bindColumn('archivoInstalacion', $respuesta);
    $result->fetch(PDO::FETCH_BOUND);
}
if (isset($_GET['idApiario'])) {
    $sql = "SELECT archivoApiario FROM archivosapiarios WHERE idApiario = :idApiario";
    $result = $conexion->prepare($sql);
    $result->bindParam(':idApiario', $_GET['idApiario']);
    $result->execute();
    $result->bindColumn('archivoApiario', $respuesta);
    $result->fetch(PDO::FETCH_BOUND);
}
if (isset($_GET['idCarta'])) {
    $sql = "SELECT archivoCarta FROM archivoscartas WHERE idCarta = :idCarta";
    $result = $conexion->prepare($sql);
    $result->bindParam(':idCarta', $_GET['idCarta']);
    $result->execute();
    $result->bindColumn('archivoCarta', $respuesta);
    $result->fetch(PDO::FETCH_BOUND);
}
if (isset($_GET['idPagare'])) {
    $sql = "SELECT archivoPagare FROM archivospagares WHERE idPagare = :idPagare";
    $result = $conexion->prepare($sql);
    $result->bindParam(':idPagare', $_GET['idPagare']);
    $result->execute();
    $result->bindColumn('archivoPagare', $respuesta);
    $result->fetch(PDO::FETCH_BOUND);
}
echo ($respuesta);
?>