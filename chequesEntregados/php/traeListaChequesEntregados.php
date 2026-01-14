<?php
include_once "../../DAOConeccion/conePDO.php";
include_once '../../controlAdministrativo/php/nombreDePersona.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$resultado = array();

try {

    $consulta = "SELECT che.*, b.banco
    FROM chequesentregados_encabezado che
    LEFT JOIN bancos b ON b.idBanco = che.idBanco
    ORDER BY che.fechaEntrega DESC";
    $consulta = $con->prepare($consulta);
    // ** ANA 2025 **
    // $consulta->bindParam(':idBanco', $idBanco);
    //** NO ES NECESARIO ESTE PARAMETRO, NO SE ESTA ENVIANDO NI RECIBIENDO */
    $consulta->execute();
    if ($consulta == false) {
        throw new Exception($con->errorInfo());
    }

    foreach ($consulta->fetchAll(PDO::FETCH_ASSOC) as $res) {
        $consulta_registros = "SELECT COUNT(c.idEntrega) AS registros
        FROM chequesentregados_detalle c
        WHERE c.idEncabezado =  :idEncabezado";
        $consulta_registros = $con->prepare($consulta_registros);
        $consulta_registros->bindParam(':idEncabezado', $res['idEncabezado']);
        $consulta_registros->execute();
        if ($consulta_registros == false) {
            throw new Exception($con->errorInfo());
        }
        $registros = $consulta_registros->fetch(PDO::FETCH_ASSOC);
        if (!$registros) {
            $res['registros'] = 0;
        } else {
            $res['registros'] = intval($registros['registros']);
        }

        $res['recibe'] = retornarNombre($con, $res['tipoCatalogo'], $res['nombreRecibe']);

        $consulta_cobrados = "SELECT COUNT(c.idEntrega) as cobrados
        FROM chequesentregados_detalle c 
        INNER JOIN polizacheque p ON p.folioCheque = c.folioCheque
        WHERE c.idEncabezado = :idEncabezado";
        $consulta_cobrados = $con->prepare($consulta_cobrados);
        $consulta_cobrados->bindParam(':idEncabezado', $res['idEncabezado']);
        $consulta_cobrados->execute();
        if ($consulta_cobrados == false) {
            throw new Exception($con->errorInfo());
        }
        $cobrados = $consulta_cobrados->fetch(PDO::FETCH_ASSOC);
        if (!$cobrados) {
            $res['cobrados'] = 0;
        } else {
            $res['cobrados'] = intval($cobrados['cobrados']);
        }

        if ($res['registros'] > 0) {
            if ($res['cobrados'] > 0) {
                $res['puedeEliminar'] = false;
            } else {
                $res['puedeEliminar'] = true;
            }
        } else {
            $res['puedeEliminar'] = true;
        }

        array_push($resultado, $res);
    }

    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
