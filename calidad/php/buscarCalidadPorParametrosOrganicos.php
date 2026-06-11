<?php
/**
 * Búsqueda de calidad por parámetros - Miel Orgánica
 * MIGRADO A PDO - Compatible con PHP 8.4
 * Fecha migración: 2026-03-16
 */

include_once '../../DAOConeccion/conePDO.php';

try {
    $pdo = new conePDO();
    $con = $pdo->conectar();
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $json = file_get_contents("php://input");
    $datos = json_decode($json);

    if (!$datos || !isset($datos->valor)) {
        throw new Exception('No se recibieron datos válidos');
    }

    $info = $datos->valor;

    // Contadores y variables de control
    $contadorResultadoFinal = 1;
    $contadorC13 = 1;
    $contadorPorcentaje = 1;
    $contadorSt = 1;
    $contadorSf = 1;
    $contadorHmf = 1;
    $contadorFloracion = 1;
    $contadorLocalidad = 1;
    $longitud = 0;
    $longitudListaC13 = 0;
    $longitudListaPorcentaje = 0;
    $longitudListaSt = 0;
    $longitudListaSf = 0;
    $longitudListaHmf = 0;
    $longitudFloracion = 0;
    $longitudLocalidad = 0;
    $informacionArrglos = false;

    // Construir subconsultas para cada filtro
    if (isset($info->listaResultadosFinales)) {
        $longitud = count($info->listaResultadosFinales);
        if ($longitud > 0) {
            $placeholders = implode(',', array_fill(0, $longitud, '?'));
            $sqlResulFinal = "SELECT idresultadoFinal FROM resultadofinal WHERE idresultadoFinal IN ($placeholders)";
            $informacionArrglos = true;
        }
    }

    if (isset($info->listaC13)) {
        $longitudListaC13 = count($info->listaC13);
        if ($longitudListaC13 > 0) {
            $placeholders = implode(',', array_fill(0, $longitudListaC13, '?'));
            // NOTA: Usa laboratorio_organico para miel orgánica
            $sqlC13 = "SELECT adulteracionDescripcion FROM laboratorio_organico WHERE adulteracionDescripcion IN ($placeholders)";
            $informacionArrglos = true;
        }
    }

    if (isset($info->listaPorcentaje)) {
        $longitudListaPorcentaje = count($info->listaPorcentaje);
        if ($longitudListaPorcentaje > 0) {
            $conditions = [];
            foreach ($info->listaPorcentaje as $porcentaje) {
                if ($porcentaje == "Por rangos" && isset($info->rangosPorcentaje)) {
                    $rango = $info->rangosPorcentaje;
                    $conditions[] = "porcentaje BETWEEN " . $con->quote($rango->rango1) . " AND " . $con->quote($rango->rango2);
                } else {
                    $conditions[] = "porcentaje = " . $con->quote($porcentaje);
                }
            }
            // NOTA: Usa laboratorio_organico para miel orgánica
            $sqlPorcentaje = "SELECT porcentaje FROM laboratorio_organico WHERE " . implode(' OR ', $conditions);
            $informacionArrglos = true;
        }
    }

    if (isset($info->listaSt)) {
        $longitudListaSt = count($info->listaSt);
        if ($longitudListaSt > 0) {
            $placeholders = implode(',', array_fill(0, $longitudListaSt, '?'));
            $sqlSt = "SELECT st FROM laboratorio_organico WHERE stDescripcion IN ($placeholders)";
            $informacionArrglos = true;
        }
    }

    if (isset($info->listaSf)) {
        $longitudListaSf = count($info->listaSf);
        if ($longitudListaSf > 0) {
            $placeholders = implode(',', array_fill(0, $longitudListaSf, '?'));
            $sqlSf = "SELECT sf FROM laboratorio_organico WHERE sfDescripcion IN ($placeholders)";
            $informacionArrglos = true;
        }
    }

    if (isset($info->listaHmf)) {
        $longitudListaHmf = count($info->listaHmf);
        if ($longitudListaHmf > 0) {
            $conditions = [];
            foreach ($info->listaHmf as $hmf) {
                if ($hmf == "Por rangos" && isset($info->rangosHmf)) {
                    $rango = $info->rangosHmf;
                    $conditions[] = "hmf BETWEEN " . $con->quote($rango->rango1) . " AND " . $con->quote($rango->rango2);
                } else {
                    $conditions[] = "hmf = " . $con->quote($hmf);
                }
            }
            $sqlHmf = "SELECT hmf FROM laboratorio_organico WHERE " . implode(' OR ', $conditions);
            $informacionArrglos = true;
        }
    }

    if (isset($info->listaFloracion)) {
        $longitudFloracion = count($info->listaFloracion);
        if ($longitudFloracion > 0) {
            $placeholders = implode(',', array_fill(0, $longitudFloracion, '?'));
            $sqlFloracion = "SELECT fl.idFloracion FROM floraciones WHERE fl.idFloracion IN ($placeholders)";
            $informacionArrglos = true;
        }
    }

    if (isset($info->listaLocalidad)) {
        $longitudLocalidad = count($info->listaLocalidad);
        if ($longitudLocalidad > 0) {
            $placeholders = implode(',', array_fill(0, $longitudLocalidad, '?'));
            $sqlLocalidad = "SELECT l.idlocalidad FROM localidades WHERE l.idlocalidad IN ($placeholders)";
            $informacionArrglos = true;
        }
    }

    // Construir consulta principal - USAR TABLAS ORGÁNICAS
    $sql = "SELECT ale.fecha, al.idAlmacen, al.estado, pr.nombre, pr.idSagarpa, l.localidad, al.bruto, al.tara,
                    al.neto, lab.porcentaje, lab.st, lab.porcentajeDescripcion, lab.sfDescripcion,
                    lab.stDescripcion, lab.adulteracionDescripcion, lab.hmf, lab.procesoDescripcion, lab.resultadoFinal,
                    rs.resultado, rs.idResultadoFinal, fl.idFloracion, fl.floracion
        FROM        almacen_organico al
        INNER JOIN  laboratorio_organico lab ON lab.idAlmacen = al.idAlmacen
        LEFT JOIN   almacenencabezado_organico ale ON  ale.idAlmacen = al.idalmacenEncabezado
        LEFT JOIN   resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal
        LEFT JOIN   proveedor pr ON pr.idProveedor = ale.idProveedor
        LEFT JOIN   direccion dir ON dir.idDireccion = pr.idDireccion
        LEFT JOIN   localidades l ON l.idlocalidad = dir.idlocalidad
        LEFT JOIN   floraciones fl ON fl.idFloracion = lab.idFloracion
        WHERE ";

    $conditions = [];

    if ($longitud > 0 && isset($sqlResulFinal)) {
        $conditions[] = "rs.idresultadoFinal IN ($sqlResulFinal)";
    }
    if ($longitudListaPorcentaje > 0 && isset($sqlPorcentaje)) {
        $conditions[] = "lab.porcentaje IN ($sqlPorcentaje)";
    }
    if ($longitudListaSt > 0 && isset($sqlSt)) {
        $conditions[] = "lab.st IN ($sqlSt)";
    }
    if ($longitudListaSf > 0 && isset($sqlSf)) {
        $conditions[] = "lab.sf IN ($sqlSf)";
    }
    if ($longitudListaC13 > 0 && isset($sqlC13)) {
        $conditions[] = "lab.adulteracionDescripcion IN ($sqlC13)";
    }
    if ($longitudListaHmf > 0 && isset($sqlHmf)) {
        $conditions[] = "lab.hmf IN ($sqlHmf)";
    }
    if ($longitudFloracion > 0 && isset($sqlFloracion)) {
        $conditions[] = "fl.idFloracion IN ($sqlFloracion)";
    }
    if ($longitudLocalidad > 0 && isset($sqlLocalidad)) {
        $conditions[] = "l.idlocalidad IN ($sqlLocalidad)";
    }

    if (count($conditions) > 0) {
        $sql .= implode(' AND ', $conditions) . " AND al.estado = 0";
    } else {
        $sql .= "al.estado = 0";
    }

    // Ejecutar consulta con PDO
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($resultados) == 0) {
        echo "0";
    } else {
        $array = [];
        $neto = 0;

        foreach ($resultados as $rs) {
            // if ($neto / 1000 > 22.5) {
            //     break;
            // }
            $neto += $rs["neto"];
            $calidad = new stdClass();
            $calidad->fecha = $rs["fecha"];
            $calidad->idAlmacen = $rs["idAlmacen"];
            $calidad->proveedor = $rs["nombre"]; // Ya no necesita utf8_encode con PDO charset=utf8mb4
            $calidad->idSagarpa = $rs["idSagarpa"];
            $calidad->localidad = $rs["localidad"];
            $calidad->bruto = $rs["bruto"];
            $calidad->tara = $rs["tara"];
            $calidad->neto = $rs["neto"];
            $calidad->porcentaje = $rs["porcentaje"];
            $calidad->sf = $rs["sfDescripcion"];
            $calidad->st = $rs["stDescripcion"];
            $calidad->adulteracionDescripcion = $rs["adulteracionDescripcion"];
            $calidad->hmf = $rs["hmf"];
            $calidad->resultado = $rs["resultado"];
            $calidad->idResultadoFinal = $rs["idResultadoFinal"];
            $calidad->idFloracion = $rs["idFloracion"];
            $calidad->floracion = $rs["floracion"];
            $array[] = $calidad;
        }

        echo json_encode($array);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
