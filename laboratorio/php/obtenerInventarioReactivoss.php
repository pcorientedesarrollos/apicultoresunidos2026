<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
function getInventarioReactivos($acumulado = FALSE, $idMes = FALSE, $fechaFinal = FALSE, $soloEncabezado = FALSE)
{
    global $con;

    $datos = $con->prepare("SELECT idReactivo, reactivo FROM catalogoreactivos");
    $datos->execute();
    $resultado = array();

    if ($acumulado || $fechaFinal) {
        if ($datos->rowCount() >= 1) {
            foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $reac) {
                $idReactivo = $reac['idReactivo'];
                $reactivo = $reac['reactivo'];
                $seleccionarPasado = $con->prepare("SELECT COALESCE (existenciaPasada,0) AS existenciaPasada FROM saldoinicialinventario WHERE nombre = '$reactivo'");
                // $seleccionarPasado->bindParam = (':reactivo', $reactivo);
                $seleccionarPasado->execute();
                if ($seleccionarPasado == false) {
                    throw new Exception($con->errorInfo());
                } else {
                    $dato = $seleccionarPasado->fetch(PDO::FETCH_ASSOC);
                }
                $sqlInventarioReactivos = "SELECT rd.idDetalle, rd.cantidad, rd.costoUnitario, rd.importe, rd.idReactivo, rd.reactivo, re.fecha, re.total, 'E' AS tipo
                FROM reactivosentradadetalle rd
                LEFT JOIN reactivosentradaencabezado re ON re.idEntrada = rd.idEntrada
                WHERE rd.idReactivo = $idReactivo";
                if ($fechaFinal) {
                    $sqlInventarioReactivos .= " AND re.fecha <= '" . $fechaFinal . "'";
                }
                $sqlInventarioReactivos .= " UNION
                SELECT idDetalle, cantidad, 0 AS costoUnitario, 0 AS importe, idReactivo, reactivo, fecha, 0 AS total, 'S' AS tipo
                FROM(
                SELECT cr.idEncabezado AS idDetalle, cr.idReactivo, catr.reactivo, cr.cantidad, hce.fecha
                FROM conformacionhomogeneo_reactivos cr
                LEFT JOIN catalogoreactivos catr ON catr.idReactivo = cr.reactivo
                LEFT JOIN conformacionhomogeneo_encabezado hce ON hce.idEncabezado = cr.idEncabezado
                WHERE cr.reactivo = $idReactivo
                UNION
                SELECT cr.idEncabezado AS idDetalle, cr.idReactivo, catr.reactivo, cr.cantidad, hce.fecha
                FROM conformacionhomogeneo_reactivos_organico cr
                LEFT JOIN catalogoreactivos catr ON catr.idReactivo = cr.reactivo
                LEFT JOIN conformacionhomogeneo_encabezado_organico hce ON hce.idEncabezado = cr.idEncabezado
                WHERE cr.reactivo = $idReactivo) AS salidas";
                if ($fechaFinal) {
                    $sqlInventarioReactivos .= " WHERE fecha <= '" . $fechaFinal . "'";
                }
                $sqlInventarioReactivos .= " ORDER BY fecha ASC";
                $datos = $con->prepare($sqlInventarioReactivos);
                $datos->execute();
                $reac['registros'] = [];
                $entradas = 0;
                $salidas = 0;
                // $entradasImporte = 0;
                // $salidasImporte = 0;
                $existenciaPasada = $dato['existenciaPasada'];
                // $importePasado = $dato['importeAcumuladoPasado'];
                if ($datos->rowCount() >= 1) {
                    foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $registros) {
                        if ($registros['tipo'] == 'E') {
                            $registros['entrada'] = $registros['cantidad'];
                            // $registros['importeEntrada'] = $registros['importe'];
                            $entradas += $registros['cantidad'];
                            // $entradasImporte += $registros['importe'];
                        } else if ($registros['tipo'] == 'S') {
                            $registros['salida'] = $registros['cantidad'];
                            // $registros['importeSalida'] = $registros['importe'];
                            $salidas += $registros['cantidad'];
                            // $salidasImporte += $registros['importe'];
                        }
                        $registros['existencia'] = $existenciaPasada + $entradas - $salidas;
                        // $registros['importeAcumulado'] = $importePasado + $entradasImporte - $salidasImporte;
                        array_push($reac['registros'], $registros);
                    }
                    $reac['existenciaAcumuladaPasada'] = $existenciaPasada;
                    // $reac['importeAcumuladoPasado'] = $importePasado;
                    $reac['totalEntradas'] = $entradas;
                    $reac['totalSalidas'] = $salidas;
                    $reac['totalExistencia'] = $registros['existencia'];
                    // $reac['totalImporteEntradas'] = $entradasImporte;
                    // $reac['totalImporteSalidas'] = $salidasImporte;
                    // $reac['totalImporteAcumulado'] = $registros['importeAcumulado'];
                }
                array_push($resultado, $reac);
            }
        }
        return $resultado;
    } else if ($idMes) {
        if ($idMes == 1) {
            if ($datos->rowCount() >= 1) {

                foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $reac) {
                    $idReactivo = $reac['idReactivo'];
                    $reactivo = $reac['reactivo'];

                    $seleccionarPasado = $con->prepare("SELECT COALESCE (existenciaPasada,0) AS existenciaPasada FROM saldoinicialinventario WHERE nombre = '$reactivo'");
                    $seleccionarPasado->execute();
                    if ($seleccionarPasado == false) {
                        throw new Exception($con->errorInfo());
                    } else {
                        $dato = $seleccionarPasado->fetch(PDO::FETCH_ASSOC);
                    }

                    $sqlInventarioReactivos = "SELECT rd.idDetalle, rd.cantidad, rd.costoUnitario, rd.importe, rd.idReactivo, rd.reactivo, re.fecha, re.total, 'E' AS tipo
                    FROM reactivosentradadetalle rd
                    LEFT JOIN reactivosentradaencabezado re ON re.idEntrada = rd.idEntrada
                    WHERE rd.idReactivo = $idReactivo AND SUBSTR(re.fecha FROM 6 FOR 2) = $idMes
                    UNION
                    SELECT idDetalle, cantidad, 0 AS costoUnitario, 0 AS importe, idReactivo, reactivo, fecha, 0 AS total, 'S' AS tipo
                    FROM(
                    SELECT cr.idEncabezado AS idDetalle, cr.idReactivo, catr.reactivo, cr.cantidad, hce.fecha
                    FROM conformacionhomogeneo_reactivos cr
                    LEFT JOIN catalogoreactivos catr ON catr.idReactivo = cr.reactivo
                    LEFT JOIN conformacionhomogeneo_encabezado hce ON hce.idEncabezado = cr.idEncabezado
                    WHERE cr.reactivo = $idReactivo AND SUBSTR(hce.fecha FROM 6 FOR 2) = $idMes
                    UNION
                    SELECT cr.idEncabezado AS idDetalle, cr.idReactivo, catr.reactivo, cr.cantidad, hce.fecha
                    FROM conformacionhomogeneo_reactivos_organico cr
                    LEFT JOIN catalogoreactivos catr ON catr.idReactivo = cr.reactivo
                    LEFT JOIN conformacionhomogeneo_encabezado_organico hce ON hce.idEncabezado = cr.idEncabezado
                    WHERE cr.reactivo = $idReactivo AND SUBSTR(hce.fecha FROM 6 FOR 2) = $idMes) AS salidas
                    ORDER BY fecha ASC";
                    $datos = $con->prepare($sqlInventarioReactivos);
                    $datos->execute();
                    $reac['registros'] = [];
                    $entradas = 0;
                    $salidas = 0;
                    // $entradasImporte = 0;
                    // $salidasImporte = 0;
                    $existenciaPasada = $dato['existenciaPasada'];
                    // $importePasado = $dato['importeAcumuladoPasado'];
                    if ($datos->rowCount() >= 1) {
                        foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $registros) {
                            if ($registros['tipo'] == 'E') {
                                $registros['entrada'] = $registros['cantidad'];
                                // $registros['importeEntrada'] = $registros['importe'];
                                $entradas += $registros['cantidad'];
                                // $entradasImporte += $registros['importe'];
                            } else if ($registros['tipo'] == 'S') {
                                $registros['salida'] = $registros['cantidad'];
                                // $registros['importeSalida'] = $registros['importe'];
                                $salidas += $registros['cantidad'];
                                // $salidasImporte += $registros['importe'];
                            }
                            $registros['existencia'] = $existenciaPasada + $entradas - $salidas;
                            // $registros['importeAcumulado'] = $importePasado + $entradasImporte - $salidasImporte;
                            array_push($reac['registros'], $registros);
                        }
                        $reac['existenciaAcumuladaPasada'] = $existenciaPasada;
                        // $reac['importeAcumuladoPasado'] = $importePasado;
                        $reac['totalEntradas'] = $entradas;
                        $reac['totalSalidas'] = $salidas;
                        $reac['totalExistencia'] = $registros['existencia'];
                        // $reac['totalImporteEntradas'] = $entradasImporte;
                        // $reac['totalImporteSalidas'] = $salidasImporte;
                        // $reac['totalImporteAcumulado'] = $registros['importeAcumulado'];
                    }
                    array_push($resultado, $reac);
                }
            }
            return $resultado;
        } else {
            if ($datos->rowCount() >= 1) {
                foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $reac) {
                    $idReactivo = $reac['idReactivo'];
                    $reactivo = $reac['reactivo'];
                    $seleccionarInicial = $con->prepare("SELECT  COALESCE (existenciaPasada,0) AS existenciaInicial FROM saldoinicialinventario WHERE nombre = '$reactivo'");
                    $seleccionarInicial->execute();
                    if ($seleccionarInicial == false) {
                        throw new Exception($con->errorInfo());
                    } else {
                        $info = $seleccionarInicial->fetch(PDO::FETCH_ASSOC);
                    }
                    $seleccionarPasado = $con->prepare("SELECT COALESCE (SUM(rco.cantidad),0) AS existenciaPasada, 'E' AS tipo
                    FROM reactivosentradadetalle rco
                    LEFT JOIN reactivosentradaencabezado rce ON rce.idEntrada = rco.idEntrada
                    WHERE SUBSTR(rce.fecha FROM 6 FOR 2) < $idMes
                    UNION
                    SELECT SUM(existenciaPasada) AS existenciaPasada, tipo
                FROM (
                SELECT
                        COALESCE(SUM(rco.cantidad),0) AS existenciaPasada, 
                        'S' AS tipo
                    FROM conformacionhomogeneo_reactivos rco
                    LEFT JOIN conformacionhomogeneo_encabezado rce ON rce.idEncabezado = rco.idEncabezado
                    WHERE
                        SUBSTR(rce.fecha FROM 6 FOR 2) < $idMes
                UNION
                SELECT
                        COALESCE(SUM(rco.cantidad),0) AS existenciaPasada,
                        'S' AS tipo
                    FROM conformacionhomogeneo_reactivos_organico rco
                    LEFT JOIN conformacionhomogeneo_encabezado_organico rce ON rce.idEncabezado = rco.idEncabezado
                    WHERE
                        SUBSTR(rce.fecha FROM 6 FOR 2) < $idMes
                ) AS tabla");
                    $seleccionarPasado->execute();
                    $entradasExistencia = 0;
                    // $entradasImporte = 0;
                    $salidasExistencia = 0;
                    // $salidasImporte = 0;
                    $datoExistenciaPasado = 0;
                    // $datoImportePasado = 0;
                    if ($seleccionarPasado->rowCount() >= 1) {
                        foreach ($seleccionarPasado->fetchAll(PDO::FETCH_ASSOC) as $registro) {
                            if ($registro['tipo'] == 'E') {
                                $entradasExistencia = $registro['existenciaPasada'];
                                // $entradasImporte = $registro['importeAcumuladoPasado'];
                            } else if ($registro['tipo'] == 'S') {
                                $salidasExistencia = $registro['existenciaPasada'];
                                // $salidasImporte += $registro['importeAcumuladoPasado'];
                            }
                            $datoExistenciaPasado = $entradasExistencia - $salidasExistencia;
                            // $datoImportePasado = $entradasImporte - $salidasImporte;
                        }
                    }

                    $datosRegistros = $con->prepare("SELECT rd.idDetalle, rd.cantidad, rd.costoUnitario, rd.importe, rd.idReactivo, rd.reactivo, re.fecha, re.total, 'E' AS tipo
                    FROM reactivosentradadetalle rd
                    LEFT JOIN reactivosentradaencabezado re ON re.idEntrada = rd.idEntrada
                    WHERE rd.idReactivo = $idReactivo AND SUBSTR(re.fecha FROM 6 FOR 2) = $idMes
                    UNION
                    SELECT idDetalle, cantidad, 0 AS costoUnitario, 0 AS importe, idReactivo, reactivo, fecha, 0 AS total, 'S' AS tipo
                    FROM(
                    SELECT cr.idEncabezado AS idDetalle, cr.idReactivo, catr.reactivo, cr.cantidad, hce.fecha
                    FROM conformacionhomogeneo_reactivos cr
                    LEFT JOIN catalogoreactivos catr ON catr.idReactivo = cr.reactivo
                    LEFT JOIN conformacionhomogeneo_encabezado hce ON hce.idEncabezado = cr.idEncabezado
                    WHERE cr.idReactivo = $idReactivo AND SUBSTR(hce.fecha FROM 6 FOR 2) = $idMes
                    UNION
                    SELECT cr.idEncabezado AS idDetalle, cr.idReactivo, catr.reactivo, cr.cantidad, hce.fecha
                    FROM conformacionhomogeneo_reactivos_organico cr
                    LEFT JOIN catalogoreactivos catr ON catr.idReactivo = cr.reactivo
                    LEFT JOIN conformacionhomogeneo_encabezado_organico hce ON hce.idEncabezado = cr.idEncabezado 
                    WHERE cr.idReactivo = $idReactivo AND SUBSTR(hce.fecha FROM 6 FOR 2) = $idMes) AS salidas
                    ORDER BY fecha ASC");
                    $datosRegistros->execute();
                    $reac['registros'] = [];
                    $entradas = 0;
                    $salidas = 0;
                    // $entradasImporte = 0;
                    // $salidasImporte = 0;
                    $existenciaPasada = $datoExistenciaPasado + $info['existenciaInicial'];
                    // $importePasado = $datoImportePasado + $info['importeInicial'];
                    if ($datosRegistros->rowCount() >= 1) {
                        foreach ($datosRegistros->fetchAll(PDO::FETCH_ASSOC) as $registros) {
                            if ($registros['tipo'] == 'E') {
                                $registros['entrada'] = $registros['cantidad'];
                                // $registros['importeEntrada'] = $registros['importe'];
                                $entradas += $registros['cantidad'];
                                // $entradasImporte += $registros['importe'];
                            } else if ($registros['tipo'] == 'S') {
                                $registros['salida'] = $registros['cantidad'];
                                // $registros['importeSalida'] = $registros['importe'];
                                $salidas += $registros['cantidad'];
                                // $salidasImporte += $registros['importe'];
                            }
                            $registros['existencia'] = $existenciaPasada + $entradas - $salidas;
                            // $registros['importeAcumulado'] = $importePasado + $entradasImporte - $salidasImporte;
                            array_push($reac['registros'], $registros);
                        }
                        $reac['existenciaAcumuladaPasada'] = $existenciaPasada;
                        // $reac['importeAcumuladoPasado'] = $importePasado;
                        $reac['totalEntradas'] = $entradas;
                        $reac['totalSalidas'] = $salidas;
                        $reac['totalExistencia'] = $registros['existencia'];
                        // $reac['totalImporteEntradas'] = $entradasImporte;
                        // $reac['totalImporteSalidas'] = $salidasImporte;
                        $reac['totalImporteAcumulado'] = $registros['importeAcumulado'];
                    }
                    array_push($resultado, $reac);
                }
            }
            return $resultado;
        }
    }

    return $soloEncabezado ? $resultado['encabezado'] : $resultado;
}
