<?php
include_once "../../DAOConeccion/conePDO.php";
include_once '../../controlAdministrativo/php/nombreDePersona.php';

$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = new stdClass();
try {
    if (!isset($_GET['idEncabezado'])) {
        throw new Exception('Parámetro no especificado.');
    } else {
        $idEncabezado = $_GET['idEncabezado'];
    }

    $consultaEncabezado = "SELECT * FROM chequesentregados_encabezado WHERE idEncabezado = :idEncabezado";
    $consultaEncabezado = $con->prepare($consultaEncabezado);
    $consultaEncabezado->bindParam(':idEncabezado', $idEncabezado);
    $consultaEncabezado->execute();
    if ($consultaEncabezado == false) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado = $consultaEncabezado->fetch(PDO::FETCH_ASSOC);
    }
    $cobrados = 0;
    // $consulta = "SELECT ch.idEntrega, che.idEncabezado, che.fechaEntrega, che.nombreRecibe, ch.folioCheque, ch.tipoDePersona, ch.idPersona, ch.importe,
    // pc.fecha AS fechaCobro
    // FROM chequesentregados_detalle ch
	// LEFT JOIN chequesentregados_encabezado che ON che.idEncabezado = ch.idEncabezado
    // LEFT JOIN polizacheque pc ON pc.folioCheque = ch.folioCheque
    // WHERE ch.idEncabezado = :idEncabezado";
    $consulta = "SELECT ch.idEntrega, che.idEncabezado, che.fechaEntrega, che.nombreRecibe, ch.folioCheque, pc.persona2 AS beneficiario, pc.cantidad AS importe
    ,pc.fecha AS fechaCobro
    FROM chequesentregados_detalle ch
	LEFT JOIN chequesentregados_encabezado che ON che.idEncabezado = ch.idEncabezado
    LEFT JOIN polizacheque pc ON pc.folioCheque = ch.folioCheque
    WHERE ch.idEncabezado = :idEncabezado";
    $consulta = $con->prepare($consulta);
    $consulta->bindParam(':idEncabezado', $idEncabezado);
    $consulta->execute();
    if ($consulta == false) {
        throw new Exception($con->errorInfo());
    }

    $array = array();
    foreach ($consulta->fetchAll(PDO::FETCH_ASSOC) as $data) {
        if ($data['fechaCobro']) {
            $cobrados++;
        }
        // $data['nombre'] = retornarNombre($con, $data['tipoDePersona'], $data['idPersona']);
        array_push($array, $data);
    }

    if ($array > 0) {
        if ($cobrados > 0) {
            $resultado['puedeEditar'] = false;
        } else {
            $resultado['puedeEditar'] = true;
        }
    } else {
        $resultado['puedeEditar'] = true;
    }

    $resultado['listaDetalle'] = $array;
    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
