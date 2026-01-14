<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$data = file_get_contents('php://input');
ini_set('max_execution_time', 300);

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
            $personalenvasado_tabla = 'personalenvasado';
            $ayudantesinternos_tabla = 'ayudantesinternos';
            $ayudantesexternos_tabla = 'ayudantesexternos';
            $pesosenvasados_tabla = 'pesosenvasados';
            $tamboreslotes_tabla = 'tamboreslotes';
            $almacen_tabla = 'almacen';
            break;
        case '2':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_organico';
            $personalenvasado_tabla = 'personalenvasado_organico';
            $ayudantesinternos_tabla = 'ayudantesinternos_organico';
            $ayudantesexternos_tabla = 'ayudantesexternos_organico';
            $pesosenvasados_tabla = 'pesosenvasados_organico';
            $tamboreslotes_tabla = 'tamboreslotes_organico';
            $almacen_tabla = 'almacen_organico';
            break;
            
        case '5':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_mantequilla';
            $personalenvasado_tabla = 'personalenvasado_mantequilla';
            $ayudantesinternos_tabla = 'ayudantesinternos_mantequilla';
            $ayudantesexternos_tabla = 'ayudantesexternos_mantequilla';
            $pesosenvasados_tabla = 'pesosenvasados_mantequilla';
            $tamboreslotes_tabla = 'tamboreslotes_mantequilla';
            $almacen_tabla = 'almacen_mantequilla';
            break;
            
        case '6':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_altiplano';
            $personalenvasado_tabla = 'personalenvasado_altiplano';
            $ayudantesinternos_tabla = 'ayudantesinternos_altiplano';
            $ayudantesexternos_tabla = 'ayudantesexternos_altiplano';
            $pesosenvasados_tabla = 'pesosenvasados_altiplano';
            $tamboreslotes_tabla = 'tamboreslotes_altiplano';
            $almacen_tabla = 'almacen_altiplano';
            break;
            
        case '7':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_naranjo';
            $personalenvasado_tabla = 'personalenvasado_naranjo';
            $ayudantesinternos_tabla = 'ayudantesinternos_naranjo';
            $ayudantesexternos_tabla = 'ayudantesexternos_naranjo';
            $pesosenvasados_tabla = 'pesosenvasados_naranjo';
            $tamboreslotes_tabla = 'tamboreslotes_naranjo';
            $almacen_tabla = 'almacen_naranjo';
            break;
            
        case '8':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_aguacate';
            $personalenvasado_tabla = 'personalenvasado_aguacate';
            $ayudantesinternos_tabla = 'ayudantesinternos_aguacate';
            $ayudantesexternos_tabla = 'ayudantesexternos_aguacate';
            $pesosenvasados_tabla = 'pesosenvasados_aguacate';
            $tamboreslotes_tabla = 'tamboreslotes_aguacate';
            $almacen_tabla = 'almacen_aguacate';
            break;
            
        case '9':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_mezquite';
            $personalenvasado_tabla = 'personalenvasado_mezquite';
            $ayudantesinternos_tabla = 'ayudantesinternos_mezquite';
            $ayudantesexternos_tabla = 'ayudantesexternos_mezquite';
            $pesosenvasados_tabla = 'pesosenvasados_mezquite';
            $tamboreslotes_tabla = 'tamboreslotes_mezquite';
            $almacen_tabla = 'almacen_mezquite';
            break;
        default:
            throw new Exception('Tipo de miel inválido');
            break;
    }

    $sqlEnvasado = $con->prepare("INSERT INTO $reportesdeenvasados_tabla (idLoteInterno, numeroTanque, horaInicio, horaFinal,
    tiempo, observaciones, kilosProcesados, netosEnvasados, merma, faltante, totalDeTambores, observacionesDePesos)
    VALUES (:idLoteInterno , :numeroTanque,  :horaInicio, :horaFinal, :tiempo, :observaciones, :kilosProcesados,
    :netosEnvasados, :merma, :faltante, :totalDeTambores, :observacionesDePesos)");
    $sqlEnvasado->bindParam(':idLoteInterno', $info[0]->idLoteInterno);
    $sqlEnvasado->bindParam(':numeroTanque', $info[0]->numeroTanque);
    $sqlEnvasado->bindParam(':horaInicio', $info[0]->horaInicio);
    $sqlEnvasado->bindParam(':horaFinal', $info[0]->horaFinal);
    $sqlEnvasado->bindParam(':tiempo', $info[0]->tiempo);
    $sqlEnvasado->bindParam(':observaciones', $info[0]->observaciones);
    $sqlEnvasado->bindParam(':kilosProcesados', $info[0]->kilosProcesados);
    $sqlEnvasado->bindParam(':netosEnvasados', $info[0]->netosEnvasados);
    $sqlEnvasado->bindParam(':merma', $info[0]->merma);
    $sqlEnvasado->bindParam(':faltante', $info[0]->faltante);
    $sqlEnvasado->bindParam(':totalDeTambores', $info[0]->totalDeTambores);
    $sqlEnvasado->bindParam(':observacionesDePesos', $info[0]->observacionesDePesos);
    $sqlEnvasado->execute();
    if ($sqlEnvasado == false) {
        throw new Exception($con->errorInfo());
    }

    $idReporteEnvasado = $con->lastInsertId();

    foreach ($info[1] as $personalEnvasado) {
        $sqlPersonalEnvasado = $con->prepare("INSERT INTO $personalenvasado_tabla (idPersonalOM, idReporteEnvasado)
        VALUES (:personalEnvasado, :idReporteEnvasado)");

        $sqlPersonalEnvasado->bindParam(':personalEnvasado', $personalEnvasado);
        $sqlPersonalEnvasado->bindParam(':idReporteEnvasado', $idReporteEnvasado);
        $sqlPersonalEnvasado->execute();
        if ($sqlPersonalEnvasado == false) {
            throw new Exception($con->errorInfo());
        }
    }

    foreach ($info[2] as $personalAyudanteInterno) {
        $sqlPersonalAyudanteInterno = $con->prepare("INSERT INTO $ayudantesinternos_tabla (idPersonalOM, idReporteEnvasado) VALUES
        (:personalAyudanteInterno, :idReporteEnvasado)");
        $sqlPersonalAyudanteInterno->bindParam(':personalAyudanteInterno', $personalAyudanteInterno);
        $sqlPersonalAyudanteInterno->bindParam(':idReporteEnvasado', $idReporteEnvasado);
        $sqlPersonalAyudanteInterno->execute();
        if ($sqlPersonalAyudanteInterno == false) {
            throw new Exception($con->errorInfo());
        }
    }

    foreach ($info[3] as $personalAyudanteExterno) {
        $sqlPersonalAyudanteExterno = $con->prepare("INSERT INTO $ayudantesexternos_tabla (idPersonalOM, idReporteEnvasado)
        VALUES (:personalAyudanteExterno, :idReporteEnvasado)");
        $sqlPersonalAyudanteExterno->bindParam(':personalAyudanteExterno', $personalAyudanteExterno);
        $sqlPersonalAyudanteExterno->bindParam(':idReporteEnvasado', $idReporteEnvasado);
        $sqlPersonalAyudanteExterno->execute();
        if ($sqlPersonalAyudanteExterno == false) {
            throw new Exception($con->errorInfo());
        }
    }

    foreach ($info[4] as $envasados) {
        $sqlTambosEnvasados = $con->prepare("INSERT INTO $pesosenvasados_tabla (folio, bruto, tara, neto, idReporteEnvasado)
        VALUES (:folio, :bruto, :tara, :neto, :idReporteEnvasado)");
        $sqlTambosEnvasados->bindParam(':folio', $envasados->folio);
        $sqlTambosEnvasados->bindParam(':bruto', $envasados->bruto);
        $sqlTambosEnvasados->bindParam(':tara', $envasados->tara);
        $sqlTambosEnvasados->bindParam(':neto', $envasados->neto);
        $sqlTambosEnvasados->bindParam(':idReporteEnvasado', $idReporteEnvasado);
        $sqlTambosEnvasados->execute();
        if ($sqlTambosEnvasados == false) {
            throw new Exception($con->errorInfo());
        }
    }

    // Seleccionar los folios de los tambores en tamboreslotes
    $seleccionaTambores = $con->prepare("SELECT CONCAT(folioTambor, '-', tipo, '-', clasificacion) as folioTambor FROM $tamboreslotes_tabla WHERE idLoteInterno = :idLoteInterno");
    $seleccionaTambores->bindParam(':idLoteInterno', $info[0]->idLoteInterno);
    $seleccionaTambores->execute();
    if ($seleccionaTambores == false) {
        throw new Exception($con->errorInfo());
    }

    foreach ($seleccionaTambores->fetchAll(PDO::FETCH_ASSOC) as $tambor) {

        $tambor = explode('-', $tambor['folioTambor']);
        // $tambor[0] -> idAlmacen
        // $tambor[1] -> sobrante
        // $tambor[2] -> clasificacion, en caso de ser sobrante


        if ($tambor[1] == '1' && $tambor[2] != '0') {
            $sqlUpdate = $con->prepare("UPDATE almacensobrantes SET estado = 4
            WHERE consecutivo = :idAlmacen AND tipoDeMiel = :tipoDeMiel AND sobrante = :clasificacion");
            $sqlUpdate->bindParam(':idAlmacen', $tambor[0]);
            $sqlUpdate->bindParam(':tipoDeMiel', $tipoDeMiel);
            $sqlUpdate->bindParam(':clasificacion', $tambor[2]);
            $sqlUpdate->execute();
            if ($sqlUpdate == false) {
                throw new Exception($con->errorInfo());
            }
        } else {
            $sqlActualizaTambores = $con->prepare("UPDATE $almacen_tabla SET estado = 4 WHERE idAlmacen = :idAlmacen");
            $sqlActualizaTambores->bindParam(':idAlmacen', $tambor[0]);
            $sqlActualizaTambores->execute();
            if ($sqlActualizaTambores == false) {
                throw new Exception($con->errorInfo());
            }
        }

    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha guardado el reporte']);

} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
