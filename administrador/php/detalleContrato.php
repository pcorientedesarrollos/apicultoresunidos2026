<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = new stdClass();
try {

    if (!isset($_GET['idLote'])) {
        throw new Exception('No se recibieron datos');
    } else {
        $idLote = $_GET['idLote'];
        $tipoMiel = $_GET['miel'];
        switch ($tipoMiel) {
            case '1':
                $tabla = 'lotescontratados';
                break;
            case '2':
                $tabla = 'lotescontratados_organico';
                break;
        }
    }

    $sqlEncabezado = "SELECT * FROM $tabla WHERE idLoteContratado = :idLote";
    $datos = $con->prepare($sqlEncabezado);
    $datos->bindParam(':idLote', $idLote);
    $datos->execute();
    if ($datos->rowCount() >= 1) {
        if ($datos == FALSE) {
            throw new Exception($con->errorInfo());
        }
        $resultado = $datos->fetch(PDO::FETCH_ASSOC);
    } else {
        $resultado = '0';
    }


    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => $resultado]);
    exit();
}
