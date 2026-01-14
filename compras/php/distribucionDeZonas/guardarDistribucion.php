<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$json = file_get_contents("php://input");
try {
    if (!$json) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $info = json_decode($json);
    }

    $con->beginTransaction();

    $sqlInsert = $con->prepare("UPDATE zonas SET idcomprador = :idcomprador WHERE idzona = :idzona");
    $sqlInsert->bindParam(':idcomprador', $info->idcomprador);
    $sqlInsert->bindParam(':idzona', $info->idzona);
    $sqlInsert->execute();
    if ($sqlInsert == false) {
        throw new Exception($con->errorInfo());
    }

    $sql = $con->prepare("UPDATE localidades SET idzona = '0' WHERE idzona = :idzona");
    $sql->bindParam(':idzona', $info->idzona);
    $sql->execute();
    if ($sql == false) {
        throw new Exception($con->errorInfo());
    }

    foreach ($info->idlocalidad as $localidad) {
        $sql1 = $con->prepare("UPDATE localidades SET idzona = :idzona WHERE idlocalidad = :idlocalidad");
        $sql1->bindParam(':idzona', $info->idzona);
        $sql1->bindParam(':idlocalidad', $localidad);
        $sql1->execute();
        if ($sql1 == false) {
            throw new Exception($con->errorInfo());
        }
    }

    $mensaje = 'Se ha guardado el registro';

    $con->commit();
    echo json_encode(['error' => false, 'message' => $mensaje]);

} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine()]);
}
