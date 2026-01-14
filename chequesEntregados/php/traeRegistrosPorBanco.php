<?php
include_once "../../DAOConeccion/conePDO.php";
include_once '../../controlAdministrativo/php/nombreDePersona.php';

$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// $resultado = new stdClass();
$resultado = [
    'girados' => 0,
    'cobrados' => 0,
    'chequesEntregados' => []
];
try {
    if (!isset($_GET['idBanco'])) {
        throw new Exception('Parámetro no especificado.');
    } else {
        $idBanco = $_GET['idBanco'];
    }

    $contarGirados = "SELECT COUNT(c.idEntrega) AS girados
    FROM chequesentregados_detalle c
    LEFT JOIN chequesentregados_encabezado e ON e.idEncabezado = c.idEncabezado
    WHERE e.idBanco = :idBanco";
    $contarGirados = $con->prepare($contarGirados);
    $contarGirados->bindParam(':idBanco', $idBanco);
    $contarGirados->execute();
    if ($contarGirados == false) {
        throw new Exception($con->errorInfo());
    }
    $chequesGirados = $contarGirados->fetch(PDO::FETCH_ASSOC);
    if (!$chequesGirados) {
        $resultado['girados'] = 0;
    } else {
        $resultado['girados'] = intval($chequesGirados['girados']);
    }

    $contarCobrados = "SELECT COUNT(c.idEntrega) as cobrados
    FROM chequesentregados_detalle c 
    INNER JOIN polizacheque p ON p.folioCheque = c.folioCheque
	LEFT JOIN chequesentregados_encabezado e ON e.idEncabezado = c.idEncabezado
    WHERE e.idBanco = :idBanco";
    $contarCobrados = $con->prepare($contarCobrados);
    $contarCobrados->bindParam(':idBanco', $idBanco);
    $contarCobrados->execute();
    if ($contarCobrados == false) {
        throw new Exception($con->errorInfo());
    }
    $cobrados = $contarCobrados->fetch(PDO::FETCH_ASSOC);
    if (!$cobrados) {
        $resultado['cobrados'] = 0;
    } else {
        $resultado['cobrados'] = intval($cobrados['cobrados']);
    }

    // $consulta = "SELECT ch.idEncabezado, chd.idEntrega, ch.fechaEntrega, ch.tipoCatalogo, ch.nombreRecibe, 
    // chd.folioCheque, chd.tipoDePersona, chd.idPersona, chd.importe,
    // pc.fecha AS fechaCobro, b.banco
    // FROM chequesentregados_encabezado ch
    // LEFT JOIN chequesentregados_detalle chd ON chd.idEncabezado = ch.idEncabezado
    // LEFT JOIN polizacheque pc ON pc.folioCheque = chd.folioCheque
    // LEFT JOIN bancos b ON b.idBanco = ch.idBanco
    // WHERE ch.idBanco = :idBanco";
    $consulta = "SELECT ch.idEncabezado, chd.idEntrega, ch.fechaEntrega, ch.tipoCatalogo, ch.nombreRecibe, 
    chd.folioCheque, pc.persona2 AS beneficiario, pc.cantidad AS importe,
    pc.fecha AS fechaCobro, b.banco
    FROM chequesentregados_encabezado ch
    LEFT JOIN chequesentregados_detalle chd ON chd.idEncabezado = ch.idEncabezado
    LEFT JOIN polizacheque pc ON pc.folioCheque = chd.folioCheque
    LEFT JOIN bancos b ON b.idBanco = ch.idBanco
    WHERE ch.idBanco = :idBanco";
    $consulta = $con->prepare($consulta);
    $consulta->bindParam(':idBanco', $idBanco);
    $consulta->execute();
    if ($consulta == false) {
        throw new Exception($con->errorInfo());
    } else {
        $array = array();
        foreach ($consulta->fetchAll(PDO::FETCH_ASSOC) as $data) {
            $data['recibe'] = retornarNombre($con, $data['tipoCatalogo'], $data['nombreRecibe']);
            // $data['nombre'] = retornarNombre($con, $data['tipoDePersona'], $data['idPersona']);
            array_push($array, $data);
        }
        // $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $resultado['chequesEntregados'] = $array;
        echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
