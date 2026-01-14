<?php

include_once '../../../DAOConeccion/conePDO.php';
include_once '../../php/nombreDePersona.php';
$pdo = new conePDO();
$con = $pdo->conectar();
date_default_timezone_set("America/Merida");

if (isset($_GET['prestamoDePersonal'])) {
    $post = json_decode(file_get_contents('php://input'));
    try {
        $con->beginTransaction();
        $seleccinaPrestamos = $con->prepare("SELECT idPrestamo, fecha, tipoDePersona,
                                                idNombre, cantidad, dacc FROM prestamos
                                                WHERE tipoDePersona = :tipoDePersona AND idNombre = :idNombre
                                                ORDER BY fecha DESC, hora DESC");
        $seleccinaPrestamos->bindParam(':tipoDePersona', $post->tipoDePersona);
        $seleccinaPrestamos->bindParam(':idNombre', $post->idNombre);
        $seleccinaPrestamos->execute();
        $resultado = array();
        $encabezado = array();
        $encabezado['nombreDePersonal'] = retornarNombre($con, $post->tipoDePersona, $post->idNombre);
        if ($seleccinaPrestamos->rowCount() >= 1) {
            foreach ($seleccinaPrestamos->fetchAll(PDO::FETCH_ASSOC) as $prestamo) {
                $prestamo['nombre'] = retornarNombre($con, $prestamo['tipoDePersona'], $prestamo['idNombre']);
                $prestamo['dacc'] = $prestamo['dacc'] == 0 ? 'En curso' : 'Liquidado';

                $seleccionarDetalle = $con->prepare("SELECT * FROM prestamodetalle WHERE idPrestamo = :idPrestamo ORDER BY fecha DESC, hora DESC");
                $seleccionarDetalle->bindParam(':idPrestamo', $prestamo['idPrestamo']);
                $seleccionarDetalle->execute();

                if ($seleccionarDetalle->rowCount() >= 1) {
                    $prestamo['detalle'] = $seleccionarDetalle->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $prestamo['detalle'] = [];
                }

                $prestamo['totalAbono'] = 0;
                foreach ($prestamo['detalle'] as $detalle) {
                    $prestamo['totalAbono'] += $detalle['cantidad'];
                }
                $prestamo['restante'] = $prestamo['cantidad'] - $prestamo['totalAbono'];
                
                array_push($resultado, $prestamo);
            }
            
            $con->commit();
            echo json_encode(['error'=>false, 'message'=>'Query executed successfully!' , 'content'=>$resultado, 'header'=>$encabezado]);
        } else {
            $con->commit();
            echo json_encode(['error'=>true, 'message'=>'No hay prestamos para este personal!' , 'content'=>$resultado, 'header'=>$encabezado]);
        }
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'content'=>[]]);
    }
} elseif (isset($_GET['prestamosPorPersonal'])) {
    try {
        $con->beginTransaction();

        $seleccinaPrestamos = $con->prepare("SELECT tipoDePersona, idNombre, dacc, COUNT(*) RecordsPerGroup FROM prestamos
                                                GROUP BY tipoDePersona, idNombre
                                                ORDER BY fecha DESC, hora DESC");
        $seleccinaPrestamos->execute();
        $resultado = array();
        foreach ($seleccinaPrestamos->fetchAll(PDO::FETCH_ASSOC) as $prestamo) {
            $prestamo['nombre'] = retornarNombre($con, $prestamo['tipoDePersona'], $prestamo['idNombre']);
            array_push($resultado, $prestamo);
        }

        $con->commit();
        echo json_encode(['error'=>false, 'message'=>'Query executed successfully!' , 'content'=>$resultado]);
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'content'=>[]]);
    }
} else {
    exit();
}
