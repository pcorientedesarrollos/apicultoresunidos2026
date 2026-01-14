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
        // $vigencia = date("Y-m-d", strtotime($info[0]->vigencia));
    }

    // $sql = "INSERT INTO choferes (operador, compania, licencia, remolque, vigencia, marca, modelo, placas, caja, unas, cabello, ropa) VALUES (:operador, :compania , :licencia, :remolque, :vigencia, :marca, :modelo, :placas, :caja, :unas, :cabello, :ropa)";
    $sql = "INSERT INTO choferes (operador, compania, licencia, remolque, vigencia) VALUES (:operador, :compania , :licencia, '0', :vigencia)";
    $insertarEncabezado = $con->prepare($sql);
    $insertarEncabezado->bindParam(':operador', $info[0]->operador);
    $insertarEncabezado->bindParam(':compania', $info[0]->compania);
    $insertarEncabezado->bindParam(':licencia', $info[0]->licencia);
    // $insertarEncabezado->bindParam(':remolque', $info[0]->remolque);
    $insertarEncabezado->bindParam(':vigencia', $info[0]->vigencia);
    // $insertarEncabezado->bindParam(':marca', $info[0]->marca);
    // $insertarEncabezado->bindParam(':modelo', $info[0]->modelo);
    // $insertarEncabezado->bindParam(':placas', $info[0]->placas);
    // $insertarEncabezado->bindParam(':caja', $info[0]->caja);
    // $insertarEncabezado->bindParam(':unas', $info[0]->unas);
    // $insertarEncabezado->bindParam(':cabello', $info[0]->cabello);
    // $insertarEncabezado->bindParam(':ropa', $info[0]->ropa);
    $insertarEncabezado->execute();

    if ($insertarEncabezado == false) {
        throw new Exception($con->errorInfo());
    } else {
        $idOperador = $con->lastInsertid();
    }

    foreach ($info[1] as $arregloPlacas) {
        $sql = "INSERT INTO placaschoferes (placa, idOperador, tipo, marca, modelo, remolque, marcaRemolque, modeloRemolque, placaRemolque) VALUES ( :placa, '$idOperador', :tipo, :marca, :modelo, :remolque, :marcaRemolque, :modeloRemolque, :placaRemolque)";
        $insertarDetalle = $con->prepare($sql);
        $insertarDetalle->bindParam(':placa', $arregloPlacas->placa);
        $insertarDetalle->bindParam(':tipo', $arregloPlacas->tipo);
        $insertarDetalle->bindParam(':marca', $arregloPlacas->marca);
        $insertarDetalle->bindParam(':modelo', $arregloPlacas->modelo);
        $insertarDetalle->bindParam(':remolque', $arregloPlacas->remolque);
        $insertarDetalle->bindParam(':marcaRemolque', $arregloPlacas->marcaRemolque);
        $insertarDetalle->bindParam(':modeloRemolque', $arregloPlacas->modeloRemolque);
        $insertarDetalle->bindParam(':placaRemolque', $arregloPlacas->placaRemolque);
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