<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$resultado = array();

try {

    if (!isset($_GET['idArqueo'])) {
        throw new Exception('No se recibieron datos');
    } else {
        $idArqueo = $_GET['idArqueo'];
    }

    $sqlEncabezado = "SELECT * FROM arqueo_semanas WHERE idSemana = :idArqueo";
    $queryEncabezado = $con->prepare($sqlEncabezado);
    $queryEncabezado->bindParam(':idArqueo', $idArqueo);
    $queryEncabezado->execute();
    if ($queryEncabezado == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado = $queryEncabezado->fetch(PDO::FETCH_ASSOC);
    }

    $sqlGastos = "SELECT * FROM arqueo_gastos WHERE idSemana = :idArqueo";
    $queryGastos = $con->prepare($sqlGastos);
    $queryGastos->bindParam(':idArqueo', $idArqueo);
    $queryGastos->execute();
    if ($queryGastos == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado['gastos'] = $queryGastos->fetchAll(PDO::FETCH_ASSOC);
    }

    $sqlBilletes = "SELECT * FROM arqueo_billetesmonedas WHERE idSemana = :idArqueo";
    $queryBilletes = $con->prepare($sqlBilletes);
    $queryBilletes->bindParam(':idArqueo', $idArqueo);
    $queryBilletes->execute();
    if ($queryBilletes == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado['billetes'] = $queryBilletes->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => $resultado]);
    exit();
}
