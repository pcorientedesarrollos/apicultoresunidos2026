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
        if (isset($_GET['derivados'])) {
            $tabla = 'saldoinicial_derivados';
        } else  if (isset($_GET['miel'])) {
            $tabla = 'saldoinicial_miel';
        } else {
            $tabla = 'saldoinicial_apicola';
        }
    }

    if (isset($datos_inventario->id)) {

        $query = $con->prepare("UPDATE $tabla SET existenciaPasada = :existenciapasada, importePasado = :importepasado WHERE id = :id");

        $query->bindParam(':id', $datos_inventario->id);
        $query->bindParam(':existenciapasada', $datos_inventario->existenciaPasada);
        $query->bindParam(':importepasado', $datos_inventario->importePasado);

        $query->execute();
    } else {
        $query = $con->prepare("INSERT INTO $tabla (nombre, existenciaPasada, importePasado) 
        VALUES (:nombre, :existenciapasada, :importepasado)");

        $query->bindParam(':nombre', $datos_inventario->nombre);
        $query->bindParam(':existenciapasada', $datos_inventario->existenciaPasada);
        $query->bindParam(':importepasado', $datos_inventario->importePasado);

        $query->execute();
    }

    if (!$query) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'message' => 'Se ha guardado los saldos del inventario']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
