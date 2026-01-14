<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$postdata = file_get_contents('php://input');

try {
    if (!$postdata) {
        throw new Exception('No se recibieron los datos');
    } else {
        $datos = json_decode($postdata);
        if (!$datos->idEntradaSobrante) {
            throw new Exception('No se recibieron los parámetros esperados');
        }
    }

    // Verificar y registra la impresión

    $sqlVerificar = $con->prepare("SELECT idImpresion FROM impresion WHERE idAlmacen = :idEntradaSobrante && estado = 5");
    $sqlVerificar->bindParam(':idEntradaSobrante', $datos->idEntradaSobrante);
    $sqlVerificar->execute();
    if ($sqlVerificar == false) {
        throw new Exception($con->errorInfo());
    }

    if ($sqlVerificar->rowCount() == 0) {
        $sqlInsert = $con->prepare("INSERT INTO impresion (idAlmacen, estado, idTipoDeMiel)
        VALUES (:idEntradaSobrante, 5, 0)");
        $sqlInsert->bindParam(':idEntradaSobrante', $datos->idEntradaSobrante);
        $sqlInsert->execute();
        if ($sqlInsert == false) {
            throw new Exception($con->errorInfo());
        }
        echo json_encode(['error' => false, 'message' => 'Se ha registrado la impresión']);
    } else {
        echo json_encode(['error' => false, 'message' => 'Ya se ha registrado la impresión']);
    }

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}