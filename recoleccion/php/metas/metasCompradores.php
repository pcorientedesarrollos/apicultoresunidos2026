<?php
include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    if (isset($_GET['miel'])) {
        $miel = $_GET['miel'];
        switch ($miel) {
            case '1':
                $metas = 'metascompra';
                break;
            case '2':
                $metas = 'metascompra_organico';
                break;
        }
    }

    $resultado = [];

    $sql = "SELECT idComprador, nombre FROM compradores WHERE estado = 1;"; // Selecciona nombre y id de compradores activos

    $query = $con->prepare($sql);
    $query->bindParam(':idProyeccion', $idProyeccionSolicitado);
    $query->execute();

    if (!$query) {
        throw new Exception($con->errorInfo());
    }

    $listaCompradores = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($listaCompradores as $comprador) {
        // Por cada comprador, traer sus zonas y la meta de cada zona, sumar en una propiedad el total de la meta
        $comprador['totalTambores'] = 0;
        $comprador['totalMeta'] = 0;
        $comprador['zonas'] = array();

        $sqlZonasComprador = "SELECT z.idzona, z.zona, z.referencia,
        CASE WHEN mc.tambores IS NULL THEN 0 ELSE mc.tambores END as tambores,
        CASE WHEN mc.kilogramos IS NULL THEN 0 ELSE mc.kilogramos END as kilogramos
        FROM zonas z
        LEFT JOIN $metas mc ON mc.idZona = z.idzona
        WHERE idcomprador = :idComprador AND estado = 1";
        $queryZonasComprador = $con->prepare($sqlZonasComprador);
        $queryZonasComprador->bindParam(':idComprador', $comprador['idComprador']);
        $queryZonasComprador->execute();
        if (!$queryZonasComprador) {
            throw new Exception($con->errorInfo());
        }
        $resultadoZonasComprador = $queryZonasComprador->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultadoZonasComprador as $zona) {
            $zona['tambores'] = floatval($zona['tambores']);
            $zona['kilogramos'] = floatval($zona['kilogramos']);
            $comprador['totalTambores'] += $zona['tambores'];
            $comprador['totalMeta'] += $zona['kilogramos'];
            array_push($comprador['zonas'], $zona);
        }

        array_push($resultado, $comprador);
    }

    echo json_encode(['error' => false, 'resultado' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
