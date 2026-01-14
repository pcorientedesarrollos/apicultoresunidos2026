<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$data = file_get_contents('php://input');
try{
    if (!$data){
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($data);
        $idReporteEnvasado = $datos->idReporteEnvasado;
        $tipoDeMiel = $datos->tipoDeMiel;
    }

    switch ($tipoDeMiel) {
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
        default:
            throw new Exception('Tipo de miel inválido');
            break;
    }

    $array = array();
    $sqlPersonalCon = "SELECT pd.idAyudanteInterno, pd.idPersonalOM, om.nombre 
                    FROM $ayudantesinternos_tabla pd
                    LEFT JOIN personaloaxaca om ON om.idPersonalOM = pd.idPersonalOM
                    WHERE idReporteEnvasado = :idReporteEnvasado";
    $datos = $con->prepare($sqlPersonalCon);
    $datos->bindParam(':idReporteEnvasado', $idReporteEnvasado);
    $datos->execute();
    
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        while ($rs = $datos->fetch()) {
            $pInterno = new stdClass();
            $pInterno->idAyudanteInterno = $rs["idAyudanteInterno"];
            $pInterno->idPersonalOM = $rs["idPersonalOM"];
            $pInterno->nombre = $rs["nombre"];
            $array[] = $pInterno;
        }
    }

    echo json_encode(['error'=>false, 'message'=>'', 'data'=>$array]);
} catch (Exception $e){
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}
