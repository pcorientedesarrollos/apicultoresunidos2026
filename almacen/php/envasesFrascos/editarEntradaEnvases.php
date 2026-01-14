<?php
include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['entrada'])) {
    $json = file_get_contents("php://input");
    $idDetalleEntrada = $_GET["idDetalleEntrada"];

    try {
        $con->beginTransaction();
        if (!$json) {
            throw new Exception('No se recibieron los parámetros');
        } else {
            $datos = json_decode($json);
            $info = $datos->valor;
        }

        $sql = "UPDATE envasesfrascosdetalleentradas SET cantidad = :cantidad, descripcion = :descripcion, precioUnitario = :precioUnitario, importe = :importe,
        idMovimiento = :idMovimiento, movimiento = :movimiento, idSubcuenta = :idSubcuenta, subcuenta = :subcuenta, idConcepto = :idConcepto, concepto = :concepto
        WHERE idDetalleEntrada = :idDetalleEntrada";

        $insertarDetalle = $con->prepare($sql);
        $insertarDetalle->bindParam(':cantidad', $info->cantidad);
        $insertarDetalle->bindParam(':descripcion', $info->descripcion);
        $insertarDetalle->bindParam(':precioUnitario', $info->precioUnitario);
        $insertarDetalle->bindParam(':importe', $info->importe);
        $insertarDetalle->bindParam(':idDetalleEntrada', $idDetalleEntrada);

        // Nuevas cuentas!
        $insertarDetalle->bindParam(':idMovimiento', $info->idMovimiento);
        $insertarDetalle->bindParam(':movimiento', $info->movimiento);
        $insertarDetalle->bindParam(':idSubcuenta', $info->idSubcuenta);
        $insertarDetalle->bindParam(':subcuenta', $info->subcuenta);
        $insertarDetalle->bindParam(':idConcepto', $info->idConcepto);
        $insertarDetalle->bindParam(':concepto', $info->concepto);

        $insertarDetalle->execute();
        if ($insertarDetalle == false) {
            throw new Exception($con->errorInfo());
        }
        $con->commit();
        echo json_encode(['error' => false, 'message' => 'Se han guardado los datos', 'swal' => 'success']);
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine(), 'swal' => 'error']);
        exit();
    }
} else if (isset($_GET['salida'])) {
    $json = file_get_contents("php://input");
    $idDetalleSalida = $_GET["idDetalleSalida"];

    try {
        $con->beginTransaction();
        if (!$json) {
            throw new Exception('No se recibieron los parámetros');
        } else {
            $datos = json_decode($json);
            $info = $datos->valor;
        }

        $sql = "UPDATE envasesfrascosdetallesalidas SET cantidad = :cantidad, descripcion = :descripcion, precioUnitario = :precioUnitario, importe = :importe,
        idMovimiento = :idMovimiento, movimiento = :movimiento, idSubcuenta = :idSubcuenta, subcuenta = :subcuenta, idConcepto = :idConcepto, concepto = :concepto
        WHERE idDetalleSalida = :idDetalleSalida";

        $insertarDetalle = $con->prepare($sql);
        // $insertarDetalle->bindParam(':tipo', $info->tipo);
        $insertarDetalle->bindParam(':cantidad', $info->cantidad);
        $insertarDetalle->bindParam(':descripcion', $info->descripcion);
        $insertarDetalle->bindParam(':precioUnitario', $info->precioUnitario);
        $insertarDetalle->bindParam(':importe', $info->importe);
        $insertarDetalle->bindParam(':idDetalleSalida', $idDetalleSalida);

        // Nuevas cuentas!
        $insertarDetalle->bindParam(':idMovimiento', $info->idMovimiento);
        $insertarDetalle->bindParam(':movimiento', $info->movimiento);
        $insertarDetalle->bindParam(':idSubcuenta', $info->idSubcuenta);
        $insertarDetalle->bindParam(':subcuenta', $info->subcuenta);
        $insertarDetalle->bindParam(':idConcepto', $info->idConcepto);
        $insertarDetalle->bindParam(':concepto', $info->concepto);


        $insertarDetalle->execute();
        if ($insertarDetalle == false) {
            throw new Exception($con->errorInfo());
        }
        $con->commit();
        echo json_encode(['error' => false, 'message' => 'Se han guardado los datos', 'swal' => 'success']);
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine(), 'swal' => 'error']);
        exit();
    }
}
