<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    $arrayLotes = array();

    if (!isset($_GET['tipoDeMiel'])) {
        throw new Exception('No se especificó el tipo de miel');
    } else {
        $tipoDeMiel = $_GET['tipoDeMiel'];
    }

    // $entradaysalida_tabla = $tipoDeMiel == '1'
    //     ? 'entradaysalida'
    //     : 'entradaysalida_organico';

    switch($tipoDeMiel){
        case '1':
            $entradaysalida_tabla = 'entradaysalida';
            break;
        case '2':
            $entradaysalida_tabla = 'entradaysalida_organico';
            break;
        case '5':
            $entradaysalida_tabla = 'entradaysalida_mantequilla';
            break;
        case '6':
            $entradaysalida_tabla = 'entradaysalida_altiplano';
            break;
        case '7':
            $entradaysalida_tabla = 'entradaysalida_naranjo';
            break;
        case '8':
            $entradaysalida_tabla = 'entradaysalida_aguacate';
            break;
        case '9':
            $entradaysalida_tabla = 'entradaysalida_mezquite';
            break;
    }

    $data = $con->prepare("SELECT lote FROM $entradaysalida_tabla WHERE estado = 1");
    $data->execute();
    if ($data == false) {
        throw new Exception($con->errorInfo());
    } else {
        foreach ($data->fetchAll(PDO::FETCH_ASSOC) as $lote) {
            array_push($arrayLotes, $lote);
        }
    }

    echo json_encode(['error' => false, 'lotes' => $arrayLotes]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
