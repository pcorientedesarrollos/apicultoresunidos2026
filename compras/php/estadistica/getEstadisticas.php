<?php
include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$postdata = file_get_contents('php://input');

try {

    if (!$postdata) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $info = json_decode($postdata);
        $respuesta = array();
    }

    $sql = "SELECT :fechaUno AS fechaUno, :fechaDos AS fechaDos, COALESCE(SUM(totalTambores), 0) as tamboresCompras,
    COALESCE(SUM(importeTotal), 0) AS importeCompras,
    (SELECT COALESCE(COUNT(a.idAlmacen), 0) FROM almacen a 
    INNER JOIN almacenencabezado ae ON ae.idAlmacen = a.idAlmacenEncabezado 
    WHERE ae.fecha BETWEEN :fechaUno AND :fechaDos) as tamboresAlmacen,
    (SELECT COALESCE(SUM(ae.totalCompra), 0) 
    FROM almacenencabezado ae 
    WHERE ae.fecha BETWEEN :fechaUno AND :fechaDos
    ) AS importeAlmacen
    FROM requisicionencabezado
    WHERE fechaRequisicion BETWEEN :fechaUno AND :fechaDos";
    $datos = $con->prepare($sql);
    $datos->bindParam(':fechaUno', $info->fechaUno);
    $datos->bindParam(':fechaDos', $info->fechaDos);
    $datos->execute();
    if ($datos == false) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado = $datos->fetch(PDO::FETCH_ASSOC);
        // array_push($respuesta, $resultado);
        echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => []]);
    exit();
}