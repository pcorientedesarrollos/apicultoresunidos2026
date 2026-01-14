<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = new stdClass();

try {

    if (!isset($_GET['entrada'])) {
        throw new Exception('No se recibieron datos');
    } else {
        $entrada = $_GET['entrada'];
    }

    $sql = "SELECT al.*
            FROM almacensobrantesencabezado al
            WHERE al.idEncabezadoSobrante = :entrada";
    $datos = $con->prepare($sql);
    $datos->bindParam(':entrada', $entrada);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $resultado = $datos->fetch(PDO::FETCH_ASSOC);

    $sqlDetalle = "SELECT als.*, s.codigo
    FROM almacensobrantes als
    LEFT JOIN sobrantes s ON s.idSobrante = als.sobrante
    WHERE als.consecutivoEntrada = :entrada";
    $datos = $con->prepare($sqlDetalle);
    $datos->bindParam(':entrada', $entrada);
    $datos->execute();
    // if ($datos == FALSE) {
    //     throw new Exception($con->errorInfo());
    // }
    // $resultado = $datos->fetch(PDO::FETCH_ASSOC);

    // echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
    
    if($datos == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado['tambos'] = $datos->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['error'=>false, 'message'=>'Consulta realizada', 'data'=>$resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => $resultado]);
    exit();
}
