<?php

include_once '../../DAOConeccion/conePDO.php';
// include_once __DIR__ . '/dameInformacionAuditoriaPZona.min.php';
$pdo = new conePDO();
$con = $pdo->conectar();

function obtenerInformacionAuditoria($idAuditoria, $tipoReporte = false)
{
    global $con;
    $resultadoDatos = array();

    $sql = $con->prepare("SELECT aud.idAuditoria, aud.fecha, aud.finalizado, aud.usuario, 
    CASE aud.estado WHEN 1 THEN 'Activo' WHEN 2 THEN 'Finalizado' WHEN -1 THEN 'Cancelado' 
    WHEN 0 THEN 'Pendiente' END AS estado 
    FROM auditorias aud WHERE aud.idAuditoria = :idAuditoria");
    $sql->bindParam(':idAuditoria', $idAuditoria);
    $sql->execute();
    if ($sql == false) {
        throw new Exception($con->errorInfo());
    }

    $resultado = $sql->fetch(PDO::FETCH_ASSOC);
    $resultado['zonas'] = array();
    $resultado['tambores_no_escaneados'] = array();
    $resultado['tambos_no_disponibles'] = array(
        'lotes_internos' => array(
            'bruto' => 0,
            'tara' => 0,
            'neto' => 0,
            'tambores' => 0
        ),
        'envasados' => array(
            'bruto' => 0,
            'tara' => 0,
            'neto' => 0,
            'tambores' => 0
        ),
        'exportados' => array(
            'bruto' => 0,
            'tara' => 0,
            'neto' => 0,
            'tambores' => 0
        ),
        'netoTotal' => 0,
        'tamboresTotal' => 0
    );
    $sqlDetalle = "SELECT zt.nombre AS zona, tdm.tipoDeMiel AS tipoDeMiel, ra.idTipoDeMiel, ra.idZonaAuditoria
    FROM respaldoauditoria ra
    LEFT JOIN zonastambores zt ON zt.idZonaTambor = ra.idZonaAuditoria
    LEFT JOIN tiposdemiel tdm ON tdm.idTipoDeMiel = ra.idTipoDeMiel
    WHERE ra.idAuditoria = :idAuditoria
    GROUP BY idZonaAuditoria";
    $datosDetalle = $con->prepare($sqlDetalle);
    $datosDetalle->bindParam(':idAuditoria', $idAuditoria);
    $datosDetalle->execute();
    if ($datosDetalle == false) {
        throw new Exception($con->errorInfo());
    }
    foreach ($datosDetalle->fetchAll(PDO::FETCH_ASSOC) as $zona) {
        $zona['netoEscaneado'] = 0;
        $zona['netoNoEscaneado'] = 0;
        $zona['tambEscaneados'] = 0;
        $zona['tambores_faltantes'] = 0;

        switch ($zona['idTipoDeMiel']) {
            case '1':
                $almacen_tabla = 'almacen';
                $tipoDeMiel = 'Miel 100% pura de abeja';
                $almacenencabezado_tabla = 'almacenencabezado';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                $tipoDeMiel = 'Miel 100% orgánica';
                $almacenencabezado_tabla = 'almacenencabezado_organico';
                break;
            default:
                throw new Exception('El tipo de miel no es válido');
                break;
        }

        $respaldoAuditoria = "SELECT T1.hora, T1.idAlmacen, T1.idRespaldoAuditoria, T1.usuario, T1.sobrante 
        FROM respaldoauditoria T1 
        INNER JOIN (
        SELECT DISTINCT(idAlmacen) AS idAlmacen, MAX(hora) AS max_hora FROM respaldoauditoria WHERE idAuditoria = :idAuditoria AND idTipoDeMiel = :idTipoDeMiel GROUP BY idAlmacen,sobrante
        ) AS T2
        ON T1.hora = T2.max_hora
        WHERE T1.idAuditoria = :idAuditoria AND T1.idZonaAuditoria = :idZonaAuditoria AND T1.idTipoDeMiel = :idTipoDeMiel
        GROUP BY T1.idAlmacen,T1.sobrante
        ORDER BY T1.idAlmacen";
        $datoRespaldo = $con->prepare($respaldoAuditoria);
        $datoRespaldo->bindParam(':idZonaAuditoria', $zona['idZonaAuditoria']);
        $datoRespaldo->bindParam(':idTipoDeMiel', $zona['idTipoDeMiel']);
        $datoRespaldo->bindParam(':idAuditoria', $idAuditoria);
        $datoRespaldo->execute();
        if ($datoRespaldo->rowCount() >= 1) {
            $arreglo = array();
            foreach ($datoRespaldo->fetchAll(PDO::FETCH_ASSOC) as $respaldo) {
                if ($respaldo['sobrante'] == null || $respaldo['sobrante'] == '') {
                    //ALMACÉN ORGÁNICO Y ALMACÉN CONVENCIONAL
                    $entradaAlmacen = "SELECT ra.hora, ra.idAlmacen, ra.idRespaldoAuditoria, ra.sobrante, tdm.tipoDeMiel AS tipoDeMiel, zta.nombre AS zonaAntes, zt.nombre AS zona, alm.bruto, alm.tara, alm.neto, ae.fecha,
                CASE alm.estado WHEN 0 THEN 'Disponible' WHEN 1 THEN 'Experimental' WHEN 2 THEN 'Lote interno'
                WHEN 3 THEN 'Exportación' WHEN 4 THEN 'Envasado' END AS estado_tambor, alm.estado
            FROM respaldoauditoria ra
            LEFT JOIN auditorias aud ON ra.idAuditoria = aud.idAuditoria
            LEFT JOIN zonastambores zt ON zt.idZonaTambor = ra.idZonaAuditoria
            LEFT JOIN zonastambores zta ON zta.idZonaTambor = ra.idZonaAntes
            LEFT JOIN tiposdemiel tdm ON tdm.idTipoDeMiel = ra.idTipoDeMiel
            LEFT JOIN $almacen_tabla alm ON ra.idAlmacen = alm.idAlmacen
            LEFT JOIN $almacenencabezado_tabla ae ON ae.idAlmacen = alm.idAlmacenEncabezado
            WHERE ra.idZonaAuditoria = :idZonaAuditoria AND ra.idTipoDeMiel = :idTipoDeMiel AND alm.idAlmacen = :idAlmacen AND (
                ae.fecha <= aud.finalizado OR aud.finalizado IS NULL) ORDER BY ra.idRespaldoAuditoria DESC LIMIT 1";
                    $datosRespaldo = $con->prepare($entradaAlmacen);
                    $datosRespaldo->bindParam(':idZonaAuditoria', $zona['idZonaAuditoria']);
                    $datosRespaldo->bindParam(':idTipoDeMiel', $zona['idTipoDeMiel']);
                    $datosRespaldo->bindParam(':idAlmacen', $respaldo['idAlmacen']);
                    $datosRespaldo->execute();
                    $datoResp = $datosRespaldo->fetch(PDO::FETCH_ASSOC);
                    array_push($arreglo, $datoResp);
                } else {
                    //ALMACÉN SOBRANTE
                    $entradaSobrante = "SELECT ra.hora, CONCAT(s.codigo, '-', alm.consecutivo) AS idAlmacen, ra.idRespaldoAuditoria, tdm.tipoDeMiel AS tipoDeMiel, zta.nombre AS zonaAntes, zt.nombre AS zona, alm.bruto, alm.tara, alm.neto, alm.fecha,
                CASE alm.estado WHEN 0 THEN 'Disponible' WHEN 1 THEN 'Experimental' WHEN 2 THEN 'Lote interno'
                WHEN 3 THEN 'Exportación' WHEN alm.estado = 4 THEN 'Envasado' END AS estado_tambor, alm.estado
            FROM respaldoauditoria ra
            LEFT JOIN auditorias aud ON ra.idAuditoria = aud.idAuditoria
            LEFT JOIN zonastambores zt ON zt.idZonaTambor = ra.idZonaAuditoria
            LEFT JOIN zonastambores zta ON zta.idZonaTambor = ra.idZonaAntes
            LEFT JOIN tiposdemiel tdm ON tdm.idTipoDeMiel = ra.idTipoDeMiel
            LEFT JOIN almacensobrantes alm ON ra.idAlmacen = alm.consecutivo
            LEFT JOIN sobrantes s ON s.idSobrante = ra.sobrante
            WHERE ra.idZonaAuditoria = :idZonaAuditoria AND ra.idTipoDeMiel = :idTipoDeMiel AND alm.consecutivo = :consecutivo AND alm.sobrante = :sobrante AND (
                alm.fecha <= aud.finalizado OR aud.finalizado IS NULL) ORDER BY ra.idRespaldoAuditoria DESC LIMIT 1";
                    $datosRespaldo = $con->prepare($entradaSobrante);
                    $datosRespaldo->bindParam(':idZonaAuditoria', $zona['idZonaAuditoria']);
                    $datosRespaldo->bindParam(':idTipoDeMiel', $zona['idTipoDeMiel']);
                    $datosRespaldo->bindParam(':consecutivo', $respaldo['idAlmacen']);
                    $datosRespaldo->bindParam(':sobrante', $respaldo['sobrante']);
                    $datosRespaldo->execute();
                    $datoResp = $datosRespaldo->fetch(PDO::FETCH_ASSOC);
                    array_push($arreglo, $datoResp);
                }
                $resultadoDatos = $arreglo;
            }
        }
        //ALMACÉN CONVENCIONAL, ALMACÉN ORGÁNICO Y ALMACÉN SOBRANTE
        $sqlAlmacen = "SELECT alm.idAlmacen, '$tipoDeMiel' AS tipoDeMiel, zt.nombre as zonaAntes, '' as zona, alm.bruto,
         alm.tara, alm.neto, '' as hora, '' as usuario, 0 as idRespaldoAuditoria, ae.fecha, 
         CASE WHEN alm.estado = 0 THEN 'Disponible' WHEN alm.estado = 1 THEN 'Experimental' WHEN alm.estado = 2 THEN 'Lote interno' 
         WHEN alm.estado = 3 THEN 'Exportación' WHEN alm.estado = 4 THEN 'Envasado' END as estado_tambor, alm.estado
         FROM $almacen_tabla alm LEFT JOIN auditorias aud ON aud.idAuditoria = :idAuditoria LEFT JOIN zonastambores zt ON zt.idZonaTambor = alm.zona 
         LEFT JOIN $almacenencabezado_tabla ae ON ae.idAlmacen = alm.idAlmacenEncabezado WHERE alm.zona = :idZonaAuditoria 
         AND alm.idAlmacen NOT IN (SELECT idAlmacen FROM respaldoauditoria WHERE idAuditoria = :idAuditoria AND idTipoDeMiel = :idTipoDeMiel AND sobrante IS NULL) AND (ae.fecha <= aud.finalizado OR aud.finalizado IS NULL)
         UNION 
         SELECT CONCAT(s.codigo, '-', alm.consecutivo) AS idAlmacen, '$tipoDeMiel' AS tipoDeMiel, zt.nombre as zonaAntes,
         '' as zona, alm.bruto, alm.tara, alm.neto, '' as hora, '' as usuario, 0 as idRespaldoAuditoria, alm.fecha, 
         CASE WHEN alm.estado = 0 THEN 'Disponible' WHEN alm.estado = 1 THEN 'Experimental' WHEN alm.estado = 2 THEN 'Lote interno'
         WHEN alm.estado = 3 THEN 'Exportación' WHEN alm.estado = 4 THEN 'Envasado' END as estado_tambor, alm.estado 
         FROM almacensobrantes alm LEFT JOIN auditorias aud ON aud.idAuditoria = :idAuditoria 
         LEFT JOIN zonastambores zt ON zt.idZonaTambor = alm.zona LEFT JOIN sobrantes s ON s.idSobrante = alm.sobrante 
         WHERE alm.zona = :idZonaAuditoria AND alm.consecutivo NOT IN (SELECT idAlmacen FROM respaldoauditoria 
         WHERE idAuditoria = :idAuditoria AND idTipoDeMiel = :idTipoDeMiel AND sobrante > 0) AND (alm.fecha <= aud.finalizado OR aud.finalizado IS NULL)
         ";
        $datosAlmacen = $con->prepare($sqlAlmacen);
        $datosAlmacen->bindParam(':idZonaAuditoria', $zona['idZonaAuditoria']);
        $datosAlmacen->bindParam(':idTipoDeMiel', $zona['idTipoDeMiel']);
        $datosAlmacen->bindParam(':idAuditoria', $idAuditoria);
        $datosAlmacen->execute();

        if ($datosAlmacen == false) {
            throw new Exception($con->errorInfo());
        }
        $dato2 = $datosAlmacen->fetchAll(PDO::FETCH_ASSOC);

        $datos = array_merge($dato2, $resultadoDatos);

        $resultado1 = array(
            // Materia Prima escaneado y no escaneado
            'escaneados' => array(
                'neto' => 0,
                'tambores' => 0
            ),
            'no_escaneados' => array(
                'neto' => 0,
                'tambores' => 0
            ),
            'mp_noDisponible' => array(
                'neto' => 0,
                'tambores' => 0
            ),
            'tambores_escaneados' => array(),
            'tambores_no_escaneados' => array(),
            'tambores_noDisponibles' => array()
        );

        foreach ($datos as $tambor) {
            if ($tambor['idRespaldoAuditoria'] != 0) {
                // Estos tambores si se escanearon
                if (
                    $tambor['estado'] == '0'
                    || $tambor['estado'] == '1'
                    || $tambor['estado'] == '2'
                    || $tambor['estado'] == '3'
                    || $tambor['estado'] == '4'
                ) {
                    // Disponibles, Experimental, Lote interno, Exportado o Envasado
                    $resultado1['escaneados']['neto'] += $tambor['neto'];
                    $resultado1['escaneados']['tambores']++;
                }
                array_push($resultado1['tambores_escaneados'], $tambor);
            } else {
                // Estos tambores no se escanearon
                if (
                    $tambor['estado'] == '0'
                    || $tambor['estado'] == '1'
                ) {
                    // Disponibles, Experimental, Lote interno, Exportado o Envasado
                    $resultado1['no_escaneados']['neto'] += $tambor['neto'];
                    $resultado1['no_escaneados']['tambores']++;
                }
                array_push($resultado1['tambores_no_escaneados'], $tambor);

                if (
                    $tambor['estado'] == '2'
                    || $tambor['estado'] == '3'
                    || $tambor['estado'] == '4'
                ) {
                    //Lote interno, Exportado o Envasado
                    $resultado1['mp_noDisponible']['neto'] += $tambor['neto'];
                    $resultado1['mp_noDisponible']['tambores']++;
                }
                array_push($resultado1['tambores_noDisponibles'], $tambor);
            }
        }

        $zona['tambEscaneados'] = $resultado1['escaneados']['tambores'];
        $zona['tambores_faltantes'] = $resultado1['no_escaneados']['tambores'];
        $zona['netoEscaneado'] = $resultado1['escaneados']['neto'];
        $zona['netoNoEscaneado'] = $resultado1['no_escaneados']['neto'];
        $zona['netoNoDisponible'] = $resultado1['mp_noDisponible']['neto'];
        if ($tipoReporte == '2') {
            $zona['desglose'] = $resultado1;
        }
        if ($tipoReporte == '3') {
            foreach ($resultado1['tambores_no_escaneados'] as $tambor) {
                if ($tambor['estado'] == '1' || $tambor['estado'] == '0') {
                    array_push($resultado['tambores_no_escaneados'], $tambor);
                }
            }
        }

        array_push($resultado['zonas'], $zona);
    }

    $resultado['totalEscaneado'] = 0;
    $resultado['totalNoEscaneado'] = 0;
    $resultado['netoTotal'] = 0;
    $resultado['ttEscaneados'] = 0;
    $resultado['ttNoEscaneados'] = 0;
    $resultado['tambosTotal'] = 0;
    $resultado['comprasMiel'] = 0;
    $resultado['noDisponible'] = 0;
    foreach ($resultado['zonas'] as $zona) {
        $resultado['totalEscaneado'] += $zona['netoEscaneado'];
        $resultado['totalNoEscaneado'] += $zona['netoNoEscaneado'];
        $resultado['ttEscaneados'] += $zona['tambEscaneados'];
        $resultado['ttNoEscaneados'] += $zona['tambores_faltantes'];
        $resultado['noDisponible'] += $zona['netoNoDisponible'];
    }
    $resultado['netoTotal'] = $resultado['totalEscaneado'] + $resultado['totalNoEscaneado'];
    $resultado['tambosTotal'] = $resultado['ttEscaneados'] + $resultado['ttNoEscaneados'];
    $comprasMiel = 0;
    $sqlNeto = $con->prepare("SELECT SUM(total) AS comprasMiel FROM (
        SELECT SUM(totalNeto) AS total FROM(
                SELECT SUM(ta.neto) AS totalNeto 
                FROM almacen ta
                UNION
                SELECT SUM(ca.neto) AS totalNeto
                FROM cubetasdetalle ca
        ) AS totales
        UNION
        SELECT SUM(totalNeto) AS total FROM(
          SELECT SUM(tao.neto) AS totalNeto 
                FROM almacen_organico tao
                UNION
                SELECT SUM(cao.neto) AS totalNeto
                FROM cubetasdetalle_organico cao
        ) AS totalOrg
        )AS tablasAlmacen");
    $sqlNeto->bindColumn('comprasMiel', $comprasMiel);
    $sqlNeto->execute();
    if ($sqlNeto == false) {
        throw new Exception($con->errorInfo());
    } else {
        $sqlNeto->fetch(PDO::FETCH_BOUND);
    }
    $resultado['comprasMiel'] = $comprasMiel;


    $sqlNoDisponible = $con->prepare("SELECT alm.idAlmacen, alm.neto, alm.estado 
    FROM almacen alm 
    WHERE alm.estado > 1
    UNION
    SELECT almo.idAlmacen, almo.neto, almo.estado
    FROM almacen_organico almo
    WHERE almo.estado > 1
    UNION
    SELECT alms.consecutivo, alms.neto, alms.estado
    FROM almacensobrantes alms
    WHERE alms.estado > 1");
    $sqlNoDisponible->execute();
    if ($sqlNoDisponible == false) {
        throw new Exception($con->errorInfo());
    } else {
        foreach ($sqlNoDisponible->fetchAll(PDO::FETCH_ASSOC) as $noDisponible) {
            if ($noDisponible['estado'] == '2') {
                // Lotes internos
                $resultado['tambos_no_disponibles']['lotes_internos']['neto'] += $noDisponible['neto'];
                $resultado['tambos_no_disponibles']['lotes_internos']['tambores']++;
            } elseif ($noDisponible['estado'] == '3') {
                // Exportacion
                $resultado['tambos_no_disponibles']['exportados']['neto'] += $noDisponible['neto'];
                $resultado['tambos_no_disponibles']['exportados']['tambores']++;
            } elseif ($noDisponible['estado'] == '4') {
                // Envasados
                $resultado['tambos_no_disponibles']['envasados']['neto'] += $noDisponible['neto'];
                $resultado['tambos_no_disponibles']['envasados']['tambores']++;
                // $resultado['encabezado']['envasados']++;
            }
            $resultado['tambos_no_disponibles']['netoTotal'] += $noDisponible['neto'];
            $resultado['tambos_no_disponibles']['tamboresTotal']++;
        }
    }


    return $resultado;
}

if (isset($_GET['idAuditoria'])) {
    try {
        $idAuditoria = $_GET['idAuditoria'];
        $resultado = obtenerInformacionAuditoria($idAuditoria);
        echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
    } catch (Exception $e) {
        echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => []]);
        exit();
    }
}
