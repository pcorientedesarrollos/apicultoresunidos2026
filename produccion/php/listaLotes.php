<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    if (!isset($_GET['tipoMiel'])) {
        throw new Exception('No re recibieron los parámetros esperados');
    } else {
        $tipoDeMiel = $_GET['tipoMiel'];
    }

    switch ($tipoDeMiel) {
        case '1':
            $calidad_tabla = 'calidad';
            break;
        case '2':
            $calidad_tabla = 'calidad_organico';
            break;
        case '5':
            $calidad_tabla = 'calidad_mantequilla';
            break;
        case '6':
            $calidad_tabla = 'calidad_altiplano';
            break;
        case '7':
            $calidad_tabla = 'calidad_naranjo';
            break;
        case '8':
            $calidad_tabla = 'calidad_aguacate';
            break;
        case '9':
            $calidad_tabla = 'calidad_mezquite';
            break;
        default:
            throw new Exception('Tipo de miel no válido');
            break;
    }

    $query = "SELECT idLoteInterno FROM $calidad_tabla ORDER BY idLoteInterno ASC";
    $data = $con->prepare($query);
    //** ANA 2025 (NO SE REQUIERE PARAMETRO, NI SIQUIERA SE ENVIA NI RECIBE) */
    // $data->bindParam(':idLoteInterno', $idLoteInterno);
    //** ANA 2025 */
    $data->execute();

    $infoLote = $data->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['error' => false, 'infoLote' => $infoLote]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}

// if(isset($_GET['organica'])){
//     $sql = 'SELECT idLoteInterno FROM calidad_organico ORDER BY idLoteInterno ASC';
// } else {
//     $sql = 'SELECT idLoteInterno FROM calidad ORDER BY idLoteInterno ASC';
// }

// $data = $con->prepare($sql);
// $data->execute();

// $arrayLotes = array();

// while ($row = $data->fetch()) {
//     $listaLotes = new stdClass();
//     $listaLotes->idLoteInterno = $row["idLoteInterno"];
//     $arrayLotes[] = $listaLotes;
// }

// echo $json_response = json_encode($arrayLotes);
