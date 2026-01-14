<?php

function dameNombrePersonal($idPuesto, $con) {
    $sql = $con->prepare("SELECT nombre as nombre_completo, nombres, apellido_paterno,
    apellido_materno, CONCAT(apellido_paterno, ' ', apellido_materno) as apellidos, correo
    FROM personaloaxaca WHERE idPuesto = :idPuesto AND estado = 0 LIMIT 1");
    $sql->bindParam(':idPuesto', $idPuesto);
    $sql->execute();

    if($sql == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $personal = $sql->fetch(PDO::FETCH_ASSOC);
    }

    return $personal;
}

if (isset($_GET['idPersonal'])) {
    try {
        include_once '../../DAOConeccion/conePDO.php';
        $pdo = new conePDO(); $con = $pdo->conectar();
    
        $personal = dameNombrePersonal($_GET['idPersonal'], $con);
        echo json_encode(['error'=>false, 'personal'=>$personal]);
    } catch(Exception $e) {
        echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
    }
}
