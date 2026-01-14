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
    // $sql = "UPDATE choferes SET operador = :operador, compania = :compania, licencia = :licencia, remolque = :remolque, vigencia = :vigencia, marca = :marca, modelo = :modelo, placas = :placas, caja = :caja, unas = :unas, cabello = :cabello, ropa = :ropa WHERE idOperador = :idOperador";
    $sql = "UPDATE choferes SET operador = :operador, compania = :compania, licencia = :licencia, vigencia = :vigencia WHERE idOperador = :idOperador";
    $insertarDetalle = $con->prepare($sql);
    $insertarDetalle->bindParam(':operador', $info[0]->operador);
    $insertarDetalle->bindParam(':compania', $info[0]->compania);
    $insertarDetalle->bindParam(':licencia', $info[0]->licencia);
    // $insertarDetalle->bindParam(':remolque', $info[0]->remolque);
    $insertarDetalle->bindParam(':vigencia', $info[0]->vigencia);
    // $insertarDetalle->bindParam(':marca', $info[0]->marca);
    // $insertarDetalle->bindParam(':modelo', $info[0]->modelo);
    // $insertarDetalle->bindParam(':placas', $info[0]->placas);
    // $insertarDetalle->bindParam(':caja', $info[0]->caja);
    // $insertarDetalle->bindParam(':unas', $info[0]->unas);
    // $insertarDetalle->bindParam(':cabello', $info[0]->cabello);
    // $insertarDetalle->bindParam(':ropa', $info[0]->ropa);
    $insertarDetalle->bindParam(':idOperador', $info[0]->idOperador);
    $insertarDetalle->execute();

    if ($insertarDetalle == false) {
        throw new Exception($con->errorInfo());
    } else {
        foreach ($info[1] as $arregloPlacas) {
            if ($arregloPlacas->idPlaca > 0) {
                $sqlPlacas = "UPDATE placaschoferes SET placa = :placa, tipo = :tipo, marca = :marca, modelo = :modelo, remolque = :remolque, marcaRemolque = :marcaRemolque, modeloRemolque = :modeloRemolque, placaRemolque = :placaRemolque WHERE idPlaca = :idPlaca";
                $datosPlacas = $con->prepare($sqlPlacas);
                $datosPlacas->bindParam(':placa', $arregloPlacas->placa);
                $datosPlacas->bindParam(':idPlaca', $arregloPlacas->idPlaca);
                $datosPlacas->bindParam(':tipo', $arregloPlacas->tipo);
                $datosPlacas->bindParam(':marca', $arregloPlacas->marca);
                $datosPlacas->bindParam(':modelo', $arregloPlacas->modelo);
                $datosPlacas->bindParam(':remolque', $arregloPlacas->remolque);
                $datosPlacas->bindParam(':marcaRemolque', $arregloPlacas->marcaRemolque);
                $datosPlacas->bindParam(':modeloRemolque', $arregloPlacas->modeloRemolque);
                $datosPlacas->bindParam(':placaRemolque', $arregloPlacas->placaRemolque);
                $datosPlacas->execute();
                if ($datosPlacas == false) {
                    throw new Exception($con->errorInfo());
                }
            } else {
                $sqlPlacas1 = "INSERT INTO placaschoferes (placa, idOperador, tipo, marca, modelo, remolque, marcaRemolque, modeloRemolque, placaRemolque) VALUES ( :placa, :idOperador, :tipo, :marca, :modelo, :remolque, :marcaRemolque, :modeloRemolque, :placaRemolque)";
                $datosPlacas1 = $con->prepare($sqlPlacas1);
                $datosPlacas1->bindParam(':placa', $arregloPlacas->placa);
                $datosPlacas1->bindParam(':idOperador', $info[0]->idOperador);
                $datosPlacas1->bindParam(':tipo', $arregloPlacas->tipo);
                $datosPlacas1->bindParam(':marca', $arregloPlacas->marca);
                $datosPlacas1->bindParam(':modelo', $arregloPlacas->modelo);
                $datosPlacas1->bindParam(':remolque', $arregloPlacas->remolque);
                $datosPlacas1->bindParam(':marcaRemolque', $arregloPlacas->marcaRemolque);
                $datosPlacas1->bindParam(':modeloRemolque', $arregloPlacas->modeloRemolque);
                $datosPlacas1->bindParam(':placaRemolque', $arregloPlacas->placaRemolque);
                $datosPlacas1->execute();
                if ($datosPlacas1 == false) {
                    throw new Exception($con->errorInfo());
                }
            }
        }
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se han guardado los datos', 'swal' => 'success']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine(), 'swal' => 'error']);
    exit();
}
