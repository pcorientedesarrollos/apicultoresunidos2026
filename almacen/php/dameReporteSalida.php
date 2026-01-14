<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    if(!isset($_GET['idSalida'])) {
        throw new Exception('No se recibieron datos');
    }
    $idSalida = $_GET['idSalida'];

    $sqlSelecciona = $con->prepare("SELECT * FROM otrassalidas WHERE idOtraSalida = :idSalida");
    $sqlSelecciona->bindParam(':idSalida', $idSalida);
    $sqlSelecciona->execute();
    if($sqlSelecciona == FALSE) {
        throw new Exception($con->errorInfo());
    }

    $resultado = $sqlSelecciona->fetch(PDO::FETCH_ASSOC);
    $resultado['totalPeso'] = floatval($resultado['totalPeso']);
    $resultado['totalImporte'] = floatval($resultado['totalImporte']);

    $sqlDetalle = $con->prepare("SELECT osd.idOtrasSalidasDetalle, osd.idTipoDeMiel,
    osd.idConcepto, osd.kg, osd.importe, osd.cantidad, osd.idSubconceptoCC as idSubSubcuenta,
    osd.folioTambor, osd.estadoTambor, osd.idOtrasSalidas, osd.cajaChica, osd.observaciones FROM otrassalidasdetalle osd
    WHERE idOtrasSalidas = :idOtrasSalidas");
    $sqlDetalle->bindParam(':idOtrasSalidas', $idSalida);
    $sqlDetalle->execute();
    if($sqlDetalle == FALSE) {
        throw new Exception($con->errorInfo());
    }

    $resultado['conceptos'] = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['error'=>false, 'salida'=>$resultado]);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}