<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if (isset($_GET['miel'])) {
    $miel = $_GET['miel'];

    switch ($miel) {
        case '1':
            $entradaysalida = 'entradaysalida';
            break;
        case '2':
            $entradaysalida = 'entradaysalida_organico';
            break;
        case '5':
            $entradaysalida = 'entradaysalida_mantequilla';
            break;
        case '6':
            $entradaysalida = 'entradaysalida_altiplano';
            break;
        case '7':
            $entradaysalida = 'entradaysalida_naranjo';
            break;
        case '8':
            $entradaysalida = 'entradaysalida_aguacate';
            break;
        case '9':
            $entradaysalida = 'entradaysalida_mezquite';
            break;
    }
}
if (isset($_GET['sinLote']) && isset($_GET['idSolicitudCertificado'])) {

    $idSolicitudCertificado = $_GET['idSolicitudCertificado'];
    

    $query = "SELECT 'N/A' AS idLoteInterno, 'N/A' AS marcaDistintiva, '' AS fechaSalida, 0 AS cantidad, 0 AS pesoNeto,
    s.idSolicitudCertificado, s.numContrato, s.fechaSolicitud, s.datosProducto, s.datosTransporte, s.idClienteExportador, s.datosAnexo, c.datosCliente
    FROM solicitudcze s
    LEFT JOIN clientesexportadores c ON c.idClienteExportador = s.idClienteExportador
    WHERE s.idSolicitudCertificado = :idSolicitud
    ORDER BY fechaSalida DESC";
    $datos = $con->prepare($query);
    $datos->bindParam(':idSolicitud', $idSolicitudCertificado);
    $datos->execute();

    while ($row = $datos->fetch()) {
        $infoSolicitud = new stdClass();
        $infoSolicitud->idLoteInterno = $row["idLoteInterno"];
        $infoSolicitud->marcaDistintiva = $row["marcaDistintiva"];
        $infoSolicitud->cantidad = $row["cantidad"];
        $infoSolicitud->pesoNeto = $row["pesoNeto"];
        $infoSolicitud->fechaSalida = $row["fechaSalida"];
        $infoSolicitud->idSolicitudCertificado = $row["idSolicitudCertificado"];
        $infoSolicitud->fechaSolicitud = $row["fechaSolicitud"];
        $infoSolicitud->datosProducto = $row["datosProducto"];
        $infoSolicitud->datosTransporte = $row["datosTransporte"];
        $infoSolicitud->idClienteExportador = $row["idClienteExportador"];
        $infoSolicitud->numContrato = $row["numContrato"];
        $infoSolicitud->datosCliente = $row["datosCliente"];

    }
    echo json_encode($infoSolicitud);

} else if (isset($_GET['idLoteInterno']) && isset($_GET['lote'])) {

    $idLoteInterno = $_GET['idLoteInterno'];
    $lote = $_GET['lote'];

    $query = "SELECT eys.idLoteInterno, ldp.lote AS marcaDistintiva, eys.fechaImpresion AS fechaSalida, COUNT(tlp.idPesoTambo) AS cantidad, ldp.totalBruto AS pesoBruto, ldp.totalNeto AS pesoNeto,
    s.idSolicitudCertificado, s.numContrato, s.fechaSolicitud, s.datosProducto, s.datosTransporte, s.idClienteExportador, s.datosAnexo, c.datosCliente
    FROM listadepesos ldp 
    INNER JOIN tamboreslistapesos tlp ON tlp.idTamborPeso = ldp.idTamborPeso
    INNER JOIN $entradaysalida eys ON eys.lote = ldp.lote
    LEFT JOIN solicitudcze s ON s.idLoteInterno = eys.idLoteInterno AND eys.lote = s.lote
    LEFT JOIN clientesexportadores c ON c.idClienteExportador = s.idClienteExportador
    WHERE eys.idClasificacion = 1 AND eys.idLoteInterno = :idLoteInterno AND ldp.lote = :lote AND ldp.tipoMiel = $miel
    ORDER BY fechaSalida DESC";
    $datos = $con->prepare($query);
    $datos->bindParam(':idLoteInterno', $idLoteInterno);
    $datos->bindParam(':lote', $lote);
    $datos->execute();

    while ($row = $datos->fetch()) {
        $infoSolicitud = new stdClass();
        $infoSolicitud->idLoteInterno = $row["idLoteInterno"];
        $infoSolicitud->marcaDistintiva = $row["marcaDistintiva"];
        $infoSolicitud->cantidad = $row["cantidad"];
        $infoSolicitud->pesoBruto = $row["pesoBruto"];
        $infoSolicitud->pesoNeto = $row["pesoNeto"];
        $infoSolicitud->fechaSalida = $row["fechaSalida"];
        $infoSolicitud->idSolicitudCertificado = $row["idSolicitudCertificado"];
        $infoSolicitud->fechaSolicitud = $row["fechaSolicitud"];
        $infoSolicitud->datosProducto = $row["datosProducto"];
        $infoSolicitud->datosTransporte = $row["datosTransporte"];
        $infoSolicitud->idClienteExportador = $row["idClienteExportador"];
        $infoSolicitud->numContrato = $row["numContrato"];
        $infoSolicitud->datosCliente = $row["datosCliente"];

    }
    echo json_encode($infoSolicitud);

} else if (isset($_GET['idSolicitudCertificado'])) {
    $query = "SELECT datosTransporte FROM solicitudcze WHERE idSolicitudCertificado = :idSolicitudCertificado";

    $datos = $con->prepare($query);
    $datos->bindParam(':idSolicitudCertificado', $_GET['idSolicitudCertificado']);
    $datos->execute();

    while ($row = $datos->fetch()) {
        $infoSolicitud = new stdClass();
        $infoSolicitud->datosTransporte = $row["datosTransporte"];
    }

    echo json_encode($infoSolicitud);
} else {
    $query = "SELECT erep.lote, erep.idLoteInterno, s.idSolicitudCertificado, s.fechaSolicitud, s.datosProducto, 
                s.datosTransporte, s.numContrato, erep.fechaImpresion AS fechaSalida, s.idClienteExportador, s.datosAnexo	,c.datosCliente, s.sinLote
                FROM $entradaysalida erep
                INNER JOIN listadepesos lp ON lp.lote = erep.lote
                LEFT JOIN solicitudcze s ON s.idLoteInterno = erep.idLoteInterno AND s.lote = erep.lote
                LEFT JOIN clientesexportadores c ON c.idClienteExportador = s.idClienteExportador
                WHERE erep.idClasificacion = 1 AND lp.tipoMiel = $miel
                UNION
                SELECT 'N/A' AS lote, 'N/A' AS idLoteInterno, s.idSolicitudCertificado, s.fechaSolicitud, s.datosProducto,
                s.datosTransporte, s.numContrato, '' AS fechaSalida, s.idClienteExportador, s.datosAnexo, c.datosCliente, s.sinLote
                FROM solicitudcze s
                LEFT JOIN clientesexportadores c ON c.idClienteExportador = s.idClienteExportador
                WHERE s.sinLote = 1
                ORDER BY fechaSalida DESC";
    $datos = $con->prepare($query);
    $datos->execute();

    $array = array();
    while ($row = $datos->fetch()) {
        $infoSolicitud = new stdClass();
        $infoSolicitud->lote = $row["lote"];
        $infoSolicitud->idLoteInterno = $row["idLoteInterno"];
        $infoSolicitud->fechaSalida = $row["fechaSalida"];
        $infoSolicitud->sinLote = $row["sinLote"];

        $infoSolicitud->idSolicitudCertificado = $row["idSolicitudCertificado"];
        if ($infoSolicitud->idSolicitudCertificado == null) {
            $infoSolicitud->idSolicitudCertificado = '';
        } else {
            $infoSolicitud->idSolicitudCertificado = $row["idSolicitudCertificado"];
        }

        $infoSolicitud->fechaSolicitud = $row["fechaSolicitud"];
        if ($infoSolicitud->fechaSolicitud == null) {
            $infoSolicitud->fechaSolicitud = '';
        } else {
            $infoSolicitud->fechaSolicitud = $row["fechaSolicitud"];;
        }

        $infoSolicitud->datosProducto = $row["datosProducto"];
        if ($infoSolicitud->datosProducto == null) {
            $infoSolicitud->datosProducto = '';
        } else {
            $infoSolicitud->datosProducto = $row["datosProducto"];
        }

        $infoSolicitud->datosTransporte = $row["datosTransporte"];
        if ($infoSolicitud->datosTransporte == null) {
            $infoSolicitud->datosTransporte = '';
        } else {
            $infoSolicitud->datosTransporte = $row["datosTransporte"];
        }

        $infoSolicitud->idClienteExportador = $row["idClienteExportador"];
        if ($infoSolicitud->idClienteExportador == null) {
            $infoSolicitud->idClienteExportador = '';
        } else {
            $infoSolicitud->idClienteExportador = $row["idClienteExportador"];
        }

        $infoSolicitud->datosCliente = $row["datosCliente"];
        if ($infoSolicitud->datosCliente == null) {
            $infoSolicitud->datosCliente = '';
        } else {
            $infoSolicitud->datosCliente = $row["datosCliente"];
        }

        $infoSolicitud->datosAnexo = $row["datosAnexo"];
        if ($infoSolicitud->datosAnexo == null) {
            $infoSolicitud->datosAnexo = '';
        } else {
            $infoSolicitud->datosAnexo = $row["datosAnexo"];
        }

        $infoSolicitud->numContrato = $row["numContrato"];
        if ($infoSolicitud->numContrato == null) {
            $infoSolicitud->numContrato = '';
        } else {
            $infoSolicitud->numContrato = $row["numContrato"];
        }

        $array[] = $infoSolicitud;
    }
    echo json_encode($array);
}
?>