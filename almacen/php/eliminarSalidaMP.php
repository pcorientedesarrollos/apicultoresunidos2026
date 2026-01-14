<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$idSalidaMP = file_get_contents('php://input');

try {
    // Hacer una transacción en caso de haber errores
    $con->beginTransaction();

    if (!$idSalidaMP || !isset($_GET['eliminarReporte'])) {
        throw new Exception('No se recibió ningún dato.');
    }


    // Eliminar del encabezado
    $sqlDeleteEncabzado = $con->prepare("DELETE FROM materiaprimaencabezadosalidas WHERE idSalidaMateria = :idSalidaMateria");
    $sqlDeleteEncabzado->bindParam(':idSalidaMateria', $idSalidaMP);

    $sqlDeleteEncabzado->execute();
    if ($sqlDeleteEncabzado == false) {
        throw new Exception($con->errorInfo());
    }

    // Eliminar el detalle

    $sqlDeleteEncabzado = $con->prepare("DELETE FROM materiaprimadetallesalidas WHERE idSalidaMateria = :idSalidaMateria");
    $sqlDeleteEncabzado->bindParam(':idSalidaMateria', $idSalidaMP);

    $sqlDeleteEncabzado->execute();
    if ($sqlDeleteEncabzado == false) {
        throw new Exception($con->errorInfo());
    }


    $con->commit(); // Cerramos la transacción para que haga los cambios
    echo json_encode(['error' => false, 'message' => 'Se ha eliminado la salida.']);
} catch (Exception $e) {
    // Hacemos un Rollback a la transacción
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}