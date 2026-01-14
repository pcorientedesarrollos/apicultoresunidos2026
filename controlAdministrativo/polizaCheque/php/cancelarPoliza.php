<?php

$post = json_decode(file_get_contents('php://input'));

date_default_timezone_set("America/Merida");

if ($post) {
    include_once '../../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();

    try {
        $con->beginTransaction();

        $eliminarPoliza = $con->prepare("DELETE FROM polizacheque WHERE idPolizaCheque = :idPoliza");
        $eliminarPoliza->bindParam(':idPoliza', $post->idPoliza);
        $eliminarPoliza->execute();
        if ($eliminarPoliza->rowCount() < 1) {
            throw new Exception('No se encontró alguna fila afectada en la tabla de pólizas');
        }

        $eliminarAuxiliar = $con->prepare("DELETE FROM auxiliardebancos WHERE idPolizaCheque = :idPoliza");
        $eliminarAuxiliar->bindParam(':idPoliza', $post->idPoliza);
        $eliminarAuxiliar->execute();
        if ($eliminarAuxiliar->rowCount() < 1) {
            throw new Exception('No se puede actualizar el auxiliar de bancos');
        }

        $eliminarRelacion = $con->prepare("DELETE FROM relaciondemovimientos WHERE poliza = :idPoliza");
        $eliminarRelacion->bindParam(':idPoliza', $post->idPoliza);
        $eliminarRelacion->execute();
        if ($eliminarRelacion->rowCount() < 1) {
            throw new Exception('No se puede actualizar el auxiliar de bancos');
        }

        $revisarEnCajaChica = $con->prepare("SELECT idCajaChica FROM cajachica WHERE idPolizaCheque = :idPoliza");
        $revisarEnCajaChica->bindParam(':idPoliza', $post->idPoliza);
        $revisarEnCajaChica->execute();

        if ($revisarEnCajaChica->rowCount() >= 1) {
            $revisarEnCajaChica->bindColumn('idCajaChica', $_idCajaChica);
            $revisarEnCajaChica->fetch(PDO::FETCH_BOUND);
            $eliminarMovimientoCajaChica = $con->prepare("DELETE FROM cajachica WHERE idPolizaCheque = :idPoliza");
            $eliminarMovimientoCajaChica->bindParam(':idPoliza', $post->idPoliza);
            $eliminarMovimientoCajaChica->execute();
            if ($eliminarMovimientoCajaChica->rowCount() < 1) {
                throw new Exception('No se ha podido cancelar correctamente el encabazado de caja chica');
            }
            $eliminarMovimientoCajaChicaDetalle = $con->prepare("DELETE FROM cajachicadetalle WHERE idCajaChica = :idCajaChica");
            $eliminarMovimientoCajaChicaDetalle->bindParam(':idCajaChica', $_idCajaChica);
            $eliminarMovimientoCajaChicaDetalle->execute();
            if ($eliminarMovimientoCajaChicaDetalle->rowCount() < 1) {
                throw new Exception('No se ha podido cancelar correctamente en el detalle de caja chica');
            }
        }

        $con->commit();
        echo json_encode(['error'=>false, 'message'=>'Se ha cancelado la póliza', 'swal'=>'success']);
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode(['error'=>true, 'message'=>'Excepción capturada: ' . $e->getMessage(), 'swal'=>'error']);
    }
} else {
    exit();
}
