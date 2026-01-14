<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idSolicitudCertificado = $_GET['idSolicitudCertificado'];

if (isset($_GET['contrato'])) {
    $query = "SELECT numContrato, idLoteInterno, lote FROM solicitudcze WHERE idSolicitudCertificado = :idSolicitudCertificado";
    $datos = $con->prepare($query);
    $datos->bindParam(':idSolicitudCertificado', $idSolicitudCertificado);
    $datos->execute();

    while ($row = $datos->fetch()) {
        $infoCliente = new stdClass();
        $infoCliente->numContrato = $row["numContrato"];
        $infoCliente->idLoteInterno = $row["idLoteInterno"];
        $infoCliente->lote = $row["lote"];
    }
    echo json_encode($infoCliente);
} else {
    $query = "SELECT datosAnexo FROM solicitudcze WHERE idSolicitudCertificado = :idSolicitudCertificado";
    $datos = $con->prepare($query);
    $datos->bindParam(':idSolicitudCertificado', $idSolicitudCertificado);
    $datos->execute();

    while ($row = $datos->fetch()) {
        $infoCliente = new stdClass();
        $infoCliente->datosAnexo = $row["datosAnexo"];
    }
    echo json_encode($infoCliente);
}


?>