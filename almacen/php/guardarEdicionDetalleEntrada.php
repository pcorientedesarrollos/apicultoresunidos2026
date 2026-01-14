<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$json = file_get_contents("php://input");
$idEntradaMateria = $_GET["idEntradaMateria"];

try {
    $con->beginTransaction();
    if (!$json) {
        throw new Exception('No se recibieron los parámetros');
    } else {
        $datos = json_decode($json);
        $info = $datos->valor;
    }

    if (isset($_GET['tipoDeMiel'])) {
        switch ($_GET['tipoDeMiel']) {
            case '1':
                $detalle = 'materiaprimadetalleentradas';
                break;
            case '2':
                $detalle = 'materiaprimadetalleentradas_organico';
                break;
                
            case '7':
                $detalle = 'materiaprimadetalleentradas_naranjo';
                break;
            default:
                throw new Exception('Tipo de miel inválido');
                break;
        }
    }

    $sql = "INSERT INTO $detalle (idEntradaMateria, cantidad, descripcion, precioUnitario, importe, idMovimiento, movimiento, idSubcuenta, subcuenta, idConcepto, concepto) 
                VALUES (:idEntradaMateria, :cantidad, :descripcion, :precioUnitario, :importe, :idMovimiento, :movimiento, :idSubcuenta, :subcuenta, :idConcepto, :concepto)";
    $insertarDetalle = $con->prepare($sql);
    $insertarDetalle->bindParam(':idEntradaMateria', $idEntradaMateria);
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
