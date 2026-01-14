<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$datos = file_get_contents('php://input');
$resultado = array();

try {
    if(!$datos){
        throw new Exception('No se recibieron patámetros');
    } else {
        $datos = json_decode($datos);
        $idReporte = $datos->idReporte;
        $tipoDeMiel = $datos->tipoDeMiel;
    }

    switch ($tipoDeMiel){
        case '1':
            $personaldescarga_tabla = 'personaldescarga';
            break;
        case '2':
            $personaldescarga_tabla = 'personaldescarga_organico';
            break;
        default:
            throw new Exception('Tipo de miel es inválido');
            break;
    }

    $sqlPersonalDescarga = "SELECT pd.idPersonalDescarga, pd.idPersonalOM, om.nombre 
    FROM $personaldescarga_tabla pd 
    LEFT JOIN personaloaxaca om ON om.idPersonalOM = pd.idPersonalOM
    WHERE idReporte = :idReporte";
    $datos = $con->prepare($sqlPersonalDescarga);
    $datos->bindParam(':idReporte', $idReporte);
    $datos->execute();
    
    if($datos == FALSE){
        throw new Exception($con->errorInfo());
    }
    $resultado = $datos->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['error'=>false, 'message'=>'', 'data'=>$resultado]);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'data'=>$resultado]);
    exit();
}
