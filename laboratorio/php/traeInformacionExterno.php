<?php

include_once '../../DAOConeccion/conePDO.php';
$post = json_decode(file_get_contents("php://input"));
$con = new conePDO();
$cn = $con->conectar();

try {

    if (!$post) {
        throw new Exception('No se recibieron parámetros');
    }

    $datosEmpresa = $cn->prepare("SELECT * FROM empresasexternas WHERE idExterno = :idExterno");
    $datosEmpresa->bindParam(':idExterno', $post);
    $datosEmpresa->execute();

    if ($datosEmpresa->rowCount() >= 1) {
        echo json_encode(['error' => false, 'data' => $datosEmpresa->fetch(PDO::FETCH_ASSOC)]);
    } else {
        throw new Exeption('No hay empresas para mostrar');
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
