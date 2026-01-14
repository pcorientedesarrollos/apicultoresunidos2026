<?php
include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$json = file_get_contents("php://input");
$idSalidaEnvases = $_GET["idSalidaEnvases"];

try {
    $con->beginTransaction();
    if (!$json) {
        throw new Exception('No se recibieron los parámetros');
    } else {
        $datos = json_decode($json);
        $info = $datos->valor;
    }

    $sql = "INSERT INTO envasesfrascosdetallesalidas (idSalidaEnvases, cantidad, descripcion, precioUnitario, importe, idMovimiento, movimiento, idSubcuenta, subcuenta, idConcepto, concepto) 
                VALUES (:idSalidaEnvases, :cantidad, :descripcion, :precioUnitario, :importe, :idMovimiento, :movimiento, :idSubcuenta, :subcuenta, :idConcepto, :concepto)";
    $insertarDetalle = $con->prepare($sql);
    $insertarDetalle->bindParam(':idSalidaEnvases', $idSalidaEnvases);
    $insertarDetalle->bindParam(':cantidad', $info->cantidad);
    $insertarDetalle->bindParam(':descripcion', $info->descripcion);
    $insertarDetalle->bindParam(':precioUnitario', $info->precioUnitario);
    $insertarDetalle->bindParam(':importe', $info->importe);
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
