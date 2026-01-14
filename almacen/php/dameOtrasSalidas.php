<?php
include_once '../../DAOConeccion/conePDO.php';
include_once '../../controlAdministrativo/php/nombreDePersona.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $sqlSelecciona = $con->prepare("SELECT os.* FROM otrassalidas os
    ORDER BY fecha DESC");
    $sqlSelecciona->execute();
    if($sqlSelecciona == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $salidas = array();
    $resultado = $sqlSelecciona->fetchAll(PDO::FETCH_ASSOC);

    foreach($resultado as $r){
        $r['nombre'] = retornarNombre($con, $r['tipoDePersona'], $r['idPersona']);
        array_push($salidas, $r);
    }

    echo json_encode(['error'=>false, 'salidas'=>$salidas]);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}