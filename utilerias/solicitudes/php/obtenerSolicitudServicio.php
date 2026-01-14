<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = new stdClass();
try {

    if (!isset($_GET['idSolicitudServicio'])) {
        throw new Exception('No se recibieron datos');
    } else {
        $idSolicitudServicio = $_GET['idSolicitudServicio'];
    }

    $sql = "SELECT al.*
            FROM solicitudservicio_encabezado al
            WHERE al.idSolicitudServicio = :idSolicitudServicio";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idSolicitudServicio', $idSolicitudServicio);
    $datos->execute();
    if ($datos == FALSE) {
        throw new ErrorException($mensaje, 0, $severidad, $fichero, $línea);
    }
    $resultado = $datos->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT c.*, p.nombreProveedor AS proveedor 
    FROM solicitudservicio_conceptos c 
    LEFT JOIN proveedoresmantto p ON p.idProveedorMantto = c.idProveedor
    WHERE c.idSolicitudServicio = :idSolicitudServicio";
    $sqlDetalle = $con->prepare($sql);
    $sqlDetalle->bindParam(':idSolicitudServicio', $idSolicitudServicio);
    $sqlDetalle->execute();

    if ($sqlDetalle == FALSE) {
        throw new ErrorException($mensaje, 0, $severidad, $fichero, $línea);
    } else {
        $resultado['conceptos'] = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => $resultado]);
    exit();
}
