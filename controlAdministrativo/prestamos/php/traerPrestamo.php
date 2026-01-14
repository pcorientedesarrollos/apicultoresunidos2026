<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
include_once "./../../php/nombreDePersona.php";

date_default_timezone_set("America/Merida");
$post = json_decode(file_get_contents('php://input'));

if ($post) {
    try {
        $con->beginTransaction();

        $seleccinaPrestamo = $con->prepare("SELECT idPrestamo, fecha, tipoDePersona,
                                                idNombre, cantidad, dacc, fechaLiquidacion FROM prestamos WHERE idPrestamo = :idPrestamo ORDER BY fecha DESC, hora DESC");
        $seleccinaPrestamo->bindParam(':idPrestamo', $post);
        $seleccinaPrestamo->execute();
        if ($seleccinaPrestamo->rowCount() == 1) {
            $prestamo = $seleccinaPrestamo->fetch(PDO::FETCH_ASSOC);
            $resultado = array();
            $prestamo['nombre'] = retornarNombre($con, $prestamo['tipoDePersona'], $prestamo['idNombre']);
            $resultado['encabezado'] = $prestamo;
    
            $seleccionarDetalle = $con->prepare("SELECT * FROM prestamodetalle WHERE idPrestamo = :idPrestamo ORDER BY fecha DESC, hora DESC");
            $seleccionarDetalle->bindParam(':idPrestamo', $post);
            $seleccionarDetalle->execute();

            if ($seleccionarDetalle->rowCount() >= 1) {
                $resultado['detalle'] = $seleccionarDetalle->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $resultado['detalle'] = [];
            }

            $resultado['totalAbono'] = 0;
            foreach ($resultado['detalle'] as $detalle) {
                $resultado['totalAbono'] += $detalle['cantidad'];
            }
            $resultado['restante'] = $prestamo['cantidad'] - $resultado['totalAbono'];


    
            $con->commit();
            echo json_encode(['error'=>false, 'message'=>'Success' , 'content'=>$resultado]);
        } else {
            throw new Exception('No hay algún registro relacionado con el id recibido');
        }
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'content'=>[]]);
    }
} else {
    exit();
}
