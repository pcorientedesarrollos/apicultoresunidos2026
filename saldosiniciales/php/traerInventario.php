<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    if (!isset($_GET['id'])) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $id_inventario = $_GET['id'];
    }

    $registro = new stdClass();

    $query = $con->prepare("SELECT id, nombre, existenciaPasada, importeAcumuladoPasado FROM saldoinicialinventario
    WHERE id = :id");
    $query->bindParam(':id', $id_inventario);
    $query->execute();
    if (!$query) {
        throw new Exception($con->errorInfo() || 'Error en el query');
    }

    // Convertir el nombre de los registros (quitar '_')
    $registro = $query->fetch(PDO::FETCH_ASSOC);
    $registro['nombre'] = strtoupper(join(' ', explode('_', $registro['nombre'])));

    echo json_encode(['error' => false, 'data' => $registro]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
