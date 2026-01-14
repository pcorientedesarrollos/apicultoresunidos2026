<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();

$datos = file_get_contents('php://input');

try {
    if(!$datos){
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($datos);
        $idReporte = $datos->idReporte;
        $idPersonalOM = $datos->idPersonalOM;
        $tipoDeMiel = $datos->tipoDeMiel;
    }

    switch ($tipoDeMiel) {
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
            throw new Exception('Tipo de miel inválido');
            break;
    }

    $sqlDescarga = "INSERT INTO $personaldescarga_tabla (idPersonalOM ,idReporte) VALUES (:idPersonalOM, :idReporte)";
    $datos = $con->prepare($sqlDescarga);
    $datos->bindParam(':idPersonalOM', $idPersonalOM);
    $datos->bindParam(':idReporte', $idReporte);
    $datos->execute();

    if($datos == FALSE){
        throw new Exception($con->errorInfo());
    }
    echo json_encode(['error'=>false, 'message'=>'Se ha agregado al personal']);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
    exit();
}
