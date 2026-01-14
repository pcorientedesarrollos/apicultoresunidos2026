<?php
// use ___PHPSTORM_HELPERS\object;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header("Content-Type: application/json");
include_once '../../DAOConeccion/conexionWebServices.php';

try {

    function obtenerTiposDeMiel($baseDeDatos)
    {
        $pdo = new conePDO();
        $con = $pdo->conectar($baseDeDatos);
        $sql = $con->prepare('SELECT * FROM tiposdemiel WHERE idTipoDeMiel IN (1,2)');
        $sql->execute();
        if ($sql == false) {
            throw new Exception($con->errorInfo());
        }
        $tiposDeMiel = $sql->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($tiposDeMiel);
        exit();
    }

    function tryLogin($user, $pass)
    {
        $pdo = new conePDO();
        $con = $pdo->conectar();
        $pass = md5($pass);

        $sentencia = $con->prepare("SELECT idUsuario, b.nombre as baseDeDatos
        FROM usuarios u
        LEFT JOIN empresas e ON u.idEmpresa = e.idEmpresa
        LEFT JOIN basesempresa b ON e.idEmpresa = b.IdEmpresa
        WHERE usuario = :user AND app = 1 AND b.anio = YEAR(NOW())");
        $sentencia->bindParam(':user', $user);
        $sentencia->bindColumn('idUsuario', $idUsuario);
        $sentencia->bindColumn('baseDeDatos', $baseDeDatos);
        $sentencia->execute();
        if ($sentencia == false) {
            throw new Exception($con->errorInfo());
        } else {
            $sentencia->fetch(PDO::FETCH_BOUND);
        }
        if ($sentencia->rowCount() === 1) {
            $getPass = $con->prepare("SELECT password FROM usuarios WHERE usuario = :user AND app = 1");
            $getPass->bindParam(':user', $user);
            $getPass->execute();
            if ($getPass == false) {
                throw new Exception($con->errorInfo());
            }
            $response = $getPass->fetch(PDO::FETCH_ASSOC);
            $passSystem = $response['password'];
            if ($pass === $passSystem) {
                echo json_encode(['error' => false, 'message' => 'Bienvenido', 'user' => $user, 'idUsuario' => $idUsuario, 'baseDeDatos' => $baseDeDatos]);
                exit();
            } else {
                throw new Exception('Credenciales inválidas');
            }
        } else {
            throw new Exception('Usuario o contraseña incorrectos');
        }
    }

    function getListaDeZonas($tipoDeMiel, $baseDeDatos)
    {
        $pdo = new conePDO();
        $con = $pdo->conectar($baseDeDatos);
        $sql = $con->prepare('SELECT idZonaTambor, nombre FROM `zonastambores` WHERE tipoMiel = :tipoDeMiel;');
        $sql->bindParam(':tipoDeMiel', $tipoDeMiel);
        $sql->execute();
        if ($sql == false) {
            throw new Exception($con->errorInfo());
        }
        $listaDeZonas = $sql->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($listaDeZonas);
        exit();
    }

    function cambiar_zona_tambor($zona, $folioTambor, $tipoDeMiel, $idAuditoria, $idUsuario, $baseDeDatos)
    {

        $pdo = new conePDO();
        $con = $pdo->conectar($baseDeDatos);

        // Descomponemos el arreglo por guiones
        $arreglo_composicion = explode('-', $folioTambor);
        // Obtenemos el último elemento del arreglo que es el FOLIO del tambor
        // Al hace array_pop, la propiedad se extrae el arreglo
        $folio_real = array_pop($arreglo_composicion);
        // Unimos los elementos sobrantes del arreglo para obtener el prefijo completo
        $prefijo_real = join($arreglo_composicion, '-');

        // Hacemos un filtro para ver el tipo de tambor que estamos escaneando
        switch ($prefijo_real) {
            case 'C':
                $tipoDeMiel = 1;
                // Si el prefijo es una letra 'C', se trata de un tambor de miel convencional
                // Verificar que exista el tambor
                $sqlComprobarFolio = $con->prepare("SELECT idAlmacen FROM almacen WHERE idAlmacen = :folio");
                $sqlComprobarFolio->bindParam(':folio', $folio_real);
                $sqlComprobarFolio->execute();
                if ($sqlComprobarFolio == false) {
                    throw new Exception($con->errorInfo());
                } else {
                    if (count($sqlComprobarFolio->fetchAll(PDO::FETCH_ASSOC)) == 0) {
                        echo json_encode(['error' => false, 'message' => 'El folio del tambor no está registrado en el sistema. No se han hecho cambios.']);
                        exit();
                    }
                }

                // Hacer el respaldo

                $sqlSelectZonaActual = $con->prepare("SELECT zona FROM almacen WHERE idAlmacen = :folio");
                $sqlSelectZonaActual->bindParam(':folio', $folio_real);
                $sqlSelectZonaActual->bindColumn('zona', $zonaActualTambor);
                $sqlSelectZonaActual->execute();
                if ($sqlSelectZonaActual == false) {
                    throw new Exception($con->errorInfo());
                } else {
                    $sqlSelectZonaActual->fetch(PDO::FETCH_BOUND);
                }

                $sentencia = $con->prepare("UPDATE almacen SET zona = :zona WHERE idAlmacen = :folio");
                $sentencia->bindParam(':zona', $zona);
                $sentencia->bindParam(':folio', $folio_real);
                $sentencia->execute();
                if ($sentencia == false) {
                    throw new Exception($con->errorInfo());
                } else {

                    $sqlRespaldo = $con->prepare('INSERT INTO respaldoauditoria (idAuditoria, idAlmacen, idTipoDeMiel, idZonaAntes, idZonaAuditoria, usuario)
                        VALUES (:idAuditoria, :idAlmacen, :idTipoDeMiel, :idZonaAntes, :idZonaAuditoria, :usuario)');
                    $sqlRespaldo->bindParam(':idAuditoria', $idAuditoria);
                    $sqlRespaldo->bindParam(':idAlmacen', $folio_real);
                    $sqlRespaldo->bindParam(':idTipoDeMiel', $tipoDeMiel);
                    $sqlRespaldo->bindParam(':idZonaAntes', $zonaActualTambor);
                    $sqlRespaldo->bindParam(':idZonaAuditoria', $zona);
                    $sqlRespaldo->bindParam(':usuario', $idUsuario);
                    $sqlRespaldo->execute();
                    if ($sqlRespaldo == false) {
                        throw new Exception($con->errorInfo());
                    } else {
                        echo json_encode(['error' => false, 'message' => 'Se ha modificado la zona del tambor.']);
                    }
                }

                // Fin del case


                break;
            case 'O':
                $tipoDeMiel = 2;
                // Si el prefijo es una letra 'O', se trata de un tambor de miel orgánica
                // Verificar que exista el tambor
                $sqlComprobarFolio = $con->prepare("SELECT idAlmacen FROM almacen_organico WHERE idAlmacen = :folio");
                $sqlComprobarFolio->bindParam(':folio', $folio_real);
                $sqlComprobarFolio->execute();
                if ($sqlComprobarFolio == false) {
                    throw new Exception($con->errorInfo());
                } else {
                    if (count($sqlComprobarFolio->fetchAll(PDO::FETCH_ASSOC)) == 0) {
                        echo json_encode(['error' => false, 'message' => 'El folio del tambor no está registrado en el sistema. No se han hecho cambios.']);
                        exit();
                    }
                }

                // Hacer el respaldo

                $sqlSelectZonaActual = $con->prepare("SELECT zona FROM almacen_organico WHERE idAlmacen = :folio");
                $sqlSelectZonaActual->bindParam(':folio', $folio_real);
                $sqlSelectZonaActual->bindColumn('zona', $zonaActualTambor);
                $sqlSelectZonaActual->execute();
                if ($sqlSelectZonaActual == false) {
                    throw new Exception($con->errorInfo());
                } else {
                    $sqlSelectZonaActual->fetch(PDO::FETCH_BOUND);
                }

                $sentencia = $con->prepare("UPDATE almacen_organico SET zona = :zona WHERE idAlmacen = :folio");
                $sentencia->bindParam(':zona', $zona);
                $sentencia->bindParam(':folio', $folio_real);
                $sentencia->execute();
                if ($sentencia == false) {
                    throw new Exception($con->errorInfo());
                } else {

                    $sqlRespaldo = $con->prepare('INSERT INTO respaldoauditoria (idAuditoria, idAlmacen, idTipoDeMiel, idZonaAntes, idZonaAuditoria, usuario)
                        VALUES (:idAuditoria, :idAlmacen, :idTipoDeMiel, :idZonaAntes, :idZonaAuditoria, :usuario)');
                    $sqlRespaldo->bindParam(':idAuditoria', $idAuditoria);
                    $sqlRespaldo->bindParam(':idAlmacen', $folio_real);
                    $sqlRespaldo->bindParam(':idTipoDeMiel', $tipoDeMiel);
                    $sqlRespaldo->bindParam(':idZonaAntes', $zonaActualTambor);
                    $sqlRespaldo->bindParam(':idZonaAuditoria', $zona);
                    $sqlRespaldo->bindParam(':usuario', $idUsuario);
                    $sqlRespaldo->execute();
                    if ($sqlRespaldo == false) {
                        throw new Exception($con->errorInfo());
                    } else {
                        echo json_encode(['error' => false, 'message' => 'Se ha modificado la zona del tambor.']);
                    }
                }

                // Fin del case

                break;
            default:

                // Si no es ni convencional ni organica, extraemos la última propiedad

                $sobrante_composicion = explode('-', $prefijo_real);
                $tipo_de_miel_sobrante = array_pop($sobrante_composicion);
                $prefijo_real = join($sobrante_composicion, '-');

                // verificamos que el prefijo entonces sea un código de miel sobrante

                $sqlVerificarPrefijo = $con->prepare("SELECT idSobrante FROM `sobrantes` WHERE codigo = :prefijo;");
                $sqlVerificarPrefijo->bindParam(':prefijo', $prefijo_real);
                $sqlVerificarPrefijo->execute();
                if ($sqlVerificarPrefijo == false) {
                    throw new Exception($con->errorInfo());
                } else {
                    if ($sqlVerificarPrefijo->rowCount() >= 1) {
                        $idSobrante = $sqlVerificarPrefijo->fetch(PDO::FETCH_ASSOC);

                        // consecutivo, sobrante, tipoDeMiel

                        // Verificar que exista el tambor

                        $sqlComprobarFolio = $con->prepare("SELECT consecutivo FROM `almacensobrantes` WHERE tipoDeMiel = :tipoDeMiel
                        AND sobrante = :idSobrante AND consecutivo = :consecutivo");
                        $sqlComprobarFolio->bindParam(':tipoDeMiel', $tipo_de_miel_sobrante);
                        $sqlComprobarFolio->bindParam(':idSobrante', $idSobrante['idSobrante']);
                        $sqlComprobarFolio->bindParam(':consecutivo', $folio_real);

                        $sqlComprobarFolio->execute();
                        if ($sqlComprobarFolio == false) {
                            throw new Exception($con->errorInfo());
                        } else {
                            if (count($sqlComprobarFolio->fetchAll(PDO::FETCH_ASSOC)) == 0) {
                                echo json_encode(['error' => false, 'message' => 'El folio del tambor no está registrado en el sistema. No se han hecho cambios.']);
                                exit();
                            }
                        }

                        // Hacer el respaldo

                        $sqlSelectZonaActual = $con->prepare("SELECT zona FROM almacensobrantes WHERE consecutivo = :consecutivo AND sobrante = :sobrante AND tipoDeMiel = :tipoDeMiel");
                        $sqlSelectZonaActual->bindParam(':consecutivo', $folio_real);
                        $sqlSelectZonaActual->bindParam(':sobrante', $idSobrante['idSobrante']);
                        $sqlSelectZonaActual->bindParam(':tipoDeMiel', $tipo_de_miel_sobrante);
                        $sqlSelectZonaActual->bindColumn('zona', $zonaActualTambor);
                        $sqlSelectZonaActual->execute();
                        if ($sqlSelectZonaActual == false) {
                            throw new Exception($con->errorInfo());
                        } else {
                            $sqlSelectZonaActual->fetch(PDO::FETCH_BOUND);
                        }

                        $sentencia = $con->prepare("UPDATE almacensobrantes SET zona = :zona WHERE consecutivo = :consecutivo AND sobrante = :sobrante AND tipoDeMiel = :tipoDeMiel");
                        $sentencia->bindParam(':consecutivo', $folio_real);
                        $sentencia->bindParam(':sobrante', $idSobrante['idSobrante']);
                        $sentencia->bindParam(':tipoDeMiel', $tipo_de_miel_sobrante);
                        $sentencia->bindParam(':zona', $zona);
                        $sentencia->execute();
                        if ($sentencia == false) {
                            throw new Exception($con->errorInfo());
                        } else {

                            $sqlRespaldo = $con->prepare('INSERT INTO respaldoauditoria (idAuditoria, idAlmacen, idTipoDeMiel, sobrante, idZonaAntes, idZonaAuditoria, usuario)
                                VALUES (:idAuditoria, :idAlmacen, :idTipoDeMiel, :sobrante, :idZonaAntes, :idZonaAuditoria, :usuario)');
                            $sqlRespaldo->bindParam(':idAuditoria', $idAuditoria);
                            $sqlRespaldo->bindParam(':idAlmacen', $folio_real);
                            $sqlRespaldo->bindParam(':idTipoDeMiel', $tipoDeMiel);
                            $sqlRespaldo->bindParam(':sobrante', $idSobrante['idSobrante']);
                            $sqlRespaldo->bindParam(':idZonaAntes', $zonaActualTambor);
                            $sqlRespaldo->bindParam(':idZonaAuditoria', $zona);
                            $sqlRespaldo->bindParam(':usuario', $idUsuario);
                            $sqlRespaldo->execute();
                            if ($sqlRespaldo == false) {
                                throw new Exception($con->errorInfo());
                            } else {
                                echo json_encode(['error' => false, 'message' => 'Se ha modificado la zona del tambor.']);
                            }
                        }

                        // Fin del case


                    } else {
                        throw new Exception('El tipo de tambor escaneado no es válido');
                    }
                }
                break;
        }
    }

    function getAuditoriasInfo($baseDeDatos)
    {
        $pdo = new conePDO();
        $con = $pdo->conectar($baseDeDatos);
        $sql = "SELECT a.idAuditoria, a.fecha, a.finalizado, a.estado FROM auditorias a
        WHERE a.estado != -1
        ORDER BY a.fecha DESC";
        $datos = $con->prepare($sql);
        $datos->execute();
        if ($datos == false) {
            throw new Exception($con->errorInfo());
        }
        $resultado = $datos->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['error' => false, 'message' => 'Consulta exitosa', 'data' => $resultado]);
    }

    function getAuditoria($idAuditoria, $baseDeDatos)
    {
        $pdo = new conePDO();
        $con = $pdo->conectar($baseDeDatos);

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
                'tambores_escaneados' => array(),
                'tambores_no_escaneados' => array()
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
                    array_push($resultado1['tambores_escaneados'], $tambor);
                    array_push($resultado1['tambores_no_escaneados'], $tambor);
                }
            }

            $zona['tambEscaneados'] = $resultado1['escaneados']['tambores'];
            $zona['tambores_faltantes'] = $resultado1['no_escaneados']['tambores'];
            $zona['netoEscaneado'] = $resultado1['escaneados']['neto'];
            $zona['netoNoEscaneado'] = $resultado1['no_escaneados']['neto'];
            array_push($resultado['zonas'], $zona);
        }
        $resultado['totalEscaneado'] = 0;
        $resultado['totalNoEscaneado'] = 0;
        $resultado['netoTotal'] = 0;
        $resultado['ttEscaneados'] = 0;
        $resultado['ttNoEscaneados'] = 0;
        $resultado['tambosTotal'] = 0;
        foreach ($resultado['zonas'] as $zona) {
            $resultado['totalEscaneado'] += $zona['netoEscaneado'];
            $resultado['totalNoEscaneado'] += $zona['netoNoEscaneado'];
            $resultado['ttEscaneados'] += $zona['tambEscaneados'];
            $resultado['ttNoEscaneados'] += $zona['tambores_faltantes'];
        }
        $resultado['netoTotal'] = $resultado['totalEscaneado'] + $resultado['totalNoEscaneado'];
        $resultado['tambosTotal'] = $resultado['ttEscaneados'] + $resultado['ttNoEscaneados'];
        echo json_encode(['error' => false, 'message' => 'Consulta exitosa', 'data' => $resultado]);
    }

    function getFoliosEscaneados($idAuditoria, $idZonaAuditoria, $tipoDeMiel, $baseDeDatos)
    {
        $pdo = new conePDO();
        $con = $pdo->conectar($baseDeDatos);

        $sqlEncabezado = $con->prepare("SELECT nombre AS zona FROM zonastambores WHERE idZonaTambor = :idZona");
        $sqlEncabezado->bindParam(':idZona', $idZonaAuditoria);
        $sqlEncabezado->execute();
        if ($sqlEncabezado == false) {
            throw new Exception($con->errorInfo());
        }

        $resultado = $sqlEncabezado->fetch(PDO::FETCH_ASSOC);
        $resultado['folios'] = array();

        switch ($tipoDeMiel) {
            case '1':
                $tabla_almacen = 'almacen';
                break;
            case '2':
                $tabla_almacen = 'almacen_organico';
                break;
        }

        $sql = $con->prepare("SELECT IF(ISNULL(rau.sobrante), rau.idAlmacen, CONCAT(s.codigo, '-', rau.idAlmacen)) AS folio, sobrante, idTipoDeMiel,
        (SELECT IF(ISNULL(rau.sobrante),
        (SELECT DISTINCT(a.estado) AS estado FROM $tabla_almacen a WHERE rau.idAlmacen = a.idAlmacen AND ISNULL(rau.sobrante)), 
        (SELECT DISTINCT(a.estado) AS estado FROM almacensobrantes a WHERE rau.sobrante = a.sobrante AND a.consecutivo = rau.idAlmacen AND a.tipoDeMiel = :miel)
        )) AS estado,
        (SELECT IF(ISNULL(rau.sobrante),
        (SELECT DISTINCT(a.neto) FROM $tabla_almacen a WHERE rau.idAlmacen = a.idAlmacen AND ISNULL(rau.sobrante)), 
        (SELECT DISTINCT(a.neto) FROM almacensobrantes a WHERE rau.sobrante = a.sobrante AND a.consecutivo = rau.idAlmacen AND a.tipoDeMiel = :miel LIMIT 1)
        )) AS neto
        FROM respaldoauditoria rau
        LEFT JOIN sobrantes s ON s.idSobrante = rau.sobrante
        INNER JOIN (
        SELECT DISTINCT(idAlmacen) AS idAlmacen, MAX(hora) AS max_hora FROM respaldoauditoria WHERE idAuditoria = :idAuditoria GROUP BY idAlmacen,sobrante,idTipoDeMiel
        ) AS T2
        ON rau.hora = T2.max_hora
               WHERE rau.idAuditoria = :idAuditoria AND rau.idZonaAuditoria = :idZona
               GROUP BY rau.idAlmacen,rau.sobrante,rau.idTipoDeMiel
               ORDER BY rau.idAlmacen");
        $sql->bindParam(':idZona', $idZonaAuditoria);
        $sql->bindParam(':miel', $tipoDeMiel);
        $sql->bindParam(':idAuditoria', $idAuditoria);
        $sql->execute();
        if ($sql == false) {
            throw new Exception($con->errorInfo());
        }
        $resultado['neto'] = 0;
        $resultado['tambores'] = 0;
        foreach ($sql->fetchAll(PDO::FETCH_ASSOC) as $folios) {
            $folios['estado_tambor'] = '';
            $folios['idLote'] = '';
            $idLote = '';
            switch ($folios['idTipoDeMiel']) {
                case '1':
                    $tabla_experimental = 'tamboresexperimentales';
                    $tabla_lotes = 'tamboreslotes';
                    break;
                case '2':
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

            $resultado['neto'] += $folios['neto'];
            $resultado['tambores']++;
            array_push($resultado['folios'], $folios);
        }
        echo json_encode(['error' => false, 'message' => 'Consulta exitosa', 'data' => $resultado]);
        exit();
    }

    function getFoliosNoEscaneados($idAuditoria, $idZonaAuditoria, $tipoDeMiel, $baseDeDatos)
    {
        $pdo = new conePDO();
        $con = $pdo->conectar($baseDeDatos);

        $sqlEncabezado = $con->prepare("SELECT nombre AS zona FROM zonastambores WHERE idZonaTambor = :idZona");
        $sqlEncabezado->bindParam(':idZona', $idZonaAuditoria);
        $sqlEncabezado->execute();
        if ($sqlEncabezado == false) {
            throw new Exception($con->errorInfo());
        }

        $resultado = $sqlEncabezado->fetch(PDO::FETCH_ASSOC);
        $resultado['folios'] = array();

        switch ($tipoDeMiel) {
            case '1':
                $almacen_tabla = 'almacen';
                $almacenencabezado_tabla = 'almacenencabezado';
                $tambores = 'tamboresexperimentales';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                $almacenencabezado_tabla = 'almacenencabezado_organico';
                $tambores = 'tamboresexperimentales_organico';
                break;
        }

        $foliosNO = "SELECT alm.idAlmacen, alm.neto, 0 AS sobrante,
        CASE WHEN alm.estado = 0 THEN 'Disponible' WHEN alm.estado = 1 THEN 'Experimental' END as estado_tambor, alm.estado
        FROM $almacen_tabla alm 
        LEFT JOIN auditorias aud ON aud.idAuditoria = :idAuditoria 
        LEFT JOIN zonastambores zt ON zt.idZonaTambor = alm.zona 
        LEFT JOIN $almacenencabezado_tabla ae ON ae.idAlmacen = alm.idAlmacenEncabezado 
        WHERE alm.zona = :idZonaAuditoria AND (alm.estado = '0' OR alm.estado = '1')
        AND alm.idAlmacen NOT IN (SELECT idAlmacen FROM respaldoauditoria WHERE idAuditoria = :idAuditoria AND idTipoDeMiel = :idTipoDeMiel AND sobrante IS NULL)
        AND (ae.fecha <= aud.finalizado OR aud.finalizado IS NULL)
        UNION 
        SELECT CONCAT(s.codigo, '-', alm.consecutivo) AS idAlmacen, alm.neto, alm.sobrante,
        CASE WHEN alm.estado = 0 THEN 'Disponible' WHEN alm.estado = 1 THEN 'Experimental' END as estado_tambor, alm.estado
        FROM almacensobrantes alm 
        LEFT JOIN auditorias aud ON aud.idAuditoria = :idAuditoria 
        LEFT JOIN zonastambores zt ON zt.idZonaTambor = alm.zona
        LEFT JOIN sobrantes s ON s.idSobrante = alm.sobrante 
        WHERE alm.zona = :idZonaAuditoria AND (alm.estado = '0' OR alm.estado = '1')
        AND alm.consecutivo NOT IN (SELECT idAlmacen FROM respaldoauditoria WHERE idAuditoria = :idAuditoria AND idTipoDeMiel = :idTipoDeMiel AND sobrante > 0)
        AND (alm.fecha <= aud.finalizado OR aud.finalizado IS NULL)";
        $sqlAlmacen = $con->prepare($foliosNO);
        $sqlAlmacen->bindParam(':idZonaAuditoria', $idZonaAuditoria);
        $sqlAlmacen->bindParam(':idTipoDeMiel', $tipoDeMiel);
        $sqlAlmacen->bindParam(':idAuditoria', $idAuditoria);
        $sqlAlmacen->execute();
        if ($sqlAlmacen == false) {
            throw new Exception($con->errorInfo());
        }
        $resultado['neto'] = 0;
        $resultado['tambores'] = 0;
        foreach ($sqlAlmacen->fetchAll(PDO::FETCH_ASSOC) as $folios) {
            $folios['estado_tambor'] = '';
            $folios['idLote'] = '';
            $idLote = '';
            if ($folios['sobrante'] > 0) {
                $clasificacion = $folios['sobrante'];
                $idAlmacen1 = explode('-', $folios['idAlmacen']);
                $idAlmacen = $idAlmacen1[1];
            } else {
                $clasificacion = 0;
                $idAlmacen = $folios['idAlmacen'];
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
            $resultado['neto'] += $folios['neto'];
            $resultado['tambores']++;
            array_push($resultado['folios'], $folios);
        }

        echo json_encode(['error' => false, 'message' => 'Consulta exitosa', 'data' => $resultado]);
        exit();
    }

    $postdata = file_get_contents('php://input');
    if (!$postdata) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($postdata);
    }

    if (isset($datos->keyAccess) && $datos->keyAccess == '208eab2fc88ad777d933581d64ba549a' && isset($datos->baseDeDatos)) {
        if (isset($_GET['getTiposDeMiel'])) {
            obtenerTiposDeMiel($datos->baseDeDatos);
        } else if (isset($_GET['getZonas']) && isset($datos->tipoDeMiel)) {
            getListaDeZonas($datos->tipoDeMiel, $datos->baseDeDatos);
        } else if (isset($_GET['cambiarZona']) && isset($datos->zona) && isset($datos->folioTambor) && isset($datos->tipoDeMiel) && isset($datos->idAuditoria) && isset($datos->idUsuario)) {
            cambiar_zona_tambor($datos->zona, $datos->folioTambor, $datos->tipoDeMiel, $datos->idAuditoria, $datos->idUsuario, $datos->baseDeDatos);
        } else if (isset($_GET['login']) && isset($datos->user) && isset($datos->password)) {
            tryLogin($datos->user, $datos->password);
        } else if (isset($_GET['getAuditoriasInfo'])) {
            getAuditoriasInfo($datos->baseDeDatos);
        } else if (isset($_GET['getAuditoria']) && isset($datos->idAuditoria)) {
            getAuditoria($datos->idAuditoria, $datos->baseDeDatos);
        } else if (isset($_GET['foliosEscaneadosZona'])) {
            getFoliosEscaneados($datos->idAuditoria, $datos->idZonaAuditoria, $datos->tipoDeMiel, $datos->baseDeDatos);
        } else if (isset($_GET['foliosNoEscaneadosZona'])) {
            getFoliosNoEscaneados($datos->idAuditoria, $datos->idZonaAuditoria, $datos->tipoDeMiel, $datos->baseDeDatos);
        } else {
            echo json_encode([]);
        }
    } else {
        throw new Exception('403 - Forbidden');
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
