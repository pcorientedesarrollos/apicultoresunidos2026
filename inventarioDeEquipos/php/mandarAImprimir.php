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
    }

    // Verificar y registra la impresión

    foreach ($datos as $equipo) {
        $sqlVerificar = $con->prepare("SELECT idImpresion FROM impresion WHERE idAlmacen = :idEquipo && estado = 2");
        $sqlVerificar->bindParam(':idEquipo', $equipo);
        $sqlVerificar->execute();
        if ($sqlVerificar == false) {
            throw new Exception($con->errorInfo());
        }

        if ($sqlVerificar->rowCount() == 0) {
            $sqlInsert = $con->prepare("INSERT INTO impresion (idAlmacen, estado, idTipoDeMiel)
            VALUES (:idEquipo, 2, 0)");
            $sqlInsert->bindParam(':idEquipo', $equipo);
            $sqlInsert->execute();
            if ($sqlInsert == false) {
                throw new Exception($con->errorInfo());
            }
        }
    }

    echo json_encode(['error' => false, 'message' => 'Se ha registrado la impresión']);


} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}