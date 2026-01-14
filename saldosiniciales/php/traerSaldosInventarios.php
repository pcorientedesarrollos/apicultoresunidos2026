<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    $resultado = array();

    $query = $con->prepare("SELECT id, nombre, existenciaPasada, importeAcumuladoPasado FROM saldoinicialinventario;");
    $query->execute();
    if (!$query) {
        throw new Exception($con->errorInfo() || 'Error en el query');
    }

    // Convertir el nombre de los registros (quitar '_')

    foreach($query->fetchAll(PDO::FETCH_ASSOC) as $registro) {
        $registro['nombre'] = strtoupper(join(' ', explode('_', $registro['nombre'])));
        array_push($resultado, $registro);
    }

    echo json_encode(['error' => false, 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
