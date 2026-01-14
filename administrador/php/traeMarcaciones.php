<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {

    $tipo = $_GET['tipoMiel'];
    $arrayR = array();

    switch ($tipo) {
        case '1':
            $entradaysalida = 'entradaysalida';
            break;
        case '2':
            $entradaysalida = 'entradaysalida_organico';
            break;
        case '5':
            $entradaysalida = 'entradaysalida_mantequilla';
            break;
        case '6':
            $entradaysalida = 'entradaysalida_altiplano';
            break;
        case '7':
            $entradaysalida = 'entradaysalida_naranjo';
            break;
        case '8':
            $entradaysalida = 'entradaysalida_aguacate';
            break;
        case '9':
            $entradaysalida = 'entradaysalida_mezquite';
            break;
    }

    $query = "SELECT c.idReporte, c.lote, c.fechaImpresion, c.idLoteInterno
    FROM $entradaysalida c
    WHERE c.estado = '1' AND c.miel = '1'
    ORDER BY c.fechaImpresion DESC";
    $datos = $con->prepare($query);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }

    $arrayR = $datos->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $arrayR]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit();
}
