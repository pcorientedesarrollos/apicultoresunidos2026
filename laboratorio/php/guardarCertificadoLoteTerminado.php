<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$postdata = file_get_contents('php://input');

try {

    if (!$postdata) {
        throw new Exception('No se recibieron datos');
    } else {
        $datosGuardar = json_decode($postdata);
        switch ($datosGuardar[0]->tipoDeMiel) {
            case '1':
                $encabezado = 'certificadoloteterminado_encabezado';
                $detalle = 'certificadoloteterminado_detalle';
                break;
            case '2':
                $encabezado = 'certificadoloteterminado_encabezado_organico';
                $detalle = 'certificadoloteterminado_detalle_organico';
                break;
            case '5':
                $encabezado = 'certificadoloteterminado_encabezado_mantequilla';
                $detalle = 'certificadoloteterminado_detalle_mantequilla';
                break;
            case '6':
                $encabezado = 'certificadoloteterminado_encabezado_altiplano';
                $detalle = 'certificadoloteterminado_detalle_altiplano';
                break;
            case '7':
                $encabezado = 'certificadoloteterminado_encabezado_naranjo';
                $detalle = 'certificadoloteterminado_detalle_naranjo';
                break;
            case '8':
                $encabezado = 'certificadoloteterminado_encabezado_aguacate';
                $detalle = 'certificadoloteterminado_detalle_aguacate';
                break;
            case '9':
                $encabezado = 'certificadoloteterminado_encabezado_mezquite';
                $detalle = 'certificadoloteterminado_detalle_mezquite';
                break;
        }
    }

    // iniciamos una transaccion

    $con->beginTransaction();


    // Primero insertar el encabezado para obtener el ID
    if (isset($datosGuardar[0]->idEncabezado)) {
        $queryInsertaEncabezado = $con->prepare("UPDATE $encabezado SET fecha = :fecha WHERE idLote = :idLoteInterno");
    } else {
        $queryInsertaEncabezado = $con->prepare("INSERT INTO $encabezado (fecha, idLote, idParametro) VALUES (:fecha, :idLoteInterno, 0)");
        // $queryInsertaEncabezado->bindParam(':idParametro', $datosGuardar[0]->idParametro);
    }
    $queryInsertaEncabezado->bindParam(':fecha', $datosGuardar[0]->fecha);
    $queryInsertaEncabezado->bindParam(':idLoteInterno', $datosGuardar[0]->idLote);
    $queryInsertaEncabezado->execute();
    if (!$queryInsertaEncabezado) {
        throw new Exception($con->errorInfo());
    }

    // Si se inserta podemos obtener el id
    if (isset($datosGuardar[0]->idEncabezado)) {
        $idEncabezado = $datosGuardar[0]->idEncabezado;
    } else {
        $idEncabezado = $con->lastInsertId();
    }

    foreach ($datosGuardar[1]->caracteristicas as $caracteristica) {
        if (isset($caracteristica->idDetalle)) {
            $queryInsertaCara = $con->prepare("UPDATE $detalle SET parametro = :parametro, resultado = :resultado, desviacion = :desviacion, comentario = :comentario, minimo = :minimo, maximo = :maximo, tecnicas = :tecnicas WHERE idDetalle = :idDetalle");
            $queryInsertaCara->bindParam(':idDetalle', $caracteristica->idDetalle);
        } else {
            $queryInsertaCara = $con->prepare("INSERT INTO $detalle (idEncabezado, idAnalisis, parametro, resultado, desviacion, comentario, minimo, maximo, tecnicas) VALUES (:idEncabezado, :idAnalisis, :parametro, :resultado, :desviacion, :comentario, :minimo, :maximo, :tecnicas)");
            $queryInsertaCara->bindParam(':idEncabezado', $idEncabezado);
            $queryInsertaCara->bindParam(':idAnalisis', $caracteristica->idAnalisis);
        }
        $parametro = $caracteristica->minimo .' - '. $caracteristica->maximo;
        $queryInsertaCara->bindParam(':parametro', $parametro);
        $queryInsertaCara->bindParam(':resultado', $caracteristica->resultado);
        $queryInsertaCara->bindParam(':desviacion', $caracteristica->desviacion);
        $queryInsertaCara->bindParam(':comentario', $caracteristica->comentario);
        $queryInsertaCara->bindParam(':minimo', $caracteristica->minimo);
        $queryInsertaCara->bindParam(':maximo', $caracteristica->maximo);
        $queryInsertaCara->bindParam(':tecnicas', $caracteristica->tecnicas);
        // $queryInsertaCara->bindParam(':cuadroSensoriales', $caracteristica->cuadroSensoriales);

        $queryInsertaCara->execute();
        if (!$queryInsertaCara) {
            throw new Exception($con->errorInfo());
        }
    }

    foreach ($datosGuardar[1]->antibioticos as $analisis) {
        if (isset($analisis->idDetalle)) {
            $queryInsertaAnalisis = $con->prepare("UPDATE $detalle SET parametro = :parametro, resultado = :resultado, desviacion = :desviacion, comentario = :comentario, minimo = :minimo, maximo = :maximo, tecnicas = :tecnicas WHERE idDetalle = :idDetalle");
            $queryInsertaAnalisis->bindParam(':idDetalle', $analisis->idDetalle);
        } else {
            $queryInsertaAnalisis = $con->prepare("INSERT INTO $detalle (idEncabezado, idAnalisis, parametro, resultado, desviacion, comentario, minimo, maximo, tecnicas)
            VALUES (:idEncabezado, :idAnalisis, :parametro, :resultado, :desviacion, :comentario, :minimo, :maximo, :tecnicas)");
            $queryInsertaAnalisis->bindParam(':idEncabezado', $idEncabezado);
            $queryInsertaAnalisis->bindParam(':idAnalisis', $analisis->idAnalisis);
        }
        // $parametro = $analisis->minimo .' - '. $analisis->maximo;
        $queryInsertaAnalisis->bindParam(':parametro', $analisis->parametro);
        $queryInsertaAnalisis->bindParam(':resultado', $analisis->resultado);
        $queryInsertaAnalisis->bindParam(':desviacion', $analisis->desviacion);
        $queryInsertaAnalisis->bindParam(':comentario', $analisis->comentario);
        $queryInsertaAnalisis->bindParam(':minimo', $analisis->minimo);
        $queryInsertaAnalisis->bindParam(':maximo', $analisis->maximo);
        $queryInsertaAnalisis->bindParam(':tecnicas', $analisis->tecnicas);

        $queryInsertaAnalisis->execute();
        if (!$queryInsertaAnalisis) {
            throw new Exception($con->errorInfo());
        }
    }

    foreach ($datosGuardar[1]->microbiologicos as $microbiologico) {
        if (isset($microbiologico->idDetalle)) {
            $queryInsertamicrobiologicos = $con->prepare("UPDATE $detalle SET parametro = :parametro, resultado = :resultado, desviacion = :desviacion, comentario = :comentario, minimo = :minimo, maximo = :maximo, tecnicas = :tecnicas WHERE idDetalle = :idDetalle");
            $queryInsertamicrobiologicos->bindParam(':idDetalle', $microbiologico->idDetalle);
        } else {
            $queryInsertamicrobiologicos = $con->prepare("INSERT INTO $detalle (idEncabezado, idAnalisis, parametro, resultado, desviacion, comentario, minimo, maximo, tecnicas)
            VALUES (:idEncabezado, :idAnalisis, :parametro, :resultado, :desviacion, :comentario, :minimo, :maximo, :tecnicas)");
            $queryInsertamicrobiologicos->bindParam(':idEncabezado', $idEncabezado);
            $queryInsertamicrobiologicos->bindParam(':idAnalisis', $microbiologico->idAnalisis);
        }
        $queryInsertamicrobiologicos->bindParam(':parametro', $microbiologico->parametro);
        $queryInsertamicrobiologicos->bindParam(':resultado', $microbiologico->resultado);
        $queryInsertamicrobiologicos->bindParam(':desviacion', $microbiologico->desviacion);
        $queryInsertamicrobiologicos->bindParam(':comentario', $microbiologico->comentario);
        $queryInsertamicrobiologicos->bindParam(':minimo', $microbiologico->minimo);
        $queryInsertamicrobiologicos->bindParam(':maximo', $microbiologico->maximo);
        $queryInsertamicrobiologicos->bindParam(':tecnicas', $microbiologico->tecnicas);

        $queryInsertamicrobiologicos->execute();
        if (!$queryInsertamicrobiologicos) {
            throw new Exception($con->errorInfo());
        }
    }

    foreach ($datosGuardar[1]->sensoriales as $sensorial) {
        if (isset($sensorial->idDetalle)) {
            $queryInsertasensoriales = $con->prepare("UPDATE $detalle SET parametro = :parametro, resultado = :resultado, desviacion = :desviacion, comentario = :comentario, minimo = :minimo, maximo = :maximo, tecnicas = :tecnicas, cuadroSensoriales = :cuadroSensoriales WHERE idDetalle = :idDetalle");
            $queryInsertasensoriales->bindParam(':idDetalle', $sensorial->idDetalle);
        } else {
            $queryInsertasensoriales = $con->prepare("INSERT INTO $detalle (idEncabezado, idAnalisis, parametro, resultado, desviacion, comentario, minimo, maximo, tecnicas, cuadroSensoriales)
            VALUES (:idEncabezado, :idAnalisis, :parametro, :resultado, :desviacion, :comentario, :minimo, :maximo, :tecnicas, :cuadroSensoriales)");
            $queryInsertasensoriales->bindParam(':idEncabezado', $idEncabezado);
            $queryInsertasensoriales->bindParam(':idAnalisis', $sensorial->idAnalisis);
        }
        $queryInsertasensoriales->bindParam(':parametro', $sensorial->parametro);
        $queryInsertasensoriales->bindParam(':resultado', $sensorial->resultado);
        $queryInsertasensoriales->bindParam(':desviacion', $sensorial->desviacion);
        $queryInsertasensoriales->bindParam(':comentario', $sensorial->comentario);
        $queryInsertasensoriales->bindParam(':minimo', $sensorial->minimo);
        $queryInsertasensoriales->bindParam(':maximo', $sensorial->maximo);
        $queryInsertasensoriales->bindParam(':tecnicas', $sensorial->tecnicas);
        $queryInsertasensoriales->bindParam(':cuadroSensoriales', $sensorial->cuadroSensoriales);


        $queryInsertasensoriales->execute();
        if (!$queryInsertasensoriales) {
            throw new Exception($con->errorInfo());
        }
    }

    // Si llega a este punto, es que no hubo algun error
    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha guardado un nuevo registro']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
