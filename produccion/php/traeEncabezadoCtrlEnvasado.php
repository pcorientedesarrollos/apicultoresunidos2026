<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$tipoDeMiel = file_get_contents('php://input');
$response = array();
try {
    if(!$tipoDeMiel){
        throw new Exception('No se ha recibido parametros.');
    } else {
        switch($tipoDeMiel){
            case '1':
                $reportesdeenvasados_tabla = 'reportesdeenvasados';
                $calidad_tabla = 'calidad';
                $idLote = 'CONCAT("LC26-", rp.idLoteInterno) as idLoteInterno';
                break;
            case '2':
                $reportesdeenvasados_tabla = 'reportesdeenvasados_organico';
                $calidad_tabla = 'calidad_organico';
                $idLote = 'CONCAT("LO26-", rp.idLoteInterno) as idLoteInterno';
                break;
            case '5':
                $reportesdeenvasados_tabla = 'reportesdeenvasados_mantequilla';
                $calidad_tabla = 'calidad_mantequilla';
                $idLote = 'CONCAT("LM26-", rp.idLoteInterno) as idLoteInterno';
                break;
            case '6':
                $reportesdeenvasados_tabla = 'reportesdeenvasados_altiplano';
                $calidad_tabla = 'calidad_altiplano';
                $idLote = 'CONCAT("LT24-", rp.idLoteInterno) as idLoteInterno';
                break;
            case '7':
                $reportesdeenvasados_tabla = 'reportesdeenvasados_naranjo';
                $calidad_tabla = 'calidad_naranjo';
                $idLote = 'CONCAT("LN26-", rp.idLoteInterno) as idLoteInterno';
                break;
            case '8':
                $reportesdeenvasados_tabla = 'reportesdeenvasados_aguacate';
                $calidad_tabla = 'calidad_aguacate';
                $idLote = 'CONCAT("LA26-", rp.idLoteInterno) as idLoteInterno';
                break;
            case '9':
                $reportesdeenvasados_tabla = 'reportesdeenvasados_mezquite';
                $calidad_tabla = 'calidad_mezquite';
                $idLote = 'CONCAT("LZ26-", rp.idLoteInterno) as idLoteInterno';
                break;
            default:
                break;
        }
    }

    $sql = "SELECT rp.idReporteEnvasado, $idLote,
    c.fechaEnvasado, rp.netosEnvasados, rp.totalDeTambores
    FROM $reportesdeenvasados_tabla rp
    LEFT JOIN $calidad_tabla c ON c.idLoteInterno = rp.idLoteInterno
    ORDER BY c.fechaEnvasado DESC";
    $datos = $con->prepare($sql);
    $datos->execute();
    if($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $response = $datos->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['error'=>false, 'data'=>$response]);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'data'=>$response]);
    exit();
}
