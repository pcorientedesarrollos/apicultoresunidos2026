<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$postdata = file_get_contents('php://input');

try {
    // Hacer una transacción en caso de haber errores
    $con->beginTransaction();

    if (!$postdata || !isset($_GET['eliminarReporte'])) {
        throw new Exception('No se recibió ningún dato.');
    } else {
        $reporte = json_decode($postdata);
        if (!isset($reporte->idTipoDeMiel)
            || !isset($reporte->idReporte)) {
            throw new Exception('No se recibió todos los parámetros.');
        }
    }

    switch ($reporte->idTipoDeMiel) {
        case '1':
            $entradaysalida_tabla = 'entradaysalida';
            $personaldescarga_tabla = 'personaldescarga';
            break;
        case '2':
            $entradaysalida_tabla = 'entradaysalida_organico';
            $personaldescarga_tabla = 'personaldescarga_organico';
            break;
        default:
            throw new Exception('El tipo de miel no es válido');
            break;
    }


    $sqlSelecciona = $con->prepare("SELECT idDescripcion, idCondicion, idExternoTambor, idPersonal
    FROM $entradaysalida_tabla WHERE idReporte = :idReporte");
    $sqlSelecciona->bindParam(':idReporte', $reporte->idReporte);
    $sqlSelecciona->bindColumn('idDescripcion', $reporte->idDescripcion);
    $sqlSelecciona->bindColumn('idCondicion', $reporte->idCondicion);
    $sqlSelecciona->bindColumn('idExternoTambor', $reporte->idExternoTambor);
    $sqlSelecciona->bindColumn('idPersonal', $reporte->idPersonal);

    $sqlSelecciona->execute();
    if ($sqlSelecciona == false) {
        throw new Exception($con->errorInfo());
    } else {
        $sqlSelecciona->fetch(PDO::FETCH_BOUND);
    }

    // Verificar que existan y sean válidas las propiedades
    if (!isset($reporte->idDescripcion) && !$reporte->idDescripcion) {
        throw new Exception('No se obtuvo la descripción');
    }

    if (!isset($reporte->idCondicion) && !$reporte->idCondicion) {
        throw new Exception('No se obtuvo la condición');
    }

    if (!isset($reporte->idExternoTambor) && !$reporte->idExternoTambor) {
        throw new Exception('No se obtuvo el dato "idExternoTambor"');
    }

    if (!isset($reporte->idPersonal) && !$reporte->idPersonal) {
        throw new Exception('No se obtuvo el personal');
    }

    // Cambiar el estado de los tambores a 2 (lote interno)

    // Eliminar de la tabla descripiciones
    $sqlDeleteDescripciones = $con->prepare("DELETE FROM descripciones WHERE idDescripcion = :idDescripcion");
    $sqlDeleteDescripciones->bindParam(':idDescripcion', $reporte->idDescripcion);
    $sqlDeleteDescripciones->execute();
    if ($sqlDeleteDescripciones == false) {
        throw new Exception($coN->errrInfo());
    }
    // Eliminar de la tabla condicionesunidad
    $sqlDeleteDescripciones = $con->prepare("DELETE FROM condicionesunidad WHERE idCondicion = :idCondicion");
    $sqlDeleteDescripciones->bindParam(':idCondicion', $reporte->idCondicion);
    $sqlDeleteDescripciones->execute();
    if ($sqlDeleteDescripciones == false) {
        throw new Exception($coN->errrInfo());
    }
    // Eliminar de la tabla externotambores
    $sqlDeleteDescripciones = $con->prepare("DELETE FROM externotambores WHERE idExternoTambor = :idExternoTambor");
    $sqlDeleteDescripciones->bindParam(':idExternoTambor', $reporte->idExternoTambor);
    $sqlDeleteDescripciones->execute();
    if ($sqlDeleteDescripciones == false) {
        throw new Exception($coN->errrInfo());
    }
    // Eliminar de la tabla personalacciones
    $sqlDeleteDescripciones = $con->prepare("DELETE FROM personalacciones WHERE idPersonal = :idPersonal");
    $sqlDeleteDescripciones->bindParam(':idPersonal', $reporte->idPersonal);
    $sqlDeleteDescripciones->execute();
    if ($sqlDeleteDescripciones == false) {
        throw new Exception($coN->errrInfo());
    }
    // Eliminar de la tabla entradaysalida
    $sqlDeleteDescripciones = $con->prepare("DELETE FROM $entradaysalida_tabla WHERE idReporte = :idReporte");
    $sqlDeleteDescripciones->bindParam(':idReporte', $reporte->idReporte);
    $sqlDeleteDescripciones->execute();
    if ($sqlDeleteDescripciones == false) {
        throw new Exception($coN->errrInfo());
    }

    // Eliminar de la tabla personaldescarga_tabla
    $sqlDeleteDescripciones = $con->prepare("DELETE FROM $personaldescarga_tabla WHERE idReporte = :idReporte");
    $sqlDeleteDescripciones->bindParam(':idReporte', $reporte->idReporte);
    $sqlDeleteDescripciones->execute();
    if ($sqlDeleteDescripciones == false) {
        throw new Exception($coN->errrInfo());
    }

    $con->commit(); // Cerramos la transacción para que haga los cambios
    echo json_encode(['error' => false, 'message' => 'Se ha eliminado el reporte de carga.']);
} catch (Exception $e) {
    // Hacemos un Rollback a la transacción
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}