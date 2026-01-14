<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$data = file_get_contents('php://input');

try {
    if(!$data) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($data);
        $idAyudanteInterno = $datos->idAyudanteInterno;
        $tipoDeMiel = $datos->tipoDeMiel;
    }

    switch($tipoDeMiel){
        case '1':
            $ayudantesinternos_tabla = 'ayudantesinternos';
            break;
        case '2':
            $ayudantesinternos_tabla = 'ayudantesinternos_organico';
            break;
            
        case '5':
            $ayudantesinternos_tabla = 'ayudantesinternos_mantequilla';
            break;
            
        case '6':
            $ayudantesinternos_tabla = 'ayudantesinternos_altiplano';
            break;
            
        case '7':
            $ayudantesinternos_tabla = 'ayudantesinternos_naranjo';
            break;
            
        case '8':
            $ayudantesinternos_tabla = 'ayudantesinternos_aguacate';
            break;
            
        case '9':
            $ayudantesinternos_tabla = 'ayudantesinternos_mezquite';
            break;
    }

    $sql = "DELETE FROM $ayudantesinternos_tabla WHERE idAyudanteInterno = :idAyudanteInterno";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idAyudanteInterno', $idAyudanteInterno);
    $datos->execute();
    if($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }
    echo json_encode(['error'=>false, 'message'=>'Registro eliminado']);
} catch (Exception $e){
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}
