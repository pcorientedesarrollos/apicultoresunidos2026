<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

date_default_timezone_set('America/Merida');

$post = json_decode(file_get_contents('php://input'));
if ($post) {
    try {
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $con->beginTransaction();

        $_fecha = $post->fecha;
        $_idMes = explode('-', $_fecha)[1];
        $_hora = $post->hora;
        $_tipoDePersona = $post->nombre->tipoDePersona;
        $_idNombre = $post->nombre->id;
        $_cantidad = $post->cantidad;

        $seleccionarMovimientosMensual = $con->prepare("SELECT tipo, total FROM cajachica WHERE idMEs = :idMes");
        $seleccionarMovimientosMensual->bindParam(':idMes', $_idMes);
        $seleccionarMovimientosMensual->execute();
    
        $saldoDelMes = 0;
        foreach ($seleccionarMovimientosMensual->fetchAll(PDO::FETCH_ASSOC) as $movimientoDelMes) {
            $saldoDelMes = $movimientoDelMes['tipo'] == 0
            ?$saldoDelMes += $movimientoDelMes['total']
            :$saldoDelMes -= $movimientoDelMes['total'];
        }

        if ($_cantidad > $saldoDelMes) {
            throw new Exception('El préstamo excede el saldo de caja chica');
        }
       
        $guardarNuevoPrestamo = $con->prepare("INSERT INTO prestamos (fecha, idMes, hora, tipoDePersona, idNombre, cantidad)
                                                VALUES (:fecha, :idMes, :hora, :tipoDePersona, :idNombre, :cantidad)");
        $guardarNuevoPrestamo->bindParam(':fecha', $_fecha);
        $guardarNuevoPrestamo->bindParam(':idMes', $_idMes);
        $guardarNuevoPrestamo->bindParam(':hora', $_hora);
        $guardarNuevoPrestamo->bindParam(':tipoDePersona', $_tipoDePersona);
        $guardarNuevoPrestamo->bindParam(':idNombre', $_idNombre);
        $guardarNuevoPrestamo->bindParam(':cantidad', $_cantidad);
        $guardarNuevoPrestamo->execute();
    
        if ($guardarNuevoPrestamo->rowCount() >= 1) {
            echo json_encode(['error'=>false, 'message'=>'Nuevo préstamo guardado', 'swal'=>'success']);
        } else {
            echo json_encode(['error'=>true, 'message'=>'No se pudo guardar el registro', 'swal'=>'warning']);
        }

        $con->commit();
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'swal'=>'error']);
    }
} else {
    exit();
}
