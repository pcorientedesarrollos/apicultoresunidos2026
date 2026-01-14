<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    if (!isset($_GET['idLoteInterno']) || !isset($_GET['tipoDeMiel'])) {
        throw new Exception('No re recibieron los parámetros esperados');
    } else {
        $idLoteInterno = $_GET['idLoteInterno'];
        $tipoDeMiel = $_GET['tipoDeMiel'];
    }

    switch ($tipoDeMiel) {
        case '1':
            $reportesdeprocesos_tabla = 'reportesdeprocesos';
            $calidad_tabla = 'calidad';
            break;
        case '2':
            $reportesdeprocesos_tabla = 'reportesdeprocesos_organico';
            $calidad_tabla = 'calidad_organico';
            break;
            
        case '5':
            $reportesdeprocesos_tabla = 'reportesdeprocesos_mantequilla';
            $calidad_tabla = 'calidad_mantequilla';
            break;
            
        case '6':
            $reportesdeprocesos_tabla = 'reportesdeprocesos_altiplano';
            $calidad_tabla = 'calidad_altiplano';
            break;
            
        case '7':
            $reportesdeprocesos_tabla = 'reportesdeprocesos_naranjo';
            $calidad_tabla = 'calidad_naranjo';
            break;
            
        case '8':
            $reportesdeprocesos_tabla = 'reportesdeprocesos_aguacate';
            $calidad_tabla = 'calidad_aguacate';
            break;
            
        case '9':
            $reportesdeprocesos_tabla = 'reportesdeprocesos_mezquite';
            $calidad_tabla = 'calidad_mezquite';
            break;
        default:
            throw new Exception('Tipo de miel inválido');
            break;
    }

    $query = "SELECT rp.procesada, c.fechaEnvasado
    FROM $reportesdeprocesos_tabla rp
    LEFT JOIN $calidad_tabla c ON c.idLoteInterno = rp.idLoteInterno
    WHERE c.idLoteInterno = :idLoteInterno";
    $data = $con->prepare($query);
    $data->bindParam(':idLoteInterno', $idLoteInterno);
    $data->execute();

    $infoLote = $data->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['error' => false, 'infoLote' => $infoLote]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
