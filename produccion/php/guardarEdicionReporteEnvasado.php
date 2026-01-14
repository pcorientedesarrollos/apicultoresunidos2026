<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
ini_set('max_execution_time', 300);
$data = file_get_contents('php://input');
try {
    $con->beginTransaction();
    if (!$data) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($data);
        $info = $datos->valor;
        $tipoDeMiel = $datos->tipoDeMiel;
    }

    switch ($tipoDeMiel) {
        case '1':
            $reportesdeenvasados_tabla = 'reportesdeenvasados';
            $pesosenvasados_tabla = 'pesosenvasados';
            $tamboreslotes_tabla = 'tamboreslotes';
            $almacen_tabla = 'almacen';
            break;
        case '2':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_organico';
            $pesosenvasados_tabla = 'pesosenvasados_organico';
            $tamboreslotes_tabla = 'tamboreslotes_organico';
            $almacen_tabla = 'almacen_organico';
            break;
            
        case '5':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_mantequilla';
            $pesosenvasados_tabla = 'pesosenvasados_mantequilla';
            $tamboreslotes_tabla = 'tamboreslotes_mantequilla';
            $almacen_tabla = 'almacen_mantequilla';
            break;
            
        case '6':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_altiplano';
            $pesosenvasados_tabla = 'pesosenvasados_altiplano';
            $tamboreslotes_tabla = 'tamboreslotes_altiplano';
            $almacen_tabla = 'almacen_altiplano';
            break;
            
        case '7':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_naranjo';
            $pesosenvasados_tabla = 'pesosenvasados_naranjo';
            $tamboreslotes_tabla = 'tamboreslotes_naranjo';
            $almacen_tabla = 'almacen_naranjo';
            break;
            
        case '8':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_aguacate';
            $pesosenvasados_tabla = 'pesosenvasados_aguacate';
            $tamboreslotes_tabla = 'tamboreslotes_aguacate';
            $almacen_tabla = 'almacen_aguacate';
            break;
            
        case '9':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_mezquite';
            $pesosenvasados_tabla = 'pesosenvasados_mezquite';
            $tamboreslotes_tabla = 'tamboreslotes_mezquite';
            $almacen_tabla = 'almacen_mezquite';
            break;
        default:
            throw new Exception('Tipo de miel inválido');
            break;
    }

    
    // Seleccionar idLoteInterno que tiene guardardo y cambiar el estado de los tambores a 2
    $sqlSeleccionaLote = $con->prepare("SELECT idLoteInterno FROM $reportesdeenvasados_tabla WHERE idReporteEnvasado = :idReporteEnvasado");
    $sqlSeleccionaLote->bindParam(':idReporteEnvasado', $info[0]->idReporteEnvasado);
    $sqlSeleccionaLote->bindColumn('idLoteInterno', $anteriorLoteInterno);
    $sqlSeleccionaLote->execute();
    if ($sqlSeleccionaLote == false) {
        throw new Exception($con->errorInfo());
    } else {
        $sqlSeleccionaLote->fetch(PDO::FETCH_BOUND);
    }

    // Regresar al estado 2

    // Primero seleccionar los folios y luego hacer el cambio

    $sqlSeleccionaFolios = $con->prepare("SELECT CONCAT(folioTambor, '-', tipo, '-', clasificacion) as folioTambor
    FROM $tamboreslotes_tabla WHERE idLoteInterno = :idLoteInterno");
    $sqlSeleccionaFolios->bindParam(':idLoteInterno', $anteriorLoteInterno);
    $sqlSeleccionaFolios->execute();
    if ($sqlSeleccionaFolios == false) {
        throw new Exception($con->errorInfo());
    } else {
        $folios = $sqlSeleccionaFolios->fetchAll(PDO::FETCH_ASSOC);
    }

    if ($folios) {
        foreach ($folios as $tambor) {
            $tambor = explode('-', $tambor['folioTambor']); // Para separar los valores del folio que está concatenado
            if ($tambor[1] == '1' && $tambor[2] != '0') {
                // Si es sobrante
                $sqlUpdate = $con->prepare("UPDATE almacensobrantes SET estado = 2 WHERE consecutivo = :idAlmacen AND tipoDeMiel = :tipoDeMiel AND sobrante = :clasificacion");
                $sqlUpdate->bindParam(':idAlmacen', $tambor[0]);
                $sqlUpdate->bindParam(':tipoDeMiel', $tipoDeMiel);
                $sqlUpdate->bindParam(':clasificacion', $tambor[2]);
                $sqlUpdate->execute();
                if ($sqlUpdate == false) {
                    throw new Exception($con->errorInfo());
                }
            } else {
                // Si no es sobrante
                $sqlActualizaTamboresLoteAnterior = $con->prepare("UPDATE $almacen_tabla SET estado = 2 WHERE idAlmacen = :idAlmacen");
                $sqlActualizaTamboresLoteAnterior->bindParam(':idAlmacen', $tambor[0]);
                $sqlActualizaTamboresLoteAnterior->execute();

                if ($sqlActualizaTamboresLoteAnterior == false) {
                    throw new Exception($con->errorInfo());
                }
            }
        }
    }

    // Cambiar el nuevo lote
    // Seleccionar los folios del nuevo lote y repetir la operación anterior sustituyendo el estado del tambor a 4

    $sqlSeleccionaFolios = $con->prepare("SELECT CONCAT(folioTambor, '-', tipo, '-', clasificacion) as folioTambor
    FROM $tamboreslotes_tabla WHERE idLoteInterno = :idLoteInterno");
    $sqlSeleccionaFolios->bindParam(':idLoteInterno', $info[0]->idLoteInterno);
    $sqlSeleccionaFolios->execute();
    if ($sqlSeleccionaFolios == false) {
        throw new Exception($con->errorInfo());
    } else {
        $foliosNuevoLote = $sqlSeleccionaFolios->fetchAll(PDO::FETCH_ASSOC);
    }

    if ($foliosNuevoLote) {
        foreach ($foliosNuevoLote as $tambor) {
            $tambor = explode('-', $tambor['folioTambor']); // Para separar los valores del folio que está concatenado
            if ($tambor[1] == '1' && $tambor[2] != '0') {
                // Si es sobrante
                $sqlUpdate = $con->prepare("UPDATE almacensobrantes SET estado = 4 WHERE consecutivo = :idAlmacen AND tipoDeMiel = :tipoDeMiel AND sobrante = :clasificacion");
                $sqlUpdate->bindParam(':idAlmacen', $tambor[0]);
                $sqlUpdate->bindParam(':tipoDeMiel', $tipoDeMiel);
                $sqlUpdate->bindParam(':clasificacion', $tambor[2]);
                $sqlUpdate->execute();
                if ($sqlUpdate == false) {
                    throw new Exception($con->errorInfo());
                }
            } else {
                // Si no es sobrante
                $sqlActualizaTamboresLoteAnterior = $con->prepare("UPDATE $almacen_tabla SET estado = 4 WHERE idAlmacen = :idAlmacen");
                $sqlActualizaTamboresLoteAnterior->bindParam(':idAlmacen', $tambor[0]);
                $sqlActualizaTamboresLoteAnterior->execute();

                if ($sqlActualizaTamboresLoteAnterior == false) {
                    throw new Exception($con->errorInfo());
                }
            }
        }
    }

    $totalDeTambores = count($info[1]);
    $sqlProceso = "UPDATE $reportesdeenvasados_tabla SET idLoteInterno = :idLoteInterno, numeroTanque = :numeroTanque,
    horaInicio = :horaInicio, horaFinal = :horaFinal, tiempo = :tiempo, observaciones = :observaciones,
    kilosProcesados = :kilosProcesados, netosEnvasados = :netosEnvasados, merma = :merma, faltante= :faltante, totalDeTambores = :totalDeTambores,
    observacionesDePesos = :observacionesDePesos WHERE idReporteEnvasado = :idReporteEnvasado";
    $dato = $con->prepare($sqlProceso);
    $dato->bindParam(':idLoteInterno', $info[0]->idLoteInterno);
    $dato->bindParam(':numeroTanque', $info[0]->numeroTanque);
    $dato->bindParam(':horaInicio', $info[0]->horaInicio);
    $dato->bindParam(':horaFinal', $info[0]->horaFinal);
    $dato->bindParam(':tiempo', $info[0]->tiempo);
    $dato->bindParam(':observaciones', $info[0]->observaciones);
    $dato->bindParam(':kilosProcesados', $info[0]->kilosProcesados);
    $dato->bindParam(':netosEnvasados', $info[0]->netosEnvasados);
    $dato->bindParam(':merma', $info[0]->merma);
    $dato->bindParam(':faltante', $info[0]->faltante);
    $dato->bindParam(':totalDeTambores', $totalDeTambores);
    $dato->bindParam(':idReporteEnvasado', $info[0]->idReporteEnvasado);
    $dato->bindParam(':observacionesDePesos', $info[0]->observacionesDePesos);
    $dato->execute();

    if ($dato == false) {
        throw new Exception($con->errorInfo());
    }


    // Delete and the insert

    $sqlDetelePesos = $con->prepare("DELETE FROM $pesosenvasados_tabla WHERE idReporteEnvasado = :idReporteEnvasado");
    $sqlDetelePesos->bindParam(':idReporteEnvasado', $info[0]->idReporteEnvasado);
    $sqlDetelePesos->execute();
    if ($sqlDetelePesos == false) {
        throw new Exception($con->errorInfo());
    }

    foreach ($info[1] as $envasados) {
        $sqlTambosEnvasados = $con->prepare("INSERT INTO $pesosenvasados_tabla (folio, bruto, tara, neto, idReporteEnvasado)
        VALUES (:folio, :bruto, :tara, :neto, :idReporteEnvasado)");
        $sqlTambosEnvasados->bindParam(':folio', $envasados->folio);
        $sqlTambosEnvasados->bindParam(':bruto', $envasados->bruto);
        $sqlTambosEnvasados->bindParam(':tara', $envasados->tara);
        $sqlTambosEnvasados->bindParam(':neto', $envasados->neto);
        $sqlTambosEnvasados->bindParam(':idReporteEnvasado', $info[0]->idReporteEnvasado);
        $sqlTambosEnvasados->execute();
        if ($sqlTambosEnvasados == false) {
            throw new Exception($con->errorInfo());
        }
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha actualizado el registro']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
