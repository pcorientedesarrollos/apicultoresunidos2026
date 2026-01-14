<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$idEntradaMP = file_get_contents('php://input');

try {
    // Hacer una transacción en caso de haber errores
    $con->beginTransaction();

    if (!$idEntradaMP || !isset($_GET['eliminarReporte'])) {
        throw new Exception('No se recibió ningún dato.');
    }


    // Eliminar del encabezado
    $sqlDeleteEncabzado = $con->prepare("DELETE FROM materiaprimaencabezadoentradas WHERE idEntradaMateria = :idEntradaMateria");
    $sqlDeleteEncabzado->bindParam(':idEntradaMateria', $idEntradaMP);

    $sqlDeleteEncabzado->execute();
    if ($sqlDeleteEncabzado == false) {
        throw new Exception($con->errorInfo());
    }

    // Eliminar el detalle

    $sqlDeleteEncabzado = $con->prepare("DELETE FROM materiaprimadetalleentradas WHERE idEntradaMateria = :idEntradaMateria");
    $sqlDeleteEncabzado->bindParam(':idEntradaMateria', $idEntradaMP);

    $sqlDeleteEncabzado->execute();
    if ($sqlDeleteEncabzado == false) {
        throw new Exception($con->errorInfo());
    }


    $con->commit(); // Cerramos la transacción para que haga los cambios
    echo json_encode(['error' => false, 'message' => 'Se ha eliminado la entrada.']);
} catch (Exception $e) {
    // Hacemos un Rollback a la transacción
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}