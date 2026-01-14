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
        $idReporteEnvasado = $datos->idReporteEnvasado;
        $tipoDeMiel = $datos->tipoDeMiel;
    }

    switch ($tipoDeMiel){
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

    $array = array();
    $sqlPersonalCon = "SELECT pd.idEnvasado, pd.idPersonalOM, om.nombre 
                    FROM $personalenvasado_tabla pd 
                    LEFT JOIN personaloaxaca om ON om.idPersonalOM = pd.idPersonalOM
                    WHERE idReporteEnvasado = :idReporteEnvasado";
    $datos = $con->prepare($sqlPersonalCon);
    $datos->bindParam(':idReporteEnvasado', $idReporteEnvasado);
    $datos->execute();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $pEnvasado = new stdClass();
            $pEnvasado->idEnvasado = $rs["idEnvasado"];
            $pEnvasado->idPersonalOM = $rs["idPersonalOM"];
            $pEnvasado->nombre = $rs["nombre"];
            $array[] = $pEnvasado;
        }
    }
    echo json_encode(['error'=>false, 'message'=>'', 'data'=>$array]);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}
