<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

date_default_timezone_set('America/Merida');

$post = json_decode(file_get_contents('php://input'));
if ($post) {
    $abono = $post->datosNuevoAbono;
    $prestamo = $post->prestamo;
    try {
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $con->beginTransaction();
        
        $guardarNuevoAbono = $con->prepare("INSERT INTO prestamodetalle (idPrestamo, fecha, hora, cantidad)
                                                VALUES (:idPrestamo, :fecha, :hora, :cantidad)");
        $_idPrestamo = $abono->idPrestamo;
        $_fecha = $abono->fecha;
        $_hora = date("H:i:s");
        $_cantidad = $abono->cantidad;
        
        $guardarNuevoAbono->bindParam(':idPrestamo', $_idPrestamo);
        $guardarNuevoAbono->bindParam(':fecha', $_fecha);
        $guardarNuevoAbono->bindParam(':hora', $_hora);
        $guardarNuevoAbono->bindParam(':cantidad', $_cantidad);
        $guardarNuevoAbono->execute();
    
        if ($guardarNuevoAbono->rowCount() != 1) {
            throw new Exception('No se pudo guardar el abono');
        }

        if ($abono->cantidad == $prestamo->restante) {
            $actualizarPrestamo = $con->prepare("UPDATE prestamos SET dacc = 1, fechaLiquidacion = :fecha WHERE idPrestamo = :idPrestamo");
            $actualizarPrestamo->bindParam(':fecha', $_fecha);
            $actualizarPrestamo->bindParam(':idPrestamo', $_idPrestamo);
            $actualizarPrestamo->execute();
    
            if ($actualizarPrestamo->rowCount() != 1) {
                throw new Exception('No se actualizó el estado del préstamo');
            } else {
                $con->commit();
                echo json_encode(['error'=>false, 'message'=>'Se guardó el abono y se liquidó el saldo', 'swal'=>'success']);
            }
        } else {
            $con->commit();
            echo json_encode(['error'=>false, 'message'=>'Se guardó el abono', 'swal'=>'success']);
        }
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'swal'=>'error']);
    }
} else {
    exit();
}
