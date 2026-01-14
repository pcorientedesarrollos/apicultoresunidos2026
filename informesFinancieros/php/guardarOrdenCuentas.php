<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();
$json = file_get_contents("php://input");

try {
    if (!$json) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $info = json_decode($json);
    }

    foreach ($info as $orden) {
        $sqlInsert = "UPDATE relacioncuentainformes SET orden = :orden WHERE idRelacion = :idRelacion";
        $queryInsert = $con->prepare($sqlInsert);
        $queryInsert->bindParam(':orden', $orden->orden);
        $queryInsert->bindParam(':idRelacion', $orden->idRelacion);
        $queryInsert->execute();
        if ($queryInsert == FALSE) {
            throw new Exception($con->errorInfo());
        }
    }
    echo json_encode(['error' => false, 'message' => 'Se ha guardado la configuración']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line: ' . $e->getline()]);
}
