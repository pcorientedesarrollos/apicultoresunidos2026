<?php

include_once '../../DAOConeccion/conePDO.php';
$post = json_decode(file_get_contents("php://input"));
$con = new conePDO();
$cn = $con->conectar();

try {

    if (!$post) {
        throw new Exception('No se recibieron parámetros');
    }

    $seleccionarClientes = $cn->prepare("SELECT * FROM clientes WHERE idCliente = :idCliente");
    $seleccionarClientes->bindParam(':idCliente', $post);
    $seleccionarClientes->execute();

    if ($seleccionarClientes->rowCount() >= 1) {
        echo json_encode(['error' => false, 'data' => $seleccionarClientes->fetch(PDO::FETCH_ASSOC)]);
    } else {
        throw new Exception('No hay clientes para mostrar');
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
