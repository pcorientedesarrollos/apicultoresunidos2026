<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sqlTransportes = 'SELECT idTransporte, transporte FROM transportes;';

try {
    $query = $con->prepare($sqlTransportes);
    $query->execute();
    if (!$query) {
        throw new Exception($con->errorInfo());
    }

    $resultadoTransportes = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['error' => false, 'resultado' => $resultadoTransportes]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
