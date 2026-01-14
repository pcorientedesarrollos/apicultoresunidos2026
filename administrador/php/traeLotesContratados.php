<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();

$tipoDeMiel = file_get_contents('php://input');
$resultado = [];
try {
    if (!$tipoDeMiel) {
        throw new Exception('Se esperaban argumentos');
    }
    switch ($tipoDeMiel) {
        case '1':
            $tabla = 'lotescontratados';
            $experimental = 'experimental';
            $calidad = 'calidad';
            $muestras = 'enviomuestras';
            $proceso = 'reportesdeprocesos';
            $carga = 'entradaysalida';
            break;
        case '2':
            $tabla = 'lotescontratados_organico';
            $experimental = 'experimental_organico';
            $calidad = 'calidad_organico';
            $muestras = 'enviomuestras_organico';
            $proceso = 'reportesdeprocesos_organico';
            $carga = 'entradaysalida_organico';
            break;
        default:
            throw new Exception('Argumento inválido');
            break;
    }

    // $sql = "SELECT lc.idLoteContratado, lc.idCliente, lc.tipoDeCliente, lc.contrato,
    $sql = "SELECT lc.*,
    c.marcaFinalCliente, c.idLoteInterno, em.fechaEnvio, p.fechaReporte AS fechaProceso,
    c.fechaEnvasado, cg.fechaImpresion AS fechaSalida, l.fechaFactura,
	CASE WHEN x.numContrato > 0 THEN 'Entregado' ELSE 'Pendiente' END AS estado
    FROM $tabla lc
    LEFT JOIN $experimental x ON x.numContrato = lc.idLoteContratado
    LEFT JOIN $calidad c ON c.idLoteExperimental = x.idLoteExperimental
    LEFT JOIN $muestras em ON em.numContrato = lc.idLoteContratado
    LEFT JOIN $proceso p ON p.idLoteInterno = c.idLoteInterno
    LEFT JOIN $carga cg ON cg.idLoteInterno = c.idLoteInterno
    LEFT JOIN listadepesos l ON l.lote = cg.lote AND l.tipoMiel = $tipoDeMiel
    LEFT JOIN solicitudcze s ON c.idLoteInterno = s.idLoteInterno AND s.tipoMiel = $tipoDeMiel
    ORDER BY idLoteContratado DESC";
    $datos = $con->prepare($sql);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }

    foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $dato) {
        if ($dato['tipoDeCliente'] == '10') {
            $query = "SELECT *
         FROM clientesexportadores
         WHERE idClienteExportador = :idClienteExportador";
            $datosResp = $con->prepare($query);
            $datosResp->bindParam(':idClienteExportador', $dato['idCliente']);
            $datosResp->execute();
            $dato['cliente'] = $datosResp->fetch(PDO::FETCH_ASSOC);
        } else if ($dato['tipoDeCliente'] == '6') {
            $seleccionarClientes = $con->prepare("SELECT nombre FROM clientes WHERE idCliente = :idCliente");
            $seleccionarClientes->bindParam(':idCliente', $dato['idCliente']);
            $seleccionarClientes->execute();
            $dato['cliente'] = $seleccionarClientes->fetch(PDO::FETCH_ASSOC);
        }

        $sqlFechaEmbarque = "SELECT s.datosTransporte
        FROM solicitudcze s
        WHERE s.idLoteInterno = :idLote AND tipoMiel = $tipoDeMiel";
        $queryFechaEmbarque = $con->prepare($sqlFechaEmbarque);
        $queryFechaEmbarque->bindParam(':idLote', $dato['idLoteInterno']);
        $queryFechaEmbarque->execute();
        if ($queryFechaEmbarque->rowCount() === 1) {
            $result = $queryFechaEmbarque->fetch(PDO::FETCH_ASSOC);
            $datosTransporte = json_decode($result['datosTransporte']);
            $dato['fechaEmbarque'] = $datosTransporte->fechaEmbarque;
        } else {
            $dato['fechaEmbarque'] = '';
        }

        array_push($resultado, $dato);
    }


    echo json_encode(['error' => false, 'message' => '', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine(), 'data' => $resultado]);
}
