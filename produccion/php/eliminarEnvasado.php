<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$data = file_get_contents('php://input');

try {
    if(!$data){
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($data);
        $idEnvasado = $datos->idEnvasado;
        $tipoDeMiel = $datos->tipoDeMiel;
    }

    switch($tipoDeMiel){
        case '1':
            $personalenvasado_tabla = 'personalenvasado';
            break;
        case '2':
            $personalenvasado_tabla = 'personalenvasado_organico';
            break;
            
        case '5':
            $personalenvasado_tabla = 'personalenvasado_mantequilla';
            break;
            
        case '6':
            $personalenvasado_tabla = 'personalenvasado_altiplano';
            break;
            
        case '7':
            $personalenvasado_tabla = 'personalenvasado_naranjo';
            break;
            
        case '8':
            $personalenvasado_tabla = 'personalenvasado_aguacate';
            break;

        case '9':
            $personalenvasado_tabla = 'personalenvasado_mezquite';
            break;
        default: 
            throw new Exception('Tipo de miel inválido');
            break;
    }

    $sql = "DELETE FROM $personalenvasado_tabla WHERE idEnvasado = :idEnvasado";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idEnvasado', $idEnvasado);
    $datos->execute();
    if($datos == FALSE){
        throw new Exception($con->erroInfo());
    }

    echo json_encode(['error'=>false, 'message'=>'Se ha eliminado el registro']);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
    exit();
}
