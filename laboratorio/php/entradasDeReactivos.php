<?php

include_once '../../DAOConeccion/conePDO.php';
include_once '../../controlAdministrativo/php/nombreDePersona.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {

    $sql = "SELECT ere.*, erd.*, p.nombreProveedor
    FROM reactivosentradaencabezado ere
    LEFT JOIN reactivosentradadetalle erd ON ere.idEntrada = erd.idEntrada
    LEFT JOIN proveedoresmantto p ON p.idProveedorMantto = ere.idProveedor";

    if (isset($_GET['mes'])) {
        $mes = $_GET['mes'];
        $sql .= " WHERE SUBSTR(ere.fecha FROM 6 FOR 2) = " . $mes;
    }

    $sql .= " GROUP BY ere.idEntrada ORDER BY ere.idEntrada DESC";

    $datos = $con->prepare($sql);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }

    $resultado = $datos->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => []]);
    exit();
}