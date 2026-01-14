<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$post = file_get_contents('php://input');

try {
    if (!$post) {
        throw new Exception('No se recibieron datos');
    } else {
        $cliente = json_decode($post);
    }

    if (isset($cliente->idCliente)) {
        $idCliente = $cliente->idCliente;
        $success_message = 'Se ha actualizado la información';
        // Consulta para actualizar el registro
        $sql = $con->prepare("UPDATE clientes SET nombre = :nombre, domicilio = :domicilio,
        telefono = :telefono, estado = :estado, flujoEfectivo = :flujoEfectivo
        WHERE idCliente = :idCliente");
        $sql->bindParam(':idCliente', $cliente->idCliente);
    } else {
        $success_message = 'Se ha guardado el registro';
        // Consulta para crear el registro
        $sql = $con->prepare("INSERT INTO clientes (fechaRegistro, nombre, domicilio, telefono,
        estado, flujoEfectivo) VALUES (CURRENT_DATE(), :nombre, :domicilio, :telefono, :estado, :flujoEfectivo)");
    }

    $sql->bindParam(':nombre', $cliente->nombre);
    $sql->bindParam(':domicilio', $cliente->domicilio);
    $sql->bindParam(':telefono', $cliente->telefono);
    $sql->bindParam(':estado', $cliente->estado);
    $sql->bindParam(':flujoEfectivo', $cliente->flujoEfectivo);

    $sql->execute();
    if (!$sql) {
        throw new Exception($con->errorInfo());
    }

    if (isset($cliente->idCliente)) {
        $idCliente = $cliente->idCliente;
    } else {
        $idCliente = $con->lastInsertId();
    }


    echo json_encode(['error' => false, 'message' => $success_message, 'idCliente' => $idCliente]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
