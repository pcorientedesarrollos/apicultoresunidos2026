<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$resultado = array();

try {

    $sqlArqueos = "SELECT * FROM arqueo_semanas ORDER BY idSemana DESC";
    $queryArquos = $con->prepare($sqlArqueos);
    $queryArquos->execute();
    if ($queryArquos == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado = $queryArquos->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => $resultado]);
    exit();
}
