<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    $resultado = array();

    $sql = $con->prepare("SELECT * FROM tiposdemovimientos");
    $sql->execute();

    if ($sql == false) {
        throw new Exception($con->errorInfo());
    }

    $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['error' => false, 'data' => $resultado]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
