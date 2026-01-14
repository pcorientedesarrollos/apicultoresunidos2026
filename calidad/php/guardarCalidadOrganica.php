<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$cn = $pdo->conectar();

$json = file_get_contents("php://input");


try {

    if (!$json) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($json);
        $info = $datos->valor;
        $idLoteExperimental = $_GET["idLoteExperimental"];
        $fechaProceso = date("Y-m-d", strtotime($info[0]->fechaProceso));
        $fechaEnvasado = date("Y-m-d", strtotime($info[0]->fechaEnvasado));
    }

    $cn->beginTransaction();

    $sqlExpe = $cn->prepare("INSERT INTO calidad_organico (idLoteExperimental, fechaProceso, fechaEnvasado, loteCliente, marcaFinalCliente, observaciones, muestraInterna,  kilosTotales, numeroDeTambores)
        VALUES (:idLoteExperimental, :fechaProceso, :fechaEnvasado, :loteCliente, :marcaFinalCliente, :observaciones, 0, :kilosTotales, :numeroDeTambores)");
    $sqlExpe->bindParam(':idLoteExperimental', $idLoteExperimental);
    $sqlExpe->bindParam(':fechaProceso', $fechaProceso);
    $sqlExpe->bindParam(':fechaEnvasado', $fechaEnvasado);
    $sqlExpe->bindParam(':loteCliente', $info[0]->loteCliente);
    $sqlExpe->bindParam(':marcaFinalCliente', $info[0]->marcaFinalCliente);
    $sqlExpe->bindParam(':observaciones', $info[0]->observaciones);
    $sqlExpe->bindParam(':kilosTotales', $info[1]->kilosTotales);
    $sqlExpe->bindParam(':numeroDeTambores', $info[1]->numeroDeTambores);

    $sqlExpe->execute();

    if ($sqlExpe == false) {
        throw new Exception($cn - errorInfo());
    }
    $idLoteInterno = $cn->lastInsertId();

    foreach ($info[2] as $arregloTambores) {
        $sqlTam = $cn->prepare("INSERT INTO tamboreslotes_organico (folioTambor, idLoteInterno) VALUES (:folioTambor, :idLoteInterno)");
        $sqlTam->bindParam(':folioTambor', $arregloTambores->folioTambor);
        $sqlTam->bindParam(':idLoteInterno', $idLoteInterno);
        $sqlTam->execute();
        if ($sqlTam == false) {
            throw new Exception($cn->errorInfo());
        }

        $sqlActualizaEstado = $cn->prepare("UPDATE almacen_organico SET estado = '2' WHERE idAlmacen = :idAlmacen");
        $sqlActualizaEstado->bindParam(':idAlmacen', $arregloTambores->folioTambor);
        $sqlActualizaEstado->execute();

        if ($sqlActualizaEstado == false) {
            throw new Exception($cn->errorInfo());
        }
    }

    $sqlUp = $cn->prepare("UPDATE experimental_organico SET loteInterno = '1' WHERE idLoteExperimental = '$idLoteExperimental'");
    $sqlUp->execute();

    if ($sqlUp == false) {
        throw new Exception($cn->errorInfo());
    }

    $cn->commit();
    echo json_encode(['error' => false, 'message' => 'Consulta realizada']);
} catch (Exception $e) {
    $cn->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
}