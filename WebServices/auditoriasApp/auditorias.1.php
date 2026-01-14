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

                        $sqlComprobarFolio = $con->prepare("SELECT idEntradaSobrante FROM `almacensobrantes` WHERE tipoDeMiel = :tipoDeMiel
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
        $sql = "SELECT a.idAuditoria, a.fecha, a.estado FROM auditorias a
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
        // UNION
        //     SELECT zt.nombre as zona, tdm.tipoDeMiel, zt.tipoMiel as idTipoDeMiel, zt.idZonaTambor as idZonaAuditoria
        //     FROM zonastambores zt
        //     LEFT JOIN tiposdemiel tdm ON tdm.idTipoDeMiel = zt.tipoMiel
        //     WHERE idZonaTambor NOT IN (
        //     SELECT idZonaAuditoria FROM respaldoauditoria WHERE idAuditoria = :idAuditoria
        //     GROUP BY idZonaAuditoria) AND (SELECT COUNT(a.idAlmacen) FROM almacen_organico a WHERE a.zona = zt.idZonaTambor > 0)
        // UNION	
        //     SELECT zt.nombre as zona, tdm.tipoDeMiel, zt.tipoMiel as idTipoDeMiel, zt.idZonaTambor as idZonaAuditoria
        //     FROM zonastambores zt
        //     LEFT JOIN tiposdemiel tdm ON tdm.idTipoDeMiel = zt.tipoMiel
        //     WHERE idZonaTambor NOT IN (
        //     SELECT idZonaAuditoria FROM respaldoauditoria WHERE idAuditoria = :idAuditoria
        //     GROUP BY idZonaAuditoria) AND (SELECT COUNT(a.idEntradaSobrante) FROM almacensobrantes a WHERE a.zona = zt.idZonaTambor > 0)
        //     ORDER BY idZonaAuditoria";
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

            // $respaldoAuditoria = "SELECT idAlmacen, ra.hora, ra.idRespaldoAuditoria, ra.usuario, ra.sobrante
            //     FROM respaldoauditoria ra
            //     WHERE ra.idZonaAuditoria = :idZonaAuditoria AND ra.idTipoDeMiel = :idTipoDeMiel AND ra.idAuditoria = :idAuditoria";
            // $respaldoAuditoria = "SELECT MAX(hora) AS hora, CONCAT(idAlmacen,'-',sobrante) AS codigo, 
            // idAlmacen,
            // MAX(idRespaldoAuditoria) AS idRespaldoAuditoria,
            // usuario,
            // sobrante 
            // FROM respaldoauditoria
            // WHERE idTipoDeMiel = :idTipoDeMiel AND idAuditoria = :idAuditoria AND idZonaAuditoria = :idZonaAuditoria GROUP BY codigo ORDER BY idAlmacen";
            $respaldoAuditoria = "SELECT MAX(hora) AS hora, 
            idAlmacen,
            MAX(idRespaldoAuditoria) AS idRespaldoAuditoria,
            usuario,
            sobrante 
            FROM respaldoauditoria
            WHERE idTipoDeMiel = :idTipoDeMiel AND idAuditoria = :idAuditoria AND idZonaAuditoria = :idZonaAuditoria GROUP BY idAlmacen,sobrante
            ORDER BY idAlmacen";
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
            //  else {
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
            // }

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

                    // Verificar si fue la ultima vez que se escaneó este tambor, pudo haberse escaneado más de una vez
                    // if ($tambor['sobrante']) {
                    //     $sqlVerificacion = $con->prepare("SELECT registros.idRespaldoAuditoria FROM (
                    //         SELECT idRespaldoAuditoria, hora
                    //         FROM respaldoauditoria
                    //         WHERE idAlmacen = :idAlmacen AND idAuditoria = :idAuditoria AND idTipoDeMiel = :idTipoDeMiel) registros
                    //         LEFT JOIN ( SELECT MAX(hora) as hora_ultimo
                    //         FROM respaldoauditoria
                    //         WHERE idAlmacen = :idAlmacen AND idAuditoria = :idAuditoria AND idTipoDeMiel = :idTipoDeMiel AND sobrante = :sobrante)  ultimo
                    //     ON registros.hora = ultimo.hora_ultimo
                    //     WHERE registros.hora = ultimo.hora_ultimo");
                    //     $sqlVerificacion->bindParam(':sobrante', $tambor['sobrante']);
                    // } else {
                    //     $sqlVerificacion = $con->prepare("SELECT registros.idRespaldoAuditoria FROM (
                    //         SELECT idRespaldoAuditoria, hora
                    //         FROM respaldoauditoria
                    //         WHERE idAlmacen = :idAlmacen AND idAuditoria = :idAuditoria AND idTipoDeMiel = :idTipoDeMiel) registros
                    //         LEFT JOIN ( SELECT MAX(hora) as hora_ultimo
                    //         FROM respaldoauditoria
                    //         WHERE idAlmacen = :idAlmacen AND idAuditoria = :idAuditoria AND idTipoDeMiel = :idTipoDeMiel) ultimo
                    //     ON registros.hora = ultimo.hora_ultimo
                    //     WHERE registros.hora = ultimo.hora_ultimo");
                    // }
                    // $sqlVerificacion->bindParam(':idAlmacen', $tambor['idAlmacen']);
                    // $sqlVerificacion->bindParam(':idAuditoria', $idAuditoria);
                    // $sqlVerificacion->bindParam(':idTipoDeMiel', $zona['idTipoDeMiel']);
                    // $sqlVerificacion->execute();

                    // if ($sqlVerificacion == false) {
                    //     throw new Exception($con->errorInfo());
                    // }

                    // $resultadoVerificacion = $sqlVerificacion->fetch(PDO::FETCH_ASSOC);
                    // La consulta verifica si hay mas respaldos con el id, si hay devuelve el id del movimiento
                    // if ($resultadoVerificacion['idRespaldoAuditoria'] == $tambor['idRespaldoAuditoria']) {
                    // Si el id coincide, este tambor se queda
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
                    // }
                } else {
                    // Estos tambores no se escanearon
                    if (
                        $tambor['estado'] == '0'
                        || $tambor['estado'] == '1'
                        // || $tambor['estado'] == '2'
                        // || $tambor['estado'] == '4'
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
        foreach ($resultado['zonas'] as $zona) {
            $resultado['totalEscaneado'] += $zona['netoEscaneado'];
            $resultado['totalNoEscaneado'] += $zona['netoNoEscaneado'];
        }
        $resultado['netoTotal'] = $resultado['totalEscaneado'] + $resultado['totalNoEscaneado'];
        echo json_encode(['error' => false, 'message' => 'Consulta exitosa', 'data' => $resultado]);
    }

    function getFoliosEscaneados($idAuditoria, $idZonaAuditoria, $baseDeDatos)
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
        // $sql = $con->prepare('SELECT DISTINCT(ra.idAlmacen) AS folio FROM respaldoauditoria ra WHERE ra.idZonaAuditoria = :idZona AND ra.idAuditoria = :idAuditoria');
        //     $sql = $con->prepare("SELECT CONCAT(s.codigo, ' -')AS codigo, rau.idAlmacen AS folio
        //    FROM respaldoauditoria rau
        //    LEFT JOIN sobrantes s ON s.idSobrante = rau.sobrante
        //    WHERE rau.idAuditoria = :idAuditoria AND rau.idZonaAuditoria = :idZona
        //    GROUP BY rau.idAlmacen,rau.sobrante
        //    ORDER BY rau.idAlmacen");
        $sql = $con->prepare("SELECT CONCAT(s.codigo, ' -')AS codigo, rau.idAlmacen AS folio,
        (SELECT IF(ISNULL(rau.sobrante),
        (SELECT DISTINCT(a.estado) AS estado FROM almacen a WHERE rau.idAlmacen = a.idAlmacen AND ISNULL(rau.sobrante)), 
        (SELECT DISTINCT(a.estado) AS estado FROM almacensobrantes a WHERE rau.sobrante = a.sobrante AND a.consecutivo = rau.idAlmacen)
        )) AS estado_tambor
               FROM respaldoauditoria rau
               LEFT JOIN sobrantes s ON s.idSobrante = rau.sobrante
               WHERE rau.idAuditoria = :idAuditoria AND rau.idZonaAuditoria = :idZona
               GROUP BY rau.idAlmacen,rau.sobrante
               ORDER BY rau.idAlmacen");
        $sql->bindParam(':idZona', $idZonaAuditoria);
        $sql->bindParam(':idAuditoria', $idAuditoria);
        $sql->execute();
        if ($sql == false) {
            throw new Exception($con->errorInfo());
        }
        $resultado['folios'] = $sql->fetchAll(PDO::FETCH_ASSOC);
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
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                $almacenencabezado_tabla = 'almacenencabezado_organico';
                break;
        }

        $foliosNO = "SELECT alm.idAlmacen, alm.neto,
        CASE WHEN alm.estado = 0 THEN 'Disponible' WHEN alm.estado = 1 THEN 'Experimental' END as estado_tambor, alm.estado
        FROM $almacen_tabla alm 
        LEFT JOIN auditorias aud ON aud.idAuditoria = :idAuditoria 
        LEFT JOIN zonastambores zt ON zt.idZonaTambor = alm.zona 
        LEFT JOIN $almacenencabezado_tabla ae ON ae.idAlmacen = alm.idAlmacenEncabezado 
        WHERE alm.zona = :idZonaAuditoria AND (alm.estado = '0' OR alm.estado = '1')
        AND alm.idAlmacen NOT IN (SELECT idAlmacen FROM respaldoauditoria WHERE idAuditoria = :idAuditoria AND idTipoDeMiel = :idTipoDeMiel AND sobrante IS NULL)
        AND (ae.fecha <= aud.finalizado OR aud.finalizado IS NULL)
        UNION 
        SELECT CONCAT(s.codigo, '-', alm.consecutivo) AS idAlmacen, alm.neto,
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
        foreach ($sqlAlmacen->fetchAll(PDO::FETCH_ASSOC) as $folios) {
            // 
            $resultado['neto'] += $folios['neto'];
            // Estos tambores no se escanearon
            // if (
            //     $folios['estado'] == '0'
            //     || $folios['estado'] == '1'
            // ) {
            // Disponibles, Experimental
            // $resultado1['no_escaneados']['neto'] += $folios['neto'];
            // $resultado['no_escaneados']['tambores']++;
            //     array_push($resultado['folios'], $folios);
            // }
            // array_push($resultado1['tambores_escaneados'], $tambor);
            // array_push($resultado1['tambores_no_escaneados'], $tambor);
            array_push($resultado['folios'], $folios);
        }

        // $resultado['folios'] = $sqlAlmacen->fetchAll(PDO::FETCH_ASSOC);
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
            getFoliosEscaneados($datos->idAuditoria, $datos->idZonaAuditoria, $datos->baseDeDatos);
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
