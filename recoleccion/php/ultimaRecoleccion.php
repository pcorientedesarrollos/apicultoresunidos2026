<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
// Obtiene el total resultante de la ultima recoleccion de una localidad
try {

    if (!isset($_GET['idLocalidad'])) {
        throw new Exception('No se recibió parámetro.');
    } else {
        $idLocalidad = $_GET['idLocalidad'];
    }

    $sql = "SELECT total FROM recoleccion r
    LEFT JOIN recoleccionencabezado re ON re.idRecoleccion = r.idRecoleccion
    WHERE idLocalidad = :idLocalidad
    ORDER BY re.fecha DESC, r.idRecoleccionDetalle DESC LIMIT 1";

    $query = $con->prepare($sql);
    $query->bindParam(':idLocalidad', $idLocalidad);
    $query->execute();
    if (!$query) {
        throw new Exception($con->errorInfo());
    }

    $ultimaRecoleccionLocalidad = $query->fetch(PDO::FETCH_ASSOC);


    if (!$ultimaRecoleccionLocalidad) {
        $ultimaRecoleccionLocalidad = new stdClass();
        $ultimaRecoleccionLocalidad->total = 0;
    } else {
        $ultimaRecoleccionLocalidad['total'] = intval($ultimaRecoleccionLocalidad['total']);
    }

    echo json_encode(['error' => false, 'resultado' => $ultimaRecoleccionLocalidad]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
