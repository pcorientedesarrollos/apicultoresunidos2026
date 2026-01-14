<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$postdata = file_get_contents('php://input');
try {

    if (!$postdata) {
        throw new Exception('No se recibió datos');
    } else {
        $objetoRecoleccion = json_decode($postdata);
    }

    // Actualizar los datos en la tabla  recoleccionencabezado, eliminar y volver a registrar en recoleccionpersonal y recolecciontransporte y recoleccion y recoleccionoperador
    $sqlActualizaEncabezado = "UPDATE recoleccionencabezado SET fecha = :fecha, observaciones = :observaciones, totalTambores = :totalTambores, totalImporteCompra = :totalImporteCompra, totalPrecioPromedio = :totalPrecioPromedio WHERE idRecoleccion = :idRecoleccion";
    $sqlInsertarRecoleccion = "INSERT INTO recoleccion (idRecoleccion, idLocalidad, anterior, nuevo, recoleccion, total,
    precio, humedad, importe) VALUES (:idRecoleccion, :idLocalidad, :anterior, :nuevo, :recoleccion, :total, :precio, :humedad, :importe)";
    $sqlInsertaPersonal = "INSERT INTO recoleccionpersonal VALUES (:idRecoleccion, '0', :nombre)";
    $sqlInsertaTransporte = "INSERT INTO recolecciontransporte VALUES (:idRecoleccion, :idTransporte)";
    $sqlInsertaOperador = "INSERT INTO recoleccionoperador VALUES (:idRecoleccion, :idPersonal)";

    // Actualizar la recoleccion y obtener el id para guardar personal y transporte
    $con->beginTransaction();

    $queryActualizarEncabezado = $con->prepare($sqlActualizaEncabezado);
    $queryActualizarEncabezado->bindParam(':fecha', $objetoRecoleccion->fecha);
    $queryActualizarEncabezado->bindParam(':observaciones', $objetoRecoleccion->observaciones);
    $queryActualizarEncabezado->bindParam(':totalTambores', $objetoRecoleccion->totalTambores);
    $queryActualizarEncabezado->bindParam(':totalImporteCompra', $objetoRecoleccion->totalImporteCompra);
    $queryActualizarEncabezado->bindParam(':totalPrecioPromedio', $objetoRecoleccion->totalPrecioPromedio);
    $queryActualizarEncabezado->bindParam(':idRecoleccion', $objetoRecoleccion->idRecoleccion);
    $queryActualizarEncabezado->execute();
    if (!$queryActualizarEncabezado) {
        throw new Exception($con->errorInfo());
    }

    $idRecoleccion = $con->lastInsertId();

    // Eliminar localidades
    $queryEliminarLocalidades = $con->prepare("DELETE FROM recoleccion WHERE idRecoleccion = :idRecoleccion;");
    $queryEliminarLocalidades->bindParam(':idRecoleccion', $objetoRecoleccion->idRecoleccion);
    $queryEliminarLocalidades->execute();
    if (!$queryEliminarLocalidades) {
        throw new Exception($con->errorInfo());
    }

    // insertarlos de nuevo
    foreach ($objetoRecoleccion->localidades as $localidad) {
        $queryInsertaLocalidad = $con->prepare($sqlInsertarRecoleccion);
        $queryInsertaLocalidad->bindParam(':idRecoleccion', $objetoRecoleccion->idRecoleccion);
        $queryInsertaLocalidad->bindParam(':idLocalidad', $localidad->idLocalidad);
        $queryInsertaLocalidad->bindParam(':anterior', $localidad->anterior);
        $queryInsertaLocalidad->bindParam(':nuevo', $localidad->nuevo);
        $queryInsertaLocalidad->bindParam(':recoleccion', $localidad->recoleccion);
        $queryInsertaLocalidad->bindParam(':total', $localidad->total);
        $queryInsertaLocalidad->bindParam(':precio', $localidad->precio);
        $queryInsertaLocalidad->bindParam(':humedad', $localidad->humedad);
        $queryInsertaLocalidad->bindParam(':importe', $localidad->importe);
        $queryInsertaLocalidad->execute();
        if (!$queryInsertaLocalidad) {
            throw new Exception($con->errorInfo());
        }
    }

    // Eliminar
    $queryEliminarPersonal = $con->prepare("DELETE FROM recoleccionpersonal WHERE idRecoleccion = :idRecoleccion;");
    $queryEliminarPersonal->bindParam(':idRecoleccion', $objetoRecoleccion->idRecoleccion);
    $queryEliminarPersonal->execute();
    if (!$queryEliminarPersonal) {
        throw new Exception($con->errorInfo());
    }
    // Insertar cada personal
    foreach ($objetoRecoleccion->personal as $idPersonal) {
        $queryInsertaPersonal = $con->prepare($sqlInsertaPersonal);
        $queryInsertaPersonal->bindParam(':idRecoleccion', $objetoRecoleccion->idRecoleccion);
        // $queryInsertaPersonal->bindParam(':idPersonal', $idPersonal);
        $queryInsertaPersonal->bindParam(':nombre', $idPersonal->nombre);
        $queryInsertaPersonal->execute();
        if (!$queryInsertaPersonal) {
            throw new Exception($con->errorInfo());
        }
    }

    // Eliminar
    $queryEliminarTransportes = $con->prepare("DELETE FROM recolecciontransporte WHERE idRecoleccion = :idRecoleccion;");
    $queryEliminarTransportes->bindParam(':idRecoleccion', $objetoRecoleccion->idRecoleccion);
    $queryEliminarTransportes->execute();
    if (!$queryEliminarTransportes) {
        throw new Exception($con->errorInfo());
    }

    // Inserta cada transporte
    foreach ($objetoRecoleccion->transporte as $idTransporte) {
        $queryInsertaTransporte = $con->prepare($sqlInsertaTransporte);
        $queryInsertaTransporte->bindParam(':idRecoleccion', $objetoRecoleccion->idRecoleccion);
        $queryInsertaTransporte->bindParam(':idTransporte', $idTransporte);
        $queryInsertaTransporte->execute();
        if (!$queryInsertaTransporte) {
            throw new Exception($con->errorInfo());
        }
    }

    // Eliminar
    $queryEliminarPersonal = $con->prepare("DELETE FROM recoleccionoperador WHERE idRecoleccion = :idRecoleccion;");
    $queryEliminarPersonal->bindParam(':idRecoleccion', $objetoRecoleccion->idRecoleccion);
    $queryEliminarPersonal->execute();
    if (!$queryEliminarPersonal) {
        throw new Exception($con->errorInfo());
    }
    // Insertar cada operador
    foreach ($objetoRecoleccion->operadores as $idOperador) {
        $queryInsertaOperador = $con->prepare($sqlInsertaOperador);
        $queryInsertaOperador->bindParam(':idRecoleccion', $objetoRecoleccion->idRecoleccion);
        $queryInsertaOperador->bindParam(':idPersonal', $idOperador);
        $queryInsertaOperador->execute();
        if (!$queryInsertaOperador) {
            throw new Exception($con->errorInfo());
        }
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha editado el registro']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. ' . $e->getLine()]);
}
