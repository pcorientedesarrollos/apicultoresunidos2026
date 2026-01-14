<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = new stdClass();
try {

    if (!isset($_GET['idEntrada'])) {
        throw new Exception('No se recibieron datos');
    } else {
        $idEntrada = $_GET['idEntrada'];
    }

    $sql = "SELECT aee.*
            FROM analisisexternosencabezado aee
            WHERE aee.idAnalisisEncabezado = :idEntrada";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idEntrada', $idEntrada);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $resultado = $datos->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT * FROM analisisexternosdetalle WHERE idAnalisisEncabezado = :idEntrada";
    $sqlDetalle = $con->prepare($sql);
    $sqlDetalle->bindParam(':idEntrada', $idEntrada);
    $sqlDetalle->execute();

    if($sqlDetalle == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado['conceptos'] = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['error'=>false, 'message'=>'Consulta realizada', 'data'=>$resultado]);

} catch (Exception $e){
    echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'data'=>$resultado]);
    exit();
}
