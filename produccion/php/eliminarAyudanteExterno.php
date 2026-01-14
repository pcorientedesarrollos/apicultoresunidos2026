<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$data = file_get_contents('php://input');

try {
    if(!$data){
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($data);
        $idAyudanteExterno = $datos->idAyudanteExterno;
        $tipoDeMiel = $datos->tipoDeMiel;
    }
    switch($tipoDeMiel){
        case '1':
            $ayudantesexternos_tabla = 'ayudantesexternos';
            break;
        case '2':
            $ayudantesexternos_tabla = 'ayudantesexternos_organico';
            break;
            
        case '5':
            $ayudantesexternos_tabla = 'ayudantesexternos_mantequilla';
            break;
            
        case '6':
            $ayudantesexternos_tabla = 'ayudantesexternos_altiplano';
            break;
            
        case '7':
            $ayudantesexternos_tabla = 'ayudantesexternos_naranjo';
            break;
            
        case '8':
            $ayudantesexternos_tabla = 'ayudantesexternos_aguacate';
            break;
            
        case '9':
            $ayudantesexternos_tabla = 'ayudantesexternos_mezquite';
            break;
    }

    
    $sql = "DELETE FROM $ayudantesexternos_tabla WHERE idAyudanteExterno = :idAyudanteExterno";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idAyudanteExterno', $idAyudanteExterno);
    $datos->execute();
    if($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }
    echo json_encode(['error'=>false, 'message'=>'Se eliminó el registro']);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}
