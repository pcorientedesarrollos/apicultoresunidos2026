<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$arrayTSalida = array();

try {

    if (!isset($_GET['miel'])) {
        throw new Exception('No se recibieron parámetros necesarios');
    } else {
        $tipoDeMiel = $_GET['miel']; // Este es el id del tipo de miel
        if ($tipoDeMiel == '1') {
            $calidad_tabla = 'calidad';
        } else if($tipoDeMiel == '5'){
            $calidad_tabla = 'calidad_mantequilla';

        } else if($tipoDeMiel == '6'){
            $calidad_tabla = 'calidad_altiplano';

        } else if($tipoDeMiel == '7'){
            $calidad_tabla = 'calidad_naranjo';

        }  else if($tipoDeMiel == '8'){
            $calidad_tabla = 'calidad_aguacate';

        } else if($tipoDeMiel == '9'){
            $calidad_tabla = 'calidad_mezquite';

        } else {
            $calidad_tabla = 'calidad_organico';
        }
    }

    $datos = $con->prepare("SELECT t.idLoteInterno, c.marcaFinalCliente 
    FROM trazabilidadsalida t
    LEFT JOIN $calidad_tabla c ON c.idLoteInterno = t.idLoteInterno
    WHERE t.tipoMiel = $tipoDeMiel
    ORDER BY t.idLoteInterno DESC");

    $datos->execute();
    if (!$datos) {
        throw new Exception($con->errorInfo());
    }

    $arrayTSalida = $datos->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['error' => false, 'message' => '', 'data' => $arrayTSalida]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
