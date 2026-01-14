<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$json = file_get_contents("php://input");
// $miInfo = file_get_contents("php://input");
// $info = json_decode($miInfo);

try {
    $con->beginTransaction();
    if (!$json) {
        throw new Exception('No se recibieron los parámetros');
    } else {
        $datos = json_decode($json);
    }

    if (isset($datos[0]->idEncabezadoSobrante)) {
        //Ya existe el registro
        $encabezadoEdicion = "UPDATE almacensobrantesencabezado SET fecha = :fecha, tipoDeMiel = :tipoDeMiel, sobrante = :sobrante, tipo = :tipo WHERE idEncabezadoSobrante = :idEncabezadoSobrante";
        $encabezadoSql = $con->prepare($encabezadoEdicion);
        $encabezadoSql->bindParam(':fecha', $datos[0]->fecha);
        $encabezadoSql->bindParam(':tipoDeMiel', $datos[0]->tipoDeMiel);
        $encabezadoSql->bindParam(':sobrante', $datos[0]->sobrante);
        $encabezadoSql->bindParam(':tipo', $datos[0]->tipo);
        $encabezadoSql->bindParam(':idEncabezadoSobrante', $datos[0]->idEncabezadoSobrante);
        $encabezadoSql->execute();
        // if ($encabezadoSql == false) {
        //     echo 'Hubo un error';
        // } else {
        //     echo 'Se modificó';
        // }
        foreach ($datos[1] as $detalle) {
            if ($detalle->sobrante != $datos[0]->sobrante) {
                $selSobrante = "SELECT MAX(consecutivo) AS consecutivo FROM almacensobrantes WHERE sobrante = :sobrante AND tipoDeMiel = :tipoDeMiel";
                $resp = $con->prepare($selSobrante);
                $resp->bindParam(':sobrante', $datos[0]->sobrante);
                $resp->bindParam(':tipoDeMiel', $datos[0]->tipoDeMiel);
                $resp->execute();
                $resp->bindColumn('consecutivo', $consecutivo);
                $resp->fetch(PDO::FETCH_BOUND);
                $consecutivoN = $consecutivo + 1;
            }else{
                $consecutivoN = $detalle->consecutivo;
            }

            $sqlUp = "UPDATE almacensobrantes SET fecha = :fecha, tipoDeMiel = :tipoDeMiel, bruto = :bruto, tara = :tara, neto = :neto, zona = :zona, lote = :lote, observaciones = :observaciones, sobrante = :sobrante, consecutivo = :consecutivo, referencia = :referencia WHERE idEntradaSobrante = :idEntradaSobrante";
            $datosC = $con->prepare($sqlUp);
            $datosC->bindParam(':fecha', $datos[0]->fecha);
            $datosC->bindParam(':tipoDeMiel', $datos[0]->tipoDeMiel);
            $datosC->bindParam(':bruto', $detalle->bruto);
            $datosC->bindParam(':tara', $detalle->tara);
            $datosC->bindParam(':neto', $detalle->neto);
            $datosC->bindParam(':zona', $detalle->zona);
            $datosC->bindParam(':lote', $detalle->lote);
            $datosC->bindParam(':observaciones', $detalle->observaciones);
            $datosC->bindParam(':sobrante', $datos[0]->sobrante);
            $datosC->bindParam(':consecutivo', $consecutivoN);
            $datosC->bindParam(':referencia', $detalle->referencia);
            $datosC->bindParam(':idEntradaSobrante', $detalle->idEntradaSobrante);
            $datosC->execute();
            // if ($datos == false) {
            //     echo 'Hubo un error';
            // } else {
            //     echo 'Se modificó';
            // }
        }
    } else {
        //Registro nuevo

        $numeroTambos = sizeof($datos[1]);

        $sqlEncabezado = "INSERT INTO almacensobrantesencabezado (fecha, tipoDeMiel, sobrante, tambos, tipo) VALUES (:fecha, :tipoDeMiel, :sobrante, :tambos, :tipo)";
        $encabezado = $con->prepare($sqlEncabezado);
        $encabezado->bindParam(':fecha', $datos[0]->fecha);
        $encabezado->bindParam(':tipoDeMiel', $datos[0]->tipoDeMiel);
        $encabezado->bindParam(':sobrante', $datos[0]->sobrante);
        $encabezado->bindParam(':tipo', $datos[0]->tipo);
        $encabezado->bindParam(':tambos', $numeroTambos);
        $encabezado->execute();
        $idEncabezado = $con->lastInsertId();

        foreach ($datos[1] as $detalle) {
            $sql = "INSERT INTO almacensobrantes (fecha, tipoDeMiel, bruto, tara, neto, zona, lote, observaciones, sobrante, consecutivo, estado, referencia, consecutivoEntrada) VALUES (:fecha, :tipoDeMiel, :bruto, :tara, :neto, :zona, :lote, :observaciones, :sobrante, 0, 0, :referencia, :consecutivoEntrada)";
            $dato = $con->prepare($sql);
            $dato->bindParam(':fecha', $datos[0]->fecha);
            $dato->bindParam(':tipoDeMiel', $datos[0]->tipoDeMiel);
            $dato->bindParam(':bruto', $detalle->bruto);
            $dato->bindParam(':tara', $detalle->tara);
            $dato->bindParam(':neto', $detalle->neto);
            $dato->bindParam(':zona', $detalle->zona);
            $dato->bindParam(':lote', $detalle->lote);
            $dato->bindParam(':observaciones', $detalle->observaciones);
            $dato->bindParam(':sobrante', $datos[0]->sobrante);
            $dato->bindParam(':referencia', $detalle->referencia);
            $dato->bindParam(':consecutivoEntrada', $idEncabezado);
            $dato->execute();
            $idActual = $con->lastInsertId();

            $sqlCount = "SELECT COUNT(idEntradaSobrante) AS consecutivo FROM almacensobrantes WHERE sobrante = :sobrante AND tipoDeMiel = :tipoDeMiel";
            $data = $con->prepare($sqlCount);
            $data->bindParam(':sobrante', $datos[0]->sobrante);
            $data->bindParam(':tipoDeMiel', $datos[0]->tipoDeMiel);
            $data->execute();
            $data->bindColumn('consecutivo', $consecutivo);
            $data->fetch(PDO::FETCH_BOUND);

            $sqlConsecutivo = "UPDATE almacensobrantes SET consecutivo = $consecutivo WHERE idEntradaSobrante = $idActual";
            $resp = $con->prepare($sqlConsecutivo);
            $resp->execute();
        }
    }
    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se han guardado los datos', 'swal' => 'success']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine(), 'swal' => 'error']);
    exit();
}
