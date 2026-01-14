<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = new stdClass();
try {

    if (!isset($_GET['idLote'])) {
        throw new Exception('No se recibieron datos');
    } else {
        $idLote = $_GET['idLote'];
        $tipoMiel = $_GET['miel'];
        switch ($tipoMiel) {
            case '1':
                $encabezado = 'certificadoloteterminado_encabezado';
                $detalle = 'certificadoloteterminado_detalle';
                $tipoDeMiel = '1';
                break;
            case '2':
                $encabezado = 'certificadoloteterminado_encabezado_organico';
                $detalle = 'certificadoloteterminado_detalle_organico';
                $tipoDeMiel = '2';
                break;
            case '5':
                $encabezado = 'certificadoloteterminado_encabezado_mantequilla';
                $detalle = 'certificadoloteterminado_detalle_mantequilla';
                $tipoDeMiel = '5';
                break;
            case '6':
                $encabezado = 'certificadoloteterminado_encabezado_altiplano';
                $detalle = 'certificadoloteterminado_detalle_altiplano';
                $tipoDeMiel = '6';
                break;
            case '7':
                $encabezado = 'certificadoloteterminado_encabezado_naranjo';
                $detalle = 'certificadoloteterminado_detalle_naranjo';
                $tipoDeMiel = '7';
                break;
            case '8':
                $encabezado = 'certificadoloteterminado_encabezado_aguacate';
                $detalle = 'certificadoloteterminado_detalle_aguacate';
                $tipoDeMiel = '8';
                break;
            case '9':
                $encabezado = 'certificadoloteterminado_encabezado_mezquite';
                $detalle = 'certificadoloteterminado_detalle_mezquite';
                $tipoDeMiel = '9';
                break;
        }
    }


    $sqlEncabezado = "SELECT c.*, $tipoDeMiel AS tipoDeMiel, cl.marcaFinalCliente AS marcacionFinal
    FROM $encabezado c LEFT JOIN calidad cl ON cl.idLoteInterno = c.idLote
    WHERE c.idLote= :idLote";
    $datos = $con->prepare($sqlEncabezado);
    $datos->bindParam(':idLote', $idLote);
    $datos->execute();
    if ($datos->rowCount() >= 1) {
        if ($datos == FALSE) {
            throw new Exception($con->errorInfo());
        }
        $resultado = $datos->fetch(PDO::FETCH_ASSOC);

        $idEncabezado = $resultado['idEncabezado'];

        $sql = "SELECT * FROM $detalle WHERE idEncabezado = :idEncabezado AND idAnalisis BETWEEN 1 AND 6 OR idAnalisis IN (16,17)";
        $sqlDetalle = $con->prepare($sql);
        $sqlDetalle->bindParam(':idEncabezado', $idEncabezado);
        $sqlDetalle->execute();

        if ($sqlDetalle == FALSE) {
            throw new Exception($con->errorInfo());
        } else {
            $resultado['detalle']['caracteristicas'] = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);
        }

        $sqlAntibioticos = "SELECT * FROM $detalle WHERE idEncabezado = :idEncabezado AND idAnalisis BETWEEN 7 AND 9";
        $sqlDetalleAn = $con->prepare($sqlAntibioticos);
        $sqlDetalleAn->bindParam(':idEncabezado', $idEncabezado);
        $sqlDetalleAn->execute();

        if ($sqlDetalleAn == FALSE) {
            throw new Exception($con->errorInfo());
        } else {
            $resultado['detalle']['antibioticos'] = $sqlDetalleAn->fetchAll(PDO::FETCH_ASSOC);
        }

        $sqlMicrobiologicos = "SELECT * FROM $detalle WHERE idEncabezado = :idEncabezado AND idAnalisis BETWEEN 10 AND 15";
        $sqlDetalleMicro = $con->prepare($sqlMicrobiologicos);
        $sqlDetalleMicro->bindParam(':idEncabezado', $idEncabezado);
        $sqlDetalleMicro->execute();

        if ($sqlDetalleMicro == FALSE) {
            throw new Exception($con->errorInfo());
        } else {
            $resultado['detalle']['microbiologicos'] = $sqlDetalleMicro->fetchAll(PDO::FETCH_ASSOC);
        }

        $sqlSensoriales = "SELECT * FROM $detalle WHERE idEncabezado = :idEncabezado AND idAnalisis BETWEEN 18 AND 21";
        $sqlDetalleSenso = $con->prepare($sqlSensoriales);
        $sqlDetalleSenso->bindParam(':idEncabezado', $idEncabezado);
        $sqlDetalleSenso->execute();

        if ($sqlDetalleSenso == FALSE) {
            throw new Exception($con->errorInfo());
        } else {
            $resultado['detalle']['sensoriales'] = $sqlDetalleSenso->fetchAll(PDO::FETCH_ASSOC);
        }
    } else {
        $resultado = '0';
    }


    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => $resultado]);
    exit();
}
