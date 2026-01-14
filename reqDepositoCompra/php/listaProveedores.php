<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    if (isset($_GET['idComprador'])) {

        $sql = $con->prepare('SELECT p.idProveedor, p.nombre FROM proveedor p
        WHERE  p.idComprador = :idComprador AND p.idProveedor IS NOT NULL AND p.activoInactivo = 0
        GROUP BY p.idProveedor');
        $sql->bindParam(':idComprador', $_GET['idComprador']);

    } else {
        $sql = $con->prepare('SELECT idProveedor, nombre FROM proveedor WHERE activoInactivo = 0 ORDER BY nombre ASC');

    }

    $sql->execute();
    if (!$sql) {
        throw new Exception($con->errorInfo());
    }
    $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['error' => false, 'resultado' => $resultado]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
