<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$json = file_get_contents("php://input");

try {
    $con->beginTransaction();
    if (!$json) {
        throw new Exception('No se recibieron los parámetros');
    } else {
        $datos = json_decode($json);
        $info = $datos->valor;
        $nombre_completo = join(' ', [$info[0]->nombres, $info[0]->apellido_paterno, $info[0]->apellido_materno]);
    }

    $sql = "INSERT INTO personaloaxaca (idArea, clave, nombre, nombres, apellido_paterno, apellido_materno,
    idPuesto, correo, estado) VALUES (:idArea, :clave, :nombre, :nombres, :paterno, :materno, :idPuesto, :correo, '0')";
    $insertarEncabezado = $con->prepare($sql);
    $insertarEncabezado->bindParam(':idArea', $info[0]->idArea);
    $insertarEncabezado->bindParam(':clave', $info[0]->clave);
    $insertarEncabezado->bindParam(':nombre', $nombre_completo);
    $insertarEncabezado->bindParam(':nombres', $info[0]->nombres);
    $insertarEncabezado->bindParam(':paterno', $info[0]->apellido_paterno);
    $insertarEncabezado->bindParam(':materno', $info[0]->apellido_materno);
    $insertarEncabezado->bindParam(':idPuesto', $info[0]->idPuesto);
    $insertarEncabezado->bindParam(':correo', $info[0]->correo);
    $insertarEncabezado->execute();

    if ($insertarEncabezado == false) {
        throw new Exception($con->errorInfo());
    } else {
        $idPersonal = $con->lastInsertid();
    }

    foreach ($info[1] as $periodos) {
        $sql = "INSERT INTO periodos_personal (fechaUno, fechaDos, idPersonal) VALUES ( :fechaUno, :fechaDos, '$idPersonal')";
        $insertarDetalle = $con->prepare($sql);
        $insertarDetalle->bindParam(':fechaUno', $periodos->fechaUno);
        $insertarDetalle->bindParam(':fechaDos', $periodos->fechaDos);
        $insertarDetalle->execute();
        if ($insertarDetalle == false) {
            throw new Exception($con->errorInfo());
        }
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se han guardado los datos', 'swal' => 'success']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine(), 'swal' => 'error']);
    exit();
}
