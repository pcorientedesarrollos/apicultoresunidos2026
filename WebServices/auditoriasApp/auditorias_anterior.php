<?php

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
        switch ($tipoDeMiel) {
            case '1':
                $almacen_tabla = 'almacen';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                break;
            default:
                throw new Exception('El tipo de Miel no es válido');
                break;
        }


        // Verificar que el tambor existe en el sistema
        $sqlComprobarFolio = $con->prepare("SELECT idAlmacen FROM $almacen_tabla WHERE idAlmacen = :folio");
        $sqlComprobarFolio->bindParam(':folio', $folioTambor);
        $sqlComprobarFolio->execute();
        if ($sqlComprobarFolio == false) {
            throw new Exception($con->errorInfo());
        } else {
            if (count($sqlComprobarFolio->fetchAll(PDO::FETCH_ASSOC)) == 0) {
                echo json_encode(['error' => false, 'message' => 'El folio del tambor no está registrado en el sistema. No se han hecho cambios.']);
                exit();
            }
        }

        // Respaldo
        $sqlSelectZonaActual = $con->prepare("SELECT zona FROM $almacen_tabla WHERE idAlmacen = :folio");
        $sqlSelectZonaActual->bindParam(':folio', $folioTambor);
        $sqlSelectZonaActual->bindColumn('zona', $zonaActualTambor);
        $sqlSelectZonaActual->execute();
        if ($sqlSelectZonaActual == false) {
            throw new Exception($con->errorInfo());
        } else {
            $sqlSelectZonaActual->fetch(PDO::FETCH_BOUND);
        }

        $sentencia = $con->prepare("UPDATE $almacen_tabla SET zona = :zona WHERE idAlmacen = :folio");
        $sentencia->bindParam(':zona', $zona);
        $sentencia->bindParam(':folio', $folioTambor);
        $sentencia->execute();
        if ($sentencia == false) {
            throw new Exception($con->errorInfo());
        } else {

            $sqlRespaldo = $con->prepare('INSERT INTO respaldoauditoria (idAuditoria, idAlmacen, idTipoDeMiel, idZonaAntes, idZonaAuditoria, usuario)
                VALUES (:idAuditoria, :idAlmacen, :idTipoDeMiel, :idZonaAntes, :idZonaAuditoria, :usuario)');
            $sqlRespaldo->bindParam(':idAuditoria', $idAuditoria);
            $sqlRespaldo->bindParam(':idAlmacen', $folioTambor);
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
        } else {
            echo json_encode([]);
        }
    } else {
        throw new Exception('403 - Forbidden');
    }

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
