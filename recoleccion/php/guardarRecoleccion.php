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

    // Ingresar los datos a las tablas: recoleccion, recoleccionpersonal y recolecciontransporte y nuevo: recoleccionencabezado y recoleccionoperador
    $sqlInsertaRecoleccionEncabezado = "INSERT INTO recoleccionencabezado (fecha, observaciones, totalTambores, totalImporteCompra, totalPrecioPromedio) VALUES (:fecha, :observaciones, :totalTambores, :totalImporteCompra, :totalPrecioPromedio)";
    $sqlInsertarRecoleccion = "INSERT INTO recoleccion (idRecoleccion, idLocalidad, anterior, nuevo, recoleccion, total,
    precio, humedad, importe) VALUES (:idRecoleccion, :idLocalidad, :anterior, :nuevo, :recoleccion, :total, :precio, :humedad, :importe)";
    $sqlInsertaPersonal = "INSERT INTO recoleccionpersonal VALUES (:idRecoleccion, '0', :nombre)";
    $sqlInsertaTransporte = "INSERT INTO recolecciontransporte VALUES (:idRecoleccion, :idTransporte)";
    $sqlInsertaOperador = "INSERT INTO recoleccionoperador VALUES (:idRecoleccion, :idPersonal)";

    // Insertar la recoleccion y obtener el id para guardar personal y transporte y detalle
    $con->beginTransaction();

    $queryInsertaEncabezado = $con->prepare($sqlInsertaRecoleccionEncabezado);
    $queryInsertaEncabezado->bindParam(':fecha', $objetoRecoleccion->fecha);
    $queryInsertaEncabezado->bindParam(':observaciones', $objetoRecoleccion->observaciones);
    $queryInsertaEncabezado->bindParam(':totalTambores', $objetoRecoleccion->totalTambores);
    $queryInsertaEncabezado->bindParam(':totalImporteCompra', $objetoRecoleccion->totalImporteCompra);
    $queryInsertaEncabezado->bindParam(':totalPrecioPromedio', $objetoRecoleccion->totalPrecioPromedio);
    $queryInsertaEncabezado->execute();
    if (!$queryInsertaEncabezado) {
        throw new Exception($con->errorInfo());
    }
    $idRecoleccion = $con->lastInsertId();

    // Insertar cada detalle
    foreach ($objetoRecoleccion->localidades as $localidad) {
        $queryInsertaPersonal = $con->prepare($sqlInsertarRecoleccion);
        $queryInsertaPersonal->bindParam(':idRecoleccion', $idRecoleccion);
        $queryInsertaPersonal->bindParam(':idLocalidad', $localidad->idLocalidad);
        $queryInsertaPersonal->bindParam(':anterior', $localidad->anterior);
        $queryInsertaPersonal->bindParam(':nuevo', $localidad->nuevo);
        $queryInsertaPersonal->bindParam(':recoleccion', $localidad->recoleccion);
        $queryInsertaPersonal->bindParam(':total', $localidad->total);
        $queryInsertaPersonal->bindParam(':precio', $localidad->precio);
        $queryInsertaPersonal->bindParam(':humedad', $localidad->humedad);
        $queryInsertaPersonal->bindParam(':importe', $localidad->importe);
        $queryInsertaPersonal->execute();
        if (!$queryInsertaPersonal) {
            throw new Exception($con->errorInfo());
        }
    }

    // Insertar cada personal
    foreach ($objetoRecoleccion->personal as $idPersonal) {
        $queryInsertaPersonal = $con->prepare($sqlInsertaPersonal);
        $queryInsertaPersonal->bindParam(':idRecoleccion', $idRecoleccion);
        // $queryInsertaPersonal->bindParam(':idPersonal', $idPersonal);
        $queryInsertaPersonal->bindParam(':nombre', $idPersonal->nombre);
        $queryInsertaPersonal->execute();
        if (!$queryInsertaPersonal) {
            throw new Exception($con->errorInfo());
        }
    }

    // Inserta cada transporte
    foreach ($objetoRecoleccion->transporte as $idTransporte) {
        $queryInsertaTransporte = $con->prepare($sqlInsertaTransporte);
        $queryInsertaTransporte->bindParam(':idRecoleccion', $idRecoleccion);
        $queryInsertaTransporte->bindParam(':idTransporte', $idTransporte);
        $queryInsertaTransporte->execute();
        if (!$queryInsertaTransporte) {
            throw new Exception($con->errorInfo());
        }
    }

    // Insertar cada operador
    foreach ($objetoRecoleccion->operadores as $idOperador) {
        $queryInsertaOperador = $con->prepare($sqlInsertaOperador);
        $queryInsertaOperador->bindParam(':idRecoleccion', $idRecoleccion);
        $queryInsertaOperador->bindParam(':idPersonal', $idOperador);
        $queryInsertaOperador->execute();
        if (!$queryInsertaOperador) {
            throw new Exception($con->errorInfo());
        }
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha guardado la recolección']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. ' . $e->getLine()]);
}
