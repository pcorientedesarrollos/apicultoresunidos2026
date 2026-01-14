<?php

include_once '../../DAOConeccion/conePDO.php';
// include_once '../../mensajes/Mensajes.php';
$pdo = new conePDO();
// $mensajes = new Mensajes();
$con = $pdo->conectar();
$datos = file_get_contents('php://input');

try {
    if(!$datos){
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($datos);
        $idPersonalDescarga = $datos->id;
        $tipoDeMiel = $datos->tipoDeMiel;
    }

    switch($tipoDeMiel) {
        case '1':
            $personaldescarga_tabla = 'personaldescarga';
            break;
        case '2':
            $personaldescarga_tabla = 'personaldescarga_organico';
            break;
        case '5':
            $personaldescarga_tabla = 'personaldescarga_mantequilla';
            break;
        case '6':
            $personaldescarga_tabla = 'personaldescarga_altiplano';
            break;
        case '7':
            $personaldescarga_tabla = 'personaldescarga_naranjo';
            break;
        case '8':
            $personaldescarga_tabla = 'personaldescarga_aguacate';
            break;
        case '9':
            $personaldescarga_tabla = 'personaldescarga_mezquite';
            break;
        default:
            break;
    }

    $sqlEliminarContacto = "DELETE FROM $personaldescarga_tabla WHERE idPersonalDescarga = :idPersonalDescarga";
    $datos = $con->prepare($sqlEliminarContacto);
    $datos->bindParam(':idPersonalDescarga', $idPersonalDescarga);
    $datos->execute();

    if($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error'=>false, 'message'=>'Registro elminado']);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}
