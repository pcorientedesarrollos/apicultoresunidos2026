<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$postdata = file_get_contents('php://input');
try {

    if (!$postdata) {
        throw new Exception('No se recibieron los parámetros');
    } else {
        $datos_inventario = json_decode($postdata);
    }


    $query = $con->prepare("UPDATE saldoinicialinventario SET existenciaPasada = :existenciapasada, importeAcumuladoPasado = :importepasado WHERE id = :id");

    $query->bindParam(':id', $datos_inventario->id);
    $query->bindParam(':existenciapasada', $datos_inventario->existenciaPasada);
    $query->bindParam(':importepasado', $datos_inventario->importeAcumuladoPasado);

    $query->execute();
    if (!$query) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'message' => 'Se ha guardado los saldos del inventario']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
