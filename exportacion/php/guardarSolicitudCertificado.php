<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$info = json_decode($miInfo);

if (isset($_GET['idSolicitudCertificado'])) {
    $sqlUp = "UPDATE solicitudcze SET fechaSolicitud = :fechaSolicitud, datosProducto = :datosProducto, datosTransporte = :datosTransporte, idClienteExportador = :idClienteExportador, idLoteInterno = :idLoteInterno, lote = :lote WHERE idSolicitudCertificado = :idSolicitudCertificado";
    $datos = $con->prepare($sqlUp);
    $datos->bindParam(':datosProducto', $info->datosProducto);
    $datos->bindParam(':datosTransporte', $info->datosTransporte);
    $datos->bindParam(':fechaSolicitud', $info->fechaSolicitud);
    $datos->bindParam(':idClienteExportador', $info->idClienteExportador);
    $datos->bindParam(':idLoteInterno', $info->idLoteInterno);
    $datos->bindParam(':lote', $info->lote);
    $datos->bindParam(':idSolicitudCertificado', $_GET['idSolicitudCertificado']);
    $datos->execute();
    if ($datos == false) {
        echo $con->errorInfo();
    } else {
        echo 'Se modificó';
    }
} else {
    $sql = "INSERT INTO solicitudcze (fechaSolicitud, datosProducto, datosTransporte, idClienteExportador, datosAnexo, idLoteInterno, lote, tipoMiel, sinLote) VALUES (:fechaSolicitud, :datosProducto, :datosTransporte, :idClienteExportador, '', :idLoteInterno, :lote, :tipoMiel, :sinLote)";
    $datos = $con->prepare($sql);
    $datos->bindParam(':datosProducto', $info->datosProducto);
    $datos->bindParam(':datosTransporte', $info->datosTransporte);
    $datos->bindParam(':fechaSolicitud', $info->fechaSolicitud);
    $datos->bindParam(':idClienteExportador', $info->idClienteExportador);
    $datos->bindParam(':idLoteInterno', $info->idLoteInterno);
    $datos->bindParam(':lote', $info->lote);
    $datos->bindParam(':tipoMiel', $info->tipoMiel);
    $datos->bindParam(':sinLote', $info->sinLote);
    $datos->execute();

    if ($datos == false) {
        echo $con->errorInfo();
    } else {
        echo 'Se guardó';
    }

}
?>