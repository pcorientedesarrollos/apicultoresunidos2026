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
        $idReporteEnvasado = $datos->idReporteEnvasado;
        $idPersonalOM = $datos->idPersonal;
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
    $sqlConfo = "INSERT INTO $personalenvasado_tabla (idPersonalOM ,idReporteEnvasado)
    VALUES (:idPersonalOM, :idReporteEnvasado)";
    $data = $con->prepare($sqlConfo);
    $data->bindParam(':idPersonalOM', $idPersonalOM);
    $data->bindParam(':idReporteEnvasado', $idReporteEnvasado);
    $data->execute();
    if($data == FALSE) {
        throw new Exception($con->errorInfo());
    }
    echo json_encode(['error'=>false, 'message'=>'Se ha guardado nuevo envasador']);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}
