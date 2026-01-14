<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = new stdClass();
try {

    if (!isset($_GET['idEnvio'])) {
        throw new Exception('No se recibieron datos');
    } else {
        $idEnvio = $_GET['idEnvio'];
        $tipoMiel = $_GET['miel'];
        switch ($tipoMiel) {
            case '1':
                $tabla = 'enviomuestras';
                $contratos = 'lotescontratados';
                break;
            case '2':
                $tabla = 'enviomuestras_organico';
                $contratos = 'lotescontratados_organico';
                break;
        }
    }

    $sqlEncabezado = "SELECT e.*, c.contrato AS numDeContrato, c.idLoteContratado
    FROM $tabla e 
    LEFT JOIN $contratos c ON c.idLoteContratado = e.numContrato 
    WHERE e.idEnvio = :idEnvio";
    $datos = $con->prepare($sqlEncabezado);
    $datos->bindParam(':idEnvio', $idEnvio);
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
