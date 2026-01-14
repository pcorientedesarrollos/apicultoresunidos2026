<?php
include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$postdata = file_get_contents('php://input');

try {

    if(!$postdata) {
        throw new Exception('No se recibieron datos');
    } else {
        $infoProyeccion = json_decode($postdata);
    }

    // iniciamos una transaccion

    $con->beginTransaction();

    // ACTUALIZAMOS EL ENCABEZADO

    $sqlInsertaEncabezado = "UPDATE proyeccionencabezado
    SET nombre = :nombre, inicio = :inicio, fin = :fin, totalProyeccion = :totalProyeccion
    WHERE idProyeccion = :idProyeccion";

    $queryInsertaEncabezado = $con->prepare($sqlInsertaEncabezado);
    $queryInsertaEncabezado->bindParam(':nombre', $infoProyeccion->nombre);
    $queryInsertaEncabezado->bindParam(':inicio', $infoProyeccion->inicio);
    $queryInsertaEncabezado->bindParam(':fin', $infoProyeccion->fin);
    $queryInsertaEncabezado->bindParam(':totalProyeccion', $infoProyeccion->totalProyeccion);
    $queryInsertaEncabezado->bindParam(':idProyeccion', $infoProyeccion->idProyeccion);
    $queryInsertaEncabezado->execute();

    if(!$queryInsertaEncabezado) {
        throw new Exception($con->errorInfo());
    }

    // guardamos el id de la proyeccion en una variable

    $idProyeccion = $infoProyeccion->idProyeccion;

    // Ahora, por cada zona insertar detale

    foreach($infoProyeccion->listaZonas as $zona) {
        $sqlInsertaDetalle = "UPDATE proyecciondetalle SET proyeccion = :proyeccion, kilogramos = :kilogramos
        WHERE idProyeccionDetalle = :idProyeccionDetalle";
        $queryInsertaDetalle = $con->prepare($sqlInsertaDetalle);
        $queryInsertaDetalle->bindParam(':proyeccion', $zona->proyeccion);
        $queryInsertaDetalle->bindParam(':kilogramos', $zona->kilogramos);
        $queryInsertaDetalle->bindParam(':idProyeccionDetalle', $zona->idProyeccionDetalle);
        $queryInsertaDetalle->execute();

        if(!$queryInsertaDetalle) {
            throw new Exception($con->errorInfo());
        }
    }

    // Si llega a este punto, es que no hubo algun error

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha actualizado la información de la proyección']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
