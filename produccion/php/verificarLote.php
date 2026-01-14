<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    if (!isset($_GET['idLoteInterno'])) {
        throw new Exception('No se recibieron los parámetros esperados.');
    } else {
        $idLoteInterno = $_GET['idLoteInterno'];
        if (!$idLoteInterno) {
            throw new Exception('Lote no está definido');
        }
    }

    $tabla = isset($_GET['organica']) ? 'reportesdeprocesos_organico' : 'reportesdeprocesos';
    
    if(isset($_GET["organica"])){
        switch ($_GET['organica']) {
            case 0:
                $tabla = 'reportesdeprocesos_organico';
                break;
            case 1:
                $tabla = 'reportesdeprocesos_mantequilla';
                break;
            case 2:
                $tabla = 'reportesdeprocesos_altiplano';
                break;
            case 3:
                $tabla = 'reportesdeprocesos_naranjo';
                break;
            case 4:
                $tabla = 'reportesdeprocesos_aguacate';
                break;
            case 5:
                $tabla = 'reportesdeprocesos_mezquite';
                break;
        }}

    $datos = $con->prepare("SELECT idLoteInterno FROM $tabla WHERE idLoteInterno = :idLoteInterno");
    $datos->bindParam(':idLoteInterno', $idloteInterno);
    $datos->execute();

    if ($datos == false) {
        throw new Exception($con->errorInfo());
    }

    $existe = $datos->fetch(PDO::FETCH_ASSOC);
    $respuesta = $existe ? 1 : 0;

    echo json_encode(['error' => false, 'existe' => $respuesta]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}