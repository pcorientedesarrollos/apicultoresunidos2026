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
        case '5':
            $tabla = 'lotescontratados_mantequilla';
            $experimental = 'experimental_mantequilla';
            $calidad = 'calidad_mantequilla';
            $muestras = 'enviomuestras_mantequilla';
            $proceso = 'reportesdeprocesos_mantequilla';
            $carga = 'entradaysalida_mantequilla';
            break;
        case '6':
            $tabla = 'lotescontratados_altiplano';
            $experimental = 'experimental_altiplano';
            $calidad = 'calidad_altiplano';
            $muestras = 'enviomuestras_altiplano';
            $proceso = 'reportesdeprocesos_altiplano';
            $carga = 'entradaysalida_altiplano';
            break;
        case '7':
            $tabla = 'lotescontratados_naranjo';
            $experimental = 'experimental_naranjo';
            $calidad = 'calidad_naranjo';
            $muestras = 'enviomuestras_naranjo';
            $proceso = 'reportesdeprocesos_naranjo';
            $carga = 'entradaysalida_naranjo';
            break;
        case '8':
            $tabla = 'lotescontratados_aguacate';
            $experimental = 'experimental_aguacate';
            $calidad = 'calidad_aguacate';
            $muestras = 'enviomuestras_aguacate';
            $proceso = 'reportesdeprocesos_aguacate';
            $carga = 'entradaysalida_aguacate';
            break;
        case '9':
            $tabla = 'lotescontratados_mezquite';
            $experimental = 'experimental_mezquite';
            $calidad = 'calidad_mezquite';
            $muestras = 'enviomuestras_mezquite';
            $proceso = 'reportesdeprocesos_mezquite';
            $carga = 'entradaysalida_mezquite';
            break;
        default:
            throw new Exception('Argumento inválido');
            break;
    }

    $sql = "SELECT lc.idLoteContratado, lc.contrato
    FROM $tabla lc
    WHERE lc.idLoteContratado NOT IN (SELECT numContrato FROM $experimental WHERE numcontrato > 0)
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
