<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$json = file_get_contents("php://input");

try {

    if (!$json) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($json);
        $miel = $_GET['miel'];

        switch ($miel) {
            case '1':
                $carga = 'entradaysalida';
                break;
            case '2':
                $carga = 'entradaysalida_organico';
                break;
            case '5':
                $carga = 'entradaysalida_mantequilla';
                break;
            case '6':
                $carga = 'entradaysalida_altiplano';
                break;
            case '7':
                $carga = 'entradaysalida_naranjo';
                break;
            case '8':
                $carga = 'entradaysalida_aguacate';
                break;
            case '9':
                $carga = 'entradaysalida_mezquite';
                break;
        }
    }
    $con->beginTransaction();

    $sqlTblEntradaSalida = "UPDATE $carga SET lote = :lote WHERE idReporte = :idReporte";
    $datEnSa = $con->prepare($sqlTblEntradaSalida);
    $datEnSa->bindParam(':lote', $datos->lote);
    $datEnSa->bindParam(':idReporte', $datos->idReporte);
    $datEnSa->execute();
    if ($datEnSa == false) {
        throw new Exception($con->errorInfo());
    } else {
        $sqlTblLista = "UPDATE listadepesos SET lote = :lote WHERE idReporteCarga = :idReporte AND tipoMiel = :miel";
        $datLista = $con->prepare($sqlTblLista);
        $datLista->bindParam(':lote', $datos->lote);
        $datLista->bindParam(':idReporte', $datos->idReporte);
        $datLista->bindParam(':miel', $miel);
        $datLista->execute();
        if ($datLista == false) {
            throw new Exception($con->errorInfo());
        }
    }

    $con->commit();
    echo json_encode(['error' => false, 'info' => $datos]);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
}

exit();
