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
            $calidad_tabla = 'calidad';
            $experimental = 'experimental';
            $lotesContratados = 'lotescontratados';
            break;
        case '2':
            $calidad_tabla = 'calidad_organico';
            $experimental = 'experimental_organico';
            $lotesContratados = 'lotescontratados_organico';
            break;
        case '5':
            $calidad_tabla = 'calidad_mantequilla';
            $experimental = 'experimental_mantequilla';
            $lotesContratados = 'lotescontratados_mantequilla';
            break;
        case '6':
            $calidad_tabla = 'calidad_altiplano';
            $experimental = 'experimental_altiplano';
            $lotesContratados = 'lotescontratados_altiplano';
            break;
        case '7':
            $calidad_tabla = 'calidad_naranjo';
            $experimental = 'experimental_naranjo';
            $lotesContratados = 'lotescontratados_naranjo';
            break;
        case '8':
            $calidad_tabla = 'calidad_aguacate';
            $experimental = 'experimental_aguacate';
            $lotesContratados = 'lotescontratados_aguacate';
            break;
        case '9':
            $calidad_tabla = 'calidad_mezquite';
            $experimental = 'experimental_mezquite';
            $lotesContratados = 'lotescontratados_mezquite';
            break;
        default:
            throw new Exception('Argumento inválido');
            break;
    }

    $sql = "SELECT c.*, lc.contrato FROM $calidad_tabla c
    LEFT JOIN $experimental e ON e.idLoteExperimental = c.idLoteExperimental 
    LEFT JOIN $lotesContratados lc ON lc.idLoteContratado = e.numContrato";

    if (isset($_GET['mes'])) {
        $mes = $_GET['mes'];
        $sql .= " WHERE SUBSTR(fechaEnvasado FROM 6 FOR 2) =" . $mes;
    }

    $sql .= " ORDER BY idLoteInterno DESC";

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