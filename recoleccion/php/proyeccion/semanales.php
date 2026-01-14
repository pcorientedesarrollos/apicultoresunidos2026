<?php
include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miel = $_GET['miel'];

switch ($miel) {
    case '1':
        $proyeccionEncabezado = 'proyeccionencabezado';
        $almacen = 'almacen';
        $almacenEncabezado = 'almacenencabezado';
        break;
    case '2':
        $proyeccionEncabezado = 'proyeccionencabezado_organico';
        $almacen = 'almacen_organico';
        $almacenEncabezado = 'almacenencabezado_organico';
        break;
}

$sql = "SELECT idProyeccion, nombre, inicio, fin, totalProyeccion
FROM $proyeccionEncabezado ORDER BY inicio DESC;";

try {

    $query = $con->prepare($sql);
    $query->execute();
    if (!$query) {
        throw new Exception($con->errorInfo());
    }

    $resultadoProyeccionesSemanales = $query->fetchAll(PDO::FETCH_ASSOC);
    $resultado = [
        'totalProyeccion' => 0,
        'totalCompra' => 0,
        'totalDiferencia' => 0,
        'proyecciones' => []
    ];

    // Ahora con cada proyeccion semanal obtener el real del almacen

    foreach ($resultadoProyeccionesSemanales as $proyeccion) {
        $sqlConsultaReal = "SELECT SUM(al.neto) as total
        FROM $almacen al 
        LEFT JOIN $almacenEncabezado ae ON al.idAlmacenEncabezado = ae.idAlmacen
        WHERE ae.fecha BETWEEN :iniciosemana AND :finsemana";

        $queryRealSemana = $con->prepare($sqlConsultaReal);
        $queryRealSemana->bindParam(':iniciosemana', $proyeccion['inicio']);
        $queryRealSemana->bindParam(':finsemana', $proyeccion['fin']);
        $queryRealSemana->execute();

        if (!$queryRealSemana) {
            throw new Exception($con->errorInfo());
        } else {
            $resultadoReal = $queryRealSemana->fetch(PDO::FETCH_ASSOC);
        }

        if (!$resultadoReal['total']) { // Si no hay,la suma da NULL
            $resultadoReal['total'] = 0;
        }

        $proyeccion['totalReal'] = $resultadoReal['total'];
        $proyeccion['diferencia'] = $resultadoReal['total'] - $proyeccion['totalProyeccion'];
        if ($miel == "1") {
            $proyeccion['tipoDeMiel'] = '1';
        } else if ($miel == "2") {
            $proyeccion['tipoDeMiel'] = '2';
        }
        array_push($resultado['proyecciones'], $proyeccion);
    }

    foreach ($resultado['proyecciones'] as $proyeccion) {
        $resultado['totalProyeccion'] += $proyeccion['totalProyeccion'];
        $resultado['totalCompra'] += $proyeccion['totalReal'];
        $resultado['totalDiferencia'] += $proyeccion['diferencia'];
        // array_push($resultado, $totalProyeccion);
    }

    echo json_encode(['error' => false, 'resultado' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
