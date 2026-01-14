<?php

include_once '../../DAOConeccion/conePDO.php';
$post = json_decode(file_get_contents("php://input"));
$con = new conePDO();
$cn = $con->conectar();

try {

    if (!$post) {
        throw new Exception('No se recibieron parámetros');
    }

    $sql = $cn->prepare("SELECT marcaFinalCliente FROM calidad WHERE idLoteInterno = :idLoteInterno");
    $sql->bindParam(':idLoteInterno', $post);
    $sql->execute();

    if ($sql->rowCount() >= 1) {
        echo json_encode(['error' => false, 'data' => $sql->fetch(PDO::FETCH_ASSOC)]);
    } else {
        echo json_encode(['error' => false, 'data' => '0']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
