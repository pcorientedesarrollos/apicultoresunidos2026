<?php

include_once '../../../DAOConeccion/conePDO.php';
include_once '../../../controlAdministrativo/php/nombreDePersona.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Recibe por método GET la variable 'tipo' con el valor 1 para entradas y el valor 2 para salidas

try {

    if (!isset($_GET['tipo'])) {
        throw new Exception('No re recibieron parámetros');
    } else {
        $tipoDeReportes = $_GET['tipo'];
    }

    $sql = "SELECT al.*, pr.nombre AS proveedor
        FROM derivadosalmacenencabezado al 
        LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor
        LEFT JOIN derivadosalmacendetalle alm ON alm.idEntrada = al.idEntrada
        WHERE al.tipo = :tipo";

    if (isset($_GET['mes'])) {
        $mes = $_GET['mes'];
        $sql .= " AND SUBSTR(fecha FROM 6 FOR 2) = " . $mes;
    }
    if (isset($_GET['reporte'])) {
        $reporte = $_GET['reporte'];
        if ($reporte == '1') {
            $sql .= " AND al.idEntrada = alm.idEntrada";
        } else if ($reporte == '2') {
            $sql .= " AND al.idEntrada NOT IN (SELECT alm.idEntrada FROM derivadosalmacendetalle alm)";
        }
    }

    $sql .= " GROUP BY al.idEntrada ORDER BY al.idEntrada DESC";

    $datos = $con->prepare($sql);
    $datos->bindParam(':tipo', $tipoDeReportes);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $resultado = array();
    foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $object) {
        $object['proveedor'] = retornarNombre($con, $object['tipoPersona'], $object['idProveedor']);
        array_push($resultado, $object);
    }
    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => []]);
    exit();
}
