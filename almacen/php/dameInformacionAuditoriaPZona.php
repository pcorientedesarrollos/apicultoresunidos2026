<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$datos = file_get_contents('php://input');

function filtrarReporte($resultado, $tipoReporte, $fechaAuditoria)
{
    global $con;
    $resultadoFiltrado = array(
        'encabezado' => array(
            'bruto' => 0,
            'tara' => 0,
            'neto' => 0,
            'se_mantuvo' => 0,
            'cambio_zona' => 0,
            'no_escaneado' => 0
        ),
        'encabezadoNoEscaneado' => array(
            'bruto' => 0,
            'tara' => 0,
            'neto' => 0,
            'no_escaneado' => 0
        ),
        'tambores' => array(),
        'tambores_noEscaneados' => array()
    );

    switch ($tipoReporte) {
        case '1':
            foreach ($resultado['foliosEscaneados'] as $tambor) {
                if ($tambor['zonaAntes'] == $tambor['zonaDespues']) {
                    $resultadoFiltrado['encabezado']['bruto'] += $tambor['bruto'];
                    $resultadoFiltrado['encabezado']['tara'] += $tambor['tara'];
                    $resultadoFiltrado['encabezado']['neto'] += $tambor['neto'];
                    $resultadoFiltrado['encabezado']['se_mantuvo']++;
                    array_push($resultadoFiltrado['tambores'], $tambor);
                }
            }
            break;
        case '2':
            foreach ($resultado['foliosEscaneados'] as $tambor) {
                if ($tambor['zonaAntes'] != $tambor['zonaDespues'] && $tambor['zonaDespues'] != '') {
                    $resultadoFiltrado['encabezado']['bruto'] += $tambor['bruto'];
                    $resultadoFiltrado['encabezado']['tara'] += $tambor['tara'];
                    $resultadoFiltrado['encabezado']['neto'] += $tambor['neto'];
                    $resultadoFiltrado['encabezado']['cambio_zona']++;
                    array_push($resultadoFiltrado['tambores'], $tambor);
                }
            }
            break;
        case '3':
            foreach ($resultado['foliosNoEscaneados'] as $tambor) {
                // if ($tambor['zona'] == '') {
                if ($tambor['fecha'] <= $fechaAuditoria) {
                    $resultadoFiltrado['encabezado']['bruto'] += $tambor['bruto'];
                    $resultadoFiltrado['encabezado']['tara'] += $tambor['tara'];
                    $resultadoFiltrado['encabezado']['neto'] += $tambor['neto'];
                    $resultadoFiltrado['encabezado']['no_escaneado']++;
                    array_push($resultadoFiltrado['tambores'], $tambor);
                }
                // }
            }
            break;
        case '4':
            foreach ($resultado['foliosEscaneados'] as $tambor) {
                if ($tambor['fecha'] <= $fechaAuditoria) {
                    $resultadoFiltrado['encabezado']['bruto'] += $tambor['bruto'];
                    $resultadoFiltrado['encabezado']['tara'] += $tambor['tara'];
                    $resultadoFiltrado['encabezado']['neto'] += $tambor['neto'];
                    $resultadoFiltrado['encabezado']['no_escaneado']++;
                    array_push($resultadoFiltrado['tambores'], $tambor);
                }
            }
            foreach ($resultado['foliosNoEscaneados'] as $tambor) {
                if ($tambor['fecha'] <= $fechaAuditoria) {
                    $resultadoFiltrado['encabezadoNoEscaneado']['bruto'] += $tambor['bruto'];
                    $resultadoFiltrado['encabezadoNoEscaneado']['tara'] += $tambor['tara'];
                    $resultadoFiltrado['encabezadoNoEscaneado']['neto'] += $tambor['neto'];
                    $resultadoFiltrado['encabezadoNoEscaneado']['no_escaneado']++;
                    array_push($resultadoFiltrado['tambores_noEscaneados'], $tambor);
                }
            }
            break;
    }

    return $resultadoFiltrado;
}

function obtenerDetalleZona($idZonaAuditoria, $idTipoDeMiel, $idAuditoria, $tipoReporte = false)
{
    global $con;

    $sqlEncabezado = $con->prepare("SELECT z.nombre AS zona, t.tipoDeMiel, 
    (SELECT a.fecha FROM auditorias a WHERE a.idAuditoria = :idAuditoria) AS fecha
        FROM zonastambores z 
        LEFT JOIN tiposdemiel t ON t.idTipoDeMiel = z.tipoMiel
        WHERE z.idZonaTambor = :idZona");
    $sqlEncabezado->bindParam(':idZona', $idZonaAuditoria);
    $sqlEncabezado->bindParam(':idAuditoria', $idAuditoria);
    $sqlEncabezado->execute();
    if ($sqlEncabezado == false) {
        throw new Exception($con->errorInfo());
    }
    $resultado = $sqlEncabezado->fetch(PDO::FETCH_ASSOC);

    $resultado['foliosEscaneados'] = array();
    $resultado['foliosNoEscaneados'] = array();
    $resultado['foliosMpNoDisponibles'] = array();
    $resultado['resultadoTamboresEscaneados'] = array(
        'disponibles' => array(
            'bruto' => 0,
            'tara' => 0,
            'neto' => 0,
            'tambores' => 0
        ),
        'experimentales' => array(
            'bruto' => 0,
            'tara' => 0,
            'neto' => 0,
            'tambores' => 0
        ),
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
        'bruto' => 0,
        'tara' => 0,
        'neto' => 0
    );
    $resultado['resultadoTamboresNoEscaneados'] = array(
        'disponibles' => array(
            'bruto' => 0,
            'tara' => 0,
            'neto' => 0,
            'tambores' => 0
        ),
        'experimentales' => array(
            'bruto' => 0,
            'tara' => 0,
            'neto' => 0,
            'tambores' => 0
        ),
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
        'bruto' => 0,
        'tara' => 0,
        'neto' => 0,
        'tambores' => []
    );
    $resultado['encabezado'] = array(
        'se_mantuvo' => 0,
        'cambio_zona' => 0,
        'no_escaneado' => 0,
        'entrada_posterior' => 0,
        'escaneados' => 0,
        'no_encontrado' => 0,
        'envasados' => 0,
        'no_encontrado_disponible' => 0,
        'no_encontrado_noDisponible' => 0,
    );

    switch ($idTipoDeMiel) {
        case '1':
            $tabla_almacen = 'almacen';
            $tabla_almacen_encabezado = 'almacenencabezado';
            break;
        case '2':
            $tabla_almacen_encabezado = 'almacenencabezado_organico';
            $tabla_almacen = 'almacen_organico';
            break;
    }

    $sql = $con->prepare("SELECT IF(ISNULL(rau.sobrante), rau.idAlmacen, CONCAT(s.codigo, '-', rau.idAlmacen)) AS folio, sobrante, rau.idTipoDeMiel,
    ztd.nombre AS zonaDespues, zta.nombre AS zonaAntes, rau.hora, rau.usuario, tp.tipoDeMiel,
    (SELECT IF(ISNULL(rau.sobrante),
    (SELECT DISTINCT(a.estado) AS estado FROM $tabla_almacen a WHERE rau.idAlmacen = a.idAlmacen AND ISNULL(rau.sobrante)), 
    (SELECT DISTINCT(a.estado) AS estado FROM almacensobrantes a WHERE rau.sobrante = a.sobrante AND a.consecutivo = rau.idAlmacen AND a.tipoDeMiel = :miel)
    )) AS estado,
    (SELECT IF(ISNULL(rau.sobrante),
    (SELECT DISTINCT(a.bruto) FROM $tabla_almacen a WHERE rau.idAlmacen = a.idAlmacen AND ISNULL(rau.sobrante)), 
    (SELECT DISTINCT(a.bruto) FROM almacensobrantes a WHERE rau.sobrante = a.sobrante AND a.consecutivo = rau.idAlmacen AND a.tipoDeMiel = :miel LIMIT 1)
    )) AS bruto,
    (SELECT IF(ISNULL(rau.sobrante),
    (SELECT DISTINCT(a.tara) FROM $tabla_almacen a WHERE rau.idAlmacen = a.idAlmacen AND ISNULL(rau.sobrante)), 
    (SELECT DISTINCT(a.tara) FROM almacensobrantes a WHERE rau.sobrante = a.sobrante AND a.consecutivo = rau.idAlmacen AND a.tipoDeMiel = :miel LIMIT 1)
    )) AS tara,
    (SELECT IF(ISNULL(rau.sobrante),
    (SELECT DISTINCT(a.neto) FROM $tabla_almacen a WHERE rau.idAlmacen = a.idAlmacen AND ISNULL(rau.sobrante)), 
    (SELECT DISTINCT(a.neto) FROM almacensobrantes a WHERE rau.sobrante = a.sobrante AND a.consecutivo = rau.idAlmacen AND a.tipoDeMiel = :miel LIMIT 1)
    )) AS neto,
    (SELECT IF(ISNULL(rau.sobrante),
    (SELECT a.fecha FROM $tabla_almacen_encabezado a LEFT JOIN $tabla_almacen alm ON alm.idAlmacenEncabezado = a.idAlmacen WHERE rau.idAlmacen = alm.idAlmacen AND ISNULL(rau.sobrante)), 
    (SELECT a.fecha FROM almacensobrantes a WHERE rau.sobrante = a.sobrante AND a.consecutivo = rau.idAlmacen AND a.tipoDeMiel = :miel LIMIT 1)
    )) AS fecha
    FROM respaldoauditoria rau
    LEFT JOIN sobrantes s ON s.idSobrante = rau.sobrante
    INNER JOIN (
    SELECT DISTINCT(idAlmacen) AS idAlmacen, MAX(hora) AS max_hora FROM respaldoauditoria WHERE idAuditoria = :idAuditoria GROUP BY idAlmacen,sobrante,idTipoDeMiel
    ) AS T2
    ON rau.hora = T2.max_hora
    LEFT JOIN zonastambores ztd ON ztd.idZonaTambor = rau.idZonaAuditoria
    LEFT JOIN zonastambores zta ON zta.idZonaTambor = rau.idZonaAntes
    LEFT JOIN tiposdemiel tp ON tp.idTipoDeMiel = rau.idTipoDeMiel
           WHERE rau.idAuditoria = :idAuditoria AND rau.idZonaAuditoria = :idZona
           GROUP BY rau.idAlmacen,rau.sobrante,rau.idTipoDeMiel
           ORDER BY rau.idAlmacen");
    $sql->bindParam(':idZona', $idZonaAuditoria);
    $sql->bindParam(':miel', $idTipoDeMiel);
    $sql->bindParam(':idAuditoria', $idAuditoria);
    $sql->execute();
    if ($sql == false) {
        throw new Exception($con->errorInfo());
    }
    $resultado['netoEscaneado'] = 0;
    $resultado['tamboresEscaneados'] = 0;
    foreach ($sql->fetchAll(PDO::FETCH_ASSOC) as $folios) {
        // Usar la base control:
        $sqlUseDB = $con->prepare("USE apicultorescontrol");
        $sqlUseDB->execute();
        if ($sqlUseDB == false) {
            throw new Exception($con->errorInfo());
        }
        // bajar usuarios
        session_start();
        $database = $_SESSION['database'];
        session_write_close();
        // SELECCIONAR AL USUARIO DE LA BASE DE CONTROL
        $sqlSeleccionaUsuario = $con->prepare("SELECT usuario FROM usuarios WHERE idUsuario = :idUsuario");
        $sqlSeleccionaUsuario->bindParam(':idUsuario', $folios['usuario']);
        $sqlSeleccionaUsuario->execute();
        if ($sqlSeleccionaUsuario == false) {
            throw new Exception($con->errorInfo());
        }
        $resultadoUsuario = $sqlSeleccionaUsuario->fetch(PDO::FETCH_ASSOC);
        $folios['usuario'] = $resultadoUsuario['usuario'];
        // regresar a la bases
        // volver a cambiar base de datos
        $sqlUseDB = $con->prepare("USE $database");
        $sqlUseDB->execute();
        if ($sqlUseDB == false) {
            throw new Exception($con->errorInfo());
        }

        $folios['estado_tambor'] = '';
        $folios['idLote'] = '';
        $idLote = '';
        switch ($folios['idTipoDeMiel']) {
            case '1':
                $almacen_tabla = 'almacen';
                $almacenencabezado_tabla = 'almacenencabezado';
                $tambores = 'tamboresexperimentales';
                $tabla_experimental = 'tamboresexperimentales';
                $tabla_lotes = 'tamboreslotes';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                $almacenencabezado_tabla = 'almacenencabezado_organico';
                $tambores = 'tamboresexperimentales_organico';
                $tabla_experimental = 'tamboresexperimentales_organico';
                $tabla_lotes = 'tamboreslotes_organico';
                break;
        };
        if ($folios['sobrante'] > 0) {
            $clasificacion = $folios['sobrante'];
        } else {
            $clasificacion = 0;
        }
        switch ($folios['estado']) {
            case '0':
                $folios['estado_tambor'] = "Disponible";
                break;
            case '1':
                $folios['estado_tambor'] = "Experimental";
                $sqlLote = $con->prepare("SELECT idLoteExperimental FROM $tabla_experimental WHERE folioTambor = :folio AND clasificacion = :clasificacion");
                $sqlLote->bindParam(':folio', $folios['folio']);
                $sqlLote->bindParam(':clasificacion', $clasificacion);
                $sqlLote->execute();
                $sqlLote->bindColumn('idLoteExperimental', $idLote);
                $sqlLote->fetch(PDO::FETCH_BOUND);
                $folios['idLote'] = $idLote;
                break;
            case '2':
                $folios['estado_tambor'] = "Lote interno";
                $sqlLote = $con->prepare("SELECT idLoteInterno FROM $tabla_lotes WHERE folioTambor = :folio AND clasificacion = :clasificacion");
                $sqlLote->bindParam(':folio', $folios['folio']);
                $sqlLote->bindParam(':clasificacion', $clasificacion);
                $sqlLote->execute();
                $sqlLote->bindColumn('idLoteInterno', $idLote);
                $sqlLote->fetch(PDO::FETCH_BOUND);
                $folios['idLote'] = $idLote;
                break;
            case '3':
                $folios['estado_tambor'] = "Exportación";
                $sqlLote = $con->prepare("SELECT CONCAT('- Lote', ' ', idLoteInterno) AS idLoteInterno FROM $tabla_lotes WHERE folioTambor = :folio AND clasificacion = :clasificacion");
                $sqlLote->bindParam(':folio', $folios['folio']);
                $sqlLote->bindParam(':clasificacion', $clasificacion);
                $sqlLote->execute();
                $sqlLote->bindColumn('idLoteInterno', $idLote);
                $sqlLote->fetch(PDO::FETCH_BOUND);
                $folios['idLote'] = $idLote;
                break;
            case '4':
                $folios['estado_tambor'] = "Envasado";
                $sqlLote = $con->prepare("SELECT CONCAT(' - Lote', ' ', idLoteInterno) AS idLoteInterno FROM $tabla_lotes WHERE folioTambor = :folio AND clasificacion = :clasificacion");
                $sqlLote->bindParam(':folio', $folios['folio']);
                $sqlLote->bindParam(':clasificacion', $clasificacion);
                $sqlLote->execute();
                $sqlLote->bindColumn('idLoteInterno', $idLote);
                $sqlLote->fetch(PDO::FETCH_BOUND);
                $folios['idLote'] = $idLote;
                break;
        }

        $resultado['netoEscaneado'] += $folios['neto'];
        $resultado['tamboresEscaneados']++;
        if ($folios['estado'] == '0') {
            // Disponibles
            $resultado['resultadoTamboresEscaneados']['disponibles']['bruto'] += $folios['bruto'];
            $resultado['resultadoTamboresEscaneados']['disponibles']['tara'] += $folios['tara'];
            $resultado['resultadoTamboresEscaneados']['disponibles']['neto'] += $folios['neto'];
            $resultado['resultadoTamboresEscaneados']['disponibles']['tambores']++;
        } elseif ($folios['estado'] == '1') {
            // Experimentales
            $resultado['resultadoTamboresEscaneados']['experimentales']['bruto'] += $folios['bruto'];
            $resultado['resultadoTamboresEscaneados']['experimentales']['tara'] += $folios['tara'];
            $resultado['resultadoTamboresEscaneados']['experimentales']['neto'] += $folios['neto'];
            $resultado['resultadoTamboresEscaneados']['experimentales']['tambores']++;
        } elseif ($folios['estado'] == '2') {
            // Lotes internos
            $resultado['resultadoTamboresEscaneados']['lotes_internos']['bruto'] += $folios['bruto'];
            $resultado['resultadoTamboresEscaneados']['lotes_internos']['tara'] += $folios['tara'];
            $resultado['resultadoTamboresEscaneados']['lotes_internos']['neto'] += $folios['neto'];
            $resultado['resultadoTamboresEscaneados']['lotes_internos']['tambores']++;
        } elseif ($folios['estado'] == '3') {
            // Exportacion
            $resultado['resultadoTamboresEscaneados']['exportados']['bruto'] += $folios['bruto'];
            $resultado['resultadoTamboresEscaneados']['exportados']['tara'] += $folios['tara'];
            $resultado['resultadoTamboresEscaneados']['exportados']['neto'] += $folios['neto'];
            $resultado['resultadoTamboresEscaneados']['exportados']['tambores']++;
        } elseif ($folios['estado'] == '4') {
            // Envasado
            $resultado['resultadoTamboresEscaneados']['envasados']['bruto'] += $folios['bruto'];
            $resultado['resultadoTamboresEscaneados']['envasados']['tara'] += $folios['tara'];
            $resultado['resultadoTamboresEscaneados']['envasados']['neto'] += $folios['neto'];
            $resultado['resultadoTamboresEscaneados']['envasados']['tambores']++;
            // $resultado['encabezado']['envasados']++;
        }
        $resultado['resultadoTamboresEscaneados']['bruto'] += $folios['bruto'];
        $resultado['resultadoTamboresEscaneados']['tara'] += $folios['tara'];
        $resultado['resultadoTamboresEscaneados']['neto'] += $folios['neto'];
        $resultado['encabezado']['escaneados']++;
        if ($folios['fecha'] >= $resultado['fecha']) {
            $resultado['encabezado']['entrada_posterior']++;
        }

        if ($folios['zonaAntes'] == $folios['zonaDespues']) {
            $resultado['encabezado']['se_mantuvo']++;
        } else if ($folios['zonaAntes'] != $folios['zonaDespues'] && $folios['zonaDespues'] != '') {
            $resultado['encabezado']['cambio_zona']++;
        }
        array_push($resultado['foliosEscaneados'], $folios);
    }

    $foliosNO = "SELECT alm.idAlmacen AS folio, '$idTipoDeMiel' AS idTipoDeMiel, alm.neto, 0 AS sobrante,
    alm.bruto, alm.tara, zt.nombre AS zona, ae.fecha, zt.nombre AS zonaAntes, '' AS zonaDespues, '' AS usuario, '' AS hora, tp.tipoDeMiel,
    CASE WHEN alm.estado = 0 THEN 'Disponible' WHEN alm.estado = 1 THEN 'Experimental' END as estado_tambor, alm.estado
    FROM $almacen_tabla alm 
    LEFT JOIN auditorias aud ON aud.idAuditoria = :idAuditoria 
    LEFT JOIN zonastambores zt ON zt.idZonaTambor = alm.zona 
    LEFT JOIN $almacenencabezado_tabla ae ON ae.idAlmacen = alm.idAlmacenEncabezado 
    LEFT JOIN tiposdemiel tp ON tp.idTipoDeMiel = $idTipoDeMiel
    WHERE alm.zona = :idZonaAuditoria AND (alm.estado = '0' OR alm.estado = '1')
    AND alm.idAlmacen NOT IN (SELECT idAlmacen FROM respaldoauditoria WHERE idAuditoria = :idAuditoria AND idTipoDeMiel = :idTipoDeMiel AND sobrante IS NULL)
    AND (ae.fecha <= aud.finalizado OR aud.finalizado IS NULL)
    UNION 
    SELECT CONCAT(s.codigo, '-', alm.consecutivo) AS folio, alm.tipoDeMiel AS idTipoDeMiel, alm.neto, alm.sobrante,
    alm.bruto, alm.tara, zt.nombre AS zona, alm.fecha, zt.nombre AS zonaAntes, '' AS zonaDespues, '' AS usuario, '' AS hora, tp.tipoDeMiel,
    CASE WHEN alm.estado = 0 THEN 'Disponible' WHEN alm.estado = 1 THEN 'Experimental' END as estado_tambor, alm.estado
    FROM almacensobrantes alm 
    LEFT JOIN auditorias aud ON aud.idAuditoria = :idAuditoria 
    LEFT JOIN zonastambores zt ON zt.idZonaTambor = alm.zona
    LEFT JOIN sobrantes s ON s.idSobrante = alm.sobrante 
    LEFT JOIN tiposdemiel tp ON tp.idTipoDeMiel = alm.tipoDeMiel 
    WHERE alm.zona = :idZonaAuditoria AND (alm.estado = '0' OR alm.estado = '1')
    AND alm.consecutivo NOT IN (SELECT idAlmacen FROM respaldoauditoria WHERE idAuditoria = :idAuditoria AND idTipoDeMiel = :idTipoDeMiel AND sobrante > 0)
    AND (alm.fecha <= aud.finalizado OR aud.finalizado IS NULL)";
    $sqlAlmacen = $con->prepare($foliosNO);
    $sqlAlmacen->bindParam(':idZonaAuditoria', $idZonaAuditoria);
    $sqlAlmacen->bindParam(':idTipoDeMiel', $idTipoDeMiel);
    $sqlAlmacen->bindParam(':idAuditoria', $idAuditoria);
    $sqlAlmacen->execute();
    if ($sqlAlmacen == false) {
        throw new Exception($con->errorInfo());
    }
    $resultado['netoNoEscaneado'] = 0;
    $resultado['tamboresNoEscaneados'] = 0;
    foreach ($sqlAlmacen->fetchAll(PDO::FETCH_ASSOC) as $folios) {
        $folios['estado_tambor'] = '';
        $folios['idLote'] = '';
        $idLote = '';
        if ($folios['sobrante'] > 0) {
            $clasificacion = $folios['sobrante'];
            $idAlmacen1 = explode('-', $folios['folio']);
            $idAlmacen = $idAlmacen1[1];
        } else {
            $clasificacion = 0;
            $idAlmacen = $folios['folio'];
        }
        switch ($folios['estado']) {
            case '0':
                $folios['estado_tambor'] = "Disponible";

                break;
            case '1':
                $folios['estado_tambor'] = "Experimental";
                $sqlLote = $con->prepare("SELECT idLoteExperimental FROM $tambores WHERE folioTambor = :folio AND clasificacion = :clasificacion");
                $sqlLote->bindParam(':folio', $idAlmacen);
                $sqlLote->bindParam(':clasificacion', $clasificacion);
                $sqlLote->execute();
                $sqlLote->bindColumn('idLoteExperimental', $idLote);
                $sqlLote->fetch(PDO::FETCH_BOUND);
                $folios['idLote'] = $idLote;
                break;
        }

        if ($folios['estado'] == '0') {
            // Disponibles
            $resultado['resultadoTamboresNoEscaneados']['disponibles']['bruto'] += $folios['bruto'];
            $resultado['resultadoTamboresNoEscaneados']['disponibles']['tara'] += $folios['tara'];
            $resultado['resultadoTamboresNoEscaneados']['disponibles']['neto'] += $folios['neto'];
            $resultado['resultadoTamboresNoEscaneados']['disponibles']['tambores']++;
        } elseif ($folios['estado'] == '1') {
            // Experimentales
            $resultado['resultadoTamboresNoEscaneados']['experimentales']['bruto'] += $folios['bruto'];
            $resultado['resultadoTamboresNoEscaneados']['experimentales']['tara'] += $folios['tara'];
            $resultado['resultadoTamboresNoEscaneados']['experimentales']['neto'] += $folios['neto'];
            $resultado['resultadoTamboresNoEscaneados']['experimentales']['tambores']++;
        }
        $resultado['resultadoTamboresNoEscaneados']['bruto'] += $folios['bruto'];
        $resultado['resultadoTamboresNoEscaneados']['tara'] += $folios['tara'];
        $resultado['resultadoTamboresNoEscaneados']['neto'] += $folios['neto'];
        $resultado['netoNoEscaneado'] += $folios['neto'];
        $resultado['tamboresNoEscaneados']++;
        // if (isset($folios['fecha']) && $folios['fecha'] >= $resultado['fecha']) {
        //     $resultado['encabezado']['entrada_posterior']++;
        // } 
        // else {
        $resultado['encabezado']['no_encontrado_disponible']++;
        // }
        $resultado['encabezado']['no_escaneado']++;
        array_push($resultado['foliosNoEscaneados'], $folios);
    }


    $mpNoDisponible = "SELECT alm.idAlmacen AS folio, '$idTipoDeMiel' AS idTipoDeMiel, alm.neto, 0 AS sobrante,
    alm.bruto, alm.tara, zt.nombre AS zona, ae.fecha,
    CASE WHEN alm.estado = 2 THEN 'Lote Interno' WHEN alm.estado = 3 THEN 'Envasado' WHEN alm.estado = 4 THEN 'Exportación' END as estado_tambor, alm.estado
    FROM $almacen_tabla alm 
    LEFT JOIN auditorias aud ON aud.idAuditoria = :idAuditoria 
    LEFT JOIN zonastambores zt ON zt.idZonaTambor = alm.zona 
    LEFT JOIN $almacenencabezado_tabla ae ON ae.idAlmacen = alm.idAlmacenEncabezado 
    WHERE alm.zona = :idZonaAuditoria AND alm.estado = 2
    AND alm.idAlmacen NOT IN (SELECT idAlmacen FROM respaldoauditoria WHERE idAuditoria = :idAuditoria AND idTipoDeMiel = :idTipoDeMiel AND sobrante IS NULL)
    AND (ae.fecha <= aud.finalizado OR aud.finalizado IS NULL)
    UNION 
    SELECT CONCAT(s.codigo, '-', alm.consecutivo) AS folio, alm.tipoDeMiel AS idTipoDeMiel, alm.neto, alm.sobrante,
    alm.bruto, alm.tara, zt.nombre AS zona, alm.fecha,
    CASE WHEN alm.estado = 2 THEN 'Lote Interno' WHEN alm.estado = 3 THEN 'Envasado' WHEN alm.estado = 4 THEN 'Exportación' END as estado_tambor, alm.estado
    FROM almacensobrantes alm 
    LEFT JOIN auditorias aud ON aud.idAuditoria = :idAuditoria 
    LEFT JOIN zonastambores zt ON zt.idZonaTambor = alm.zona
    LEFT JOIN sobrantes s ON s.idSobrante = alm.sobrante 
    WHERE alm.zona = :idZonaAuditoria AND alm.estado = 2
    AND alm.consecutivo NOT IN (SELECT idAlmacen FROM respaldoauditoria WHERE idAuditoria = :idAuditoria AND idTipoDeMiel = :idTipoDeMiel AND sobrante > 0)
    AND (alm.fecha <= aud.finalizado OR aud.finalizado IS NULL)";
    $sqlMpNoDisponible = $con->prepare($mpNoDisponible);
    $sqlMpNoDisponible->bindParam(':idZonaAuditoria', $idZonaAuditoria);
    $sqlMpNoDisponible->bindParam(':idTipoDeMiel', $idTipoDeMiel);
    $sqlMpNoDisponible->bindParam(':idAuditoria', $idAuditoria);
    $sqlMpNoDisponible->execute();
    if ($sqlMpNoDisponible == false) {
        throw new Exception($con->errorInfo());
    }
    $resultado['netoMpNoDisponible'] = 0;
    $resultado['tamboresMpNoDisponibles'] = 0;
    foreach ($sqlMpNoDisponible->fetchAll(PDO::FETCH_ASSOC) as $folios) {
        $folios['estado_tambor'] = '';
        $folios['idLote'] = '';
        $idLote = '';
        if ($folios['sobrante'] > 0) {
            $clasificacion = $folios['sobrante'];
            $idAlmacen1 = explode('-', $folios['folio']);
            $idAlmacen = $idAlmacen1[1];
        } else {
            $clasificacion = 0;
            $idAlmacen = $folios['folio'];
        }
        switch ($folios['estado']) {
            case '2':
                $folios['estado_tambor'] = "Lote interno";
                $sqlLote = $con->prepare("SELECT idLoteInterno FROM $tabla_lotes WHERE folioTambor = :folio AND clasificacion = :clasificacion");
                $sqlLote->bindParam(':folio', $folios['folio']);
                $sqlLote->bindParam(':clasificacion', $clasificacion);
                $sqlLote->execute();
                $sqlLote->bindColumn('idLoteInterno', $idLote);
                $sqlLote->fetch(PDO::FETCH_BOUND);
                $folios['idLote'] = $idLote;
                break;
            case '3':
                $folios['estado_tambor'] = "Exportación";
                $sqlLote = $con->prepare("SELECT CONCAT('- Lote', ' ', idLoteInterno) AS idLoteInterno FROM $tabla_lotes WHERE folioTambor = :folio AND clasificacion = :clasificacion");
                $sqlLote->bindParam(':folio', $folios['folio']);
                $sqlLote->bindParam(':clasificacion', $clasificacion);
                $sqlLote->execute();
                $sqlLote->bindColumn('idLoteInterno', $idLote);
                $sqlLote->fetch(PDO::FETCH_BOUND);
                $folios['idLote'] = $idLote;
                break;
            case '4':
                $folios['estado_tambor'] = "Envasado";
                $sqlLote = $con->prepare("SELECT CONCAT(' - Lote', ' ', idLoteInterno) AS idLoteInterno FROM $tabla_lotes WHERE folioTambor = :folio AND clasificacion = :clasificacion");
                $sqlLote->bindParam(':folio', $folios['folio']);
                $sqlLote->bindParam(':clasificacion', $clasificacion);
                $sqlLote->execute();
                $sqlLote->bindColumn('idLoteInterno', $idLote);
                $sqlLote->fetch(PDO::FETCH_BOUND);
                $folios['idLote'] = $idLote;
                break;
        }

        if ($folios['estado'] == '2') {
            // Lotes internos
            $resultado['resultadoTamboresNoEscaneados']['lotes_internos']['bruto'] += $folios['bruto'];
            $resultado['resultadoTamboresNoEscaneados']['lotes_internos']['tara'] += $folios['tara'];
            $resultado['resultadoTamboresNoEscaneados']['lotes_internos']['neto'] += $folios['neto'];
            $resultado['resultadoTamboresNoEscaneados']['lotes_internos']['tambores']++;
        } elseif ($folios['estado'] == '3') {
            // Exportacion
            $resultado['resultadoTamboresNoEscaneados']['exportados']['bruto'] += $folios['bruto'];
            $resultado['resultadoTamboresNoEscaneados']['exportados']['tara'] += $folios['tara'];
            $resultado['resultadoTamboresNoEscaneados']['exportados']['neto'] += $folios['neto'];
            $resultado['resultadoTamboresNoEscaneados']['exportados']['tambores']++;
        } elseif ($folios['estado'] == '4') {
            // Envasados
            $resultado['resultadoTamboresNoEscaneados']['envasados']['bruto'] += $folios['bruto'];
            $resultado['resultadoTamboresNoEscaneados']['envasados']['tara'] += $folios['tara'];
            $resultado['resultadoTamboresNoEscaneados']['envasados']['neto'] += $folios['neto'];
            $resultado['resultadoTamboresNoEscaneados']['envasados']['tambores']++;
            // $resultado['encabezado']['envasados']++;
        }
        $resultado['resultadoTamboresNoEscaneados']['bruto'] += $folios['bruto'];
        $resultado['resultadoTamboresNoEscaneados']['tara'] += $folios['tara'];
        $resultado['resultadoTamboresNoEscaneados']['neto'] += $folios['neto'];
        $resultado['netoMpNoDisponible'] += $folios['neto'];
        $resultado['tamboresMpNoDisponibles']++;
        // if (isset($folios['fecha']) && $folios['fecha'] >= $resultado['fecha']) {
        //     $resultado['encabezado']['entrada_posterior']++;
        // } 
        // else {
        $resultado['encabezado']['no_encontrado_noDisponible']++;
        // }
        $resultado['encabezado']['no_escaneado']++;
        array_push($resultado['foliosMpNoDisponibles'], $folios);
    }

    if ($tipoReporte) {
        $resultado = filtrarReporte($resultado, $tipoReporte, $resultado['fecha']);
    }

    return $resultado;
}

if (isset($_GET['obtenerDatos'])) {
    try {
        if (!$datos) {
            throw new Exception('No se recibieron parámetros');
        } else {
            $infoZona = json_decode($datos);
            $resultado = obtenerDetalleZona($infoZona->idZonaAuditoria, $infoZona->idTipoDeMiel, $infoZona->idAuditoria);
        }
        echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
    } catch (Exception $e) {
        echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => []]);
        exit();
    }
}
