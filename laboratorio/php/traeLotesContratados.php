<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();

$tipoDeMiel = file_get_contents('php://input');
$resultado = [];
try {
    if (!$tipoDeMiel) {
        throw new Exception('Se esperaban argumentos');
    }
    switch ($tipoDeMiel) {
        case '1':
            $tabla = 'lotescontratados';
            $experimental = 'experimental';
            $calidad = 'calidad';
            $muestras = 'enviomuestras';
            $proceso = 'reportesdeprocesos';
            $carga = 'entradaysalida';
            break;
        case '2':
            $tabla = 'lotescontratados_organico';
            $experimental = 'experimental_organico';
            $calidad = 'calidad_organico';
            $muestras = 'enviomuestras_organico';
            $proceso = 'reportesdeprocesos_organico';
            $carga = 'entradaysalida_organico';
            break;
        default:
            throw new Exception('Argumento inválido');
            break;
    }

    $sql = "SELECT lc.idLoteContratado, lc.contrato
    FROM $tabla lc
    WHERE lc.idLoteContratado NOT IN (SELECT numContrato FROM $muestras WHERE numcontrato > 0)
    ORDER BY idLoteContratado DESC";
    $datos = $con->prepare($sql);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }

    $resultado = $datos->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['error' => false, 'message' => '', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine(), 'data' => $resultado]);
}
