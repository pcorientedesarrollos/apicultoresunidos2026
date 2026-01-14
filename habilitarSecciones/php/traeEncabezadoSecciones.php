<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    $query = $con->prepare("SELECT * FROM secciones");
    $query->execute();
    if ($query == false) {
        throw new Exception($con->errorInfo());
    }

    $informacionTabla = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['error' => false, 'secciones' => $informacionTabla]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
