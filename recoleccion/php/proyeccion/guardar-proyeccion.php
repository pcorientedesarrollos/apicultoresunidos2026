<?php
include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$postdata = file_get_contents('php://input');

try {

    if (!$postdata) {
        throw new Exception('No se recibieron datos');
    } else {
        $infoProyeccion = json_decode($postdata);
        switch ($infoProyeccion->tipoDeMiel) {
            case '1':
                $proyeccionencabezado = 'proyeccionencabezado';
                $proyecciondetalle = 'proyecciondetalle';
                break;
            case '2':
                $proyeccionencabezado = 'proyeccionencabezado_organico';
                $proyecciondetalle = 'proyecciondetalle_organico';
                break;
        }
    }

    // iniciamos una transaccion

    $con->beginTransaction();


    // Primero insertar el encabezado para obtener el ID

    $sqlInsertaEncabezado = "INSERT INTO $proyeccionencabezado (nombre, inicio, fin, totalProyeccion)
    VALUES (:nombre, :inicio, :fin, :totalProyeccion)";

    $queryInsertaEncabezado = $con->prepare($sqlInsertaEncabezado);
    $queryInsertaEncabezado->bindParam(':nombre', $infoProyeccion->nombre);
    $queryInsertaEncabezado->bindParam(':inicio', $infoProyeccion->inicio);
    $queryInsertaEncabezado->bindParam(':fin', $infoProyeccion->fin);
    $queryInsertaEncabezado->bindParam(':totalProyeccion', $infoProyeccion->totalProyeccion);
    $queryInsertaEncabezado->execute();

    if (!$queryInsertaEncabezado) {
        throw new Exception($con->errorInfo());
    }

    // Si se inserta podemos obtener el id

    $idProyeccion = $con->lastInsertId();

    // Ahora, por cada zona insertar detale

    foreach ($infoProyeccion->listaZonas as $zona) {
        $sqlInsertaDetalle = "INSERT INTO $proyecciondetalle (idProyeccion, idZona, proyeccion, kilogramos)
        VALUES (:idProyeccion, :idZona, :proyeccion, :kilogramos)";
        $queryInsertaDetalle = $con->prepare($sqlInsertaDetalle);
        $queryInsertaDetalle->bindParam(':idProyeccion', $idProyeccion);
        $queryInsertaDetalle->bindParam(':idZona', $zona->idzona);
        $queryInsertaDetalle->bindParam(':proyeccion', $zona->proyeccion);
        $queryInsertaDetalle->bindParam(':kilogramos', $zona->kilogramos);
        $queryInsertaDetalle->execute();

        if (!$queryInsertaDetalle) {
            throw new Exception($con->errorInfo());
        }
    }

    // Si llega a este punto, es que no hubo algun error

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha guardado una nueva proyección']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
