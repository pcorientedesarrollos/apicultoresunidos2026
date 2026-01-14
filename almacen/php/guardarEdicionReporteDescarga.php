<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
date_default_timezone_set('America/Merida');
$json = file_get_contents("php://input");

try {
    $con->beginTransaction();
    if (!$json) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($json);
        $info = $datos->valor;
    }

    switch ($info[0]->producto) {
        case '0':
            $entradaysalida_tabla = 'entradaysalida';
            break;
        case '1':
            $entradaysalida_tabla = 'entradaysalida';
            break;
        case '2':
            $entradaysalida_tabla = 'entradaysalida_organico';
            break;
        default:
            throw new Exception('El tipo de miel no es válido');
            break;
    }

    $sqlDescripciones = $con->prepare("UPDATE descripciones SET producto = :producto, cantidad = '0', pesoBruto = '0', pesoTara = '0',
                        pesoNeto = '0' WHERE idDescripcion = :idDescripcion");
    $sqlDescripciones->bindParam(':producto', $info[0]->producto);
    $sqlDescripciones->bindParam(':idDescripcion', $info[0]->idDescripcion);
    $sqlDescripciones->execute();
    if ($sqlDescripciones == FALSE) {
        throw new Exception($con->errorInfo());
    }

    $sqlCondiciones = $con->prepare("UPDATE condicionesunidad SET tipo = :tipo, limpieza = :limpieza, materialExtrano = :materialExtrano,
                        vehiculoAdecuado = :vehiculoAdecuado, cabello = :cabello, unas = :unas, ropa = :ropa WHERE idCondicion = :idCondicion");
    $sqlCondiciones->bindParam(':tipo', $info[0]->tipo);
    $sqlCondiciones->bindParam(':limpieza', $info[0]->limpieza);
    $sqlCondiciones->bindParam(':materialExtrano', $info[0]->materialExtrano);
    $sqlCondiciones->bindParam(':vehiculoAdecuado', $info[0]->vehiculoAdecuado);
    $sqlCondiciones->bindParam(':cabello', $info[0]->cabello);
    $sqlCondiciones->bindParam(':unas', $info[0]->unas);
    $sqlCondiciones->bindParam(':ropa', $info[0]->ropa);
    $sqlCondiciones->bindParam(':idCondicion', $info[0]->idCondicion);
    $sqlCondiciones->execute();
    if ($sqlCondiciones == FALSE) {
        throw new Exception($con->errorInfo());
    }

    $sqlExternoT = $con->prepare("UPDATE externotambores SET roto = :roto, abolladuras = :abolladuras, recipienteAdecuado = :recipienteAdecuado,
                        lavadoExterior = :lavadoExterior WHERE idExternoTambor = :idExternoTambor");
    $sqlExternoT->bindParam(':roto', $info[0]->roto);
    $sqlExternoT->bindParam(':abolladuras', $info[0]->abolladuras);
    $sqlExternoT->bindParam(':recipienteAdecuado', $info[0]->recipienteAdecuado);
    $sqlExternoT->bindParam(':lavadoExterior', $info[0]->lavadoExterior);
    $sqlExternoT->bindParam(':idExternoTambor', $info[0]->idExternoTambor);
    $sqlExternoT->execute();
    if ($sqlExternoT == FALSE) {
        throw new Exception($con->errorInfo());
    }


    $sqlPersonal = $con->prepare("UPDATE personalacciones SET limpiezaPersonal = :limpiezaPersonal, rotulacion = :rotulacion, marcacion = :marcacion,
                        montacargas = :montacargas WHERE idPersonal = :idPersonal");
    $sqlPersonal->bindParam(':limpiezaPersonal', $info[0]->limpiezaPersonal);
    $sqlPersonal->bindParam(':rotulacion', $info[0]->rotulacion);
    $sqlPersonal->bindParam(':marcacion', $info[0]->marcacion);
    $sqlPersonal->bindParam(':montacargas', $info[0]->montacargas);
    $sqlPersonal->bindParam(':idPersonal', $info[0]->idPersonal);
    $sqlPersonal->execute();
    if ($sqlPersonal == FALSE) {
        throw new Exception($con->errorInfo());
    }

    $sqlReporteDescarga = $con->prepare("UPDATE $entradaysalida_tabla SET fechaImpresion = :fechaImpresion, horaInicio = :horaInicio, responsable = :responsable, supervisor = :supervisor,
    idOperador = :idOperador, idPlaca = :idPlaca, contenedor = :contenedor, sello = :sello, lote = :lote, observaciones = :observaciones, horaFinal = :horaFinal
    WHERE idReporte = :idReporte");
    $sqlReporteDescarga->bindParam(':fechaImpresion', $info[0]->fechaImpresion);
    $sqlReporteDescarga->bindParam(':horaInicio', $info[0]->horaInicio);
    $sqlReporteDescarga->bindParam(':responsable', $info[0]->responsable);
    $sqlReporteDescarga->bindParam(':supervisor', $info[0]->supervisor);
    $sqlReporteDescarga->bindParam(':idOperador', $info[0]->idOperador);
    $sqlReporteDescarga->bindParam(':idPlaca', $info[0]->idPlaca);
    $sqlReporteDescarga->bindParam(':contenedor', $info[0]->contenedor);
    $sqlReporteDescarga->bindParam(':sello', $info[0]->sello);
    $sqlReporteDescarga->bindParam(':lote', $info[0]->lote);
    $sqlReporteDescarga->bindParam(':observaciones', $info[0]->observaciones);
    $sqlReporteDescarga->bindParam(':horaFinal', $info[0]->horaFinal);
    $sqlReporteDescarga->bindParam(':idReporte', $info[0]->idReporte);
    $sqlReporteDescarga->execute();
    if ($sqlReporteDescarga == FALSE) {
        throw new Exception($con->errorInfo());
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha actualizado el reporte']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line: ' . $e->getLine()]);
    exit();
}
