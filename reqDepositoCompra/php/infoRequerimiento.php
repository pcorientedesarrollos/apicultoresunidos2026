<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Parámetro no recibido.');
    } else {
        $id = $_GET['id'];
    }

    $query = "SELECT idRequisicion, fechaRequisicion, fechaImpresion, importeTotal, totalTambores 
    FROM requisicionencabezado WHERE idRequisicion = :id ";
    $data = $con->prepare($query);
    $data->bindParam(':id', $id);
    $data->execute();
    if (!$data) {
        throw new Exception($con->errorInfo());
    }

    $reqEncabezado = array();
    $reqEncabezado = $data->fetch(PDO::FETCH_ASSOC);
    $reqEncabezado['requerimientosDetalle'] = array();
    $cobrados = 0;
    $sqlReqDetalle = "SELECT rd.idDetalle, p.nombre,  l.localidad, rd.idTipoDeMiel,
    rd.noTambores,rd.peso, rd.precio, rd.importe, rd.banco, rd.observaciones,
    rd.cobrado, c.nombre as nombreComprador, tm.tipoDeMiel, rd.saldoActual AS saldoDeudor
    FROM requisiciondetalle rd
    LEFT JOIN requisicionencabezado re ON rd.idRequisicion = re.idRequisicion
    LEFT JOIN compradores c ON c.idcomprador = rd.idComprador
    LEFT JOIN proveedor p ON p.idProveedor = rd.idProveedor
    LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
    LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
    LEFT JOIN tiposdemiel tm ON tm.idTipoDeMiel = rd.idTipoDeMiel
    WHERE re.idRequisicion = :id ORDER BY rd.idDetalle";
    $datosDetalle = $con->prepare($sqlReqDetalle);
    $datosDetalle->bindParam(':id', $id);
    $datosDetalle->execute();
    if (!$datosDetalle) {
        throw new Exception($con->errorInfo());
    }

    $reqEncabezado['requerimientosDetalle'] = $datosDetalle->fetchAll(PDO::FETCH_ASSOC);

    foreach ($reqEncabezado['requerimientosDetalle'] as $reqDetalle) {
        if ($reqDetalle['cobrado'] == '1') {
            $cobrados++;
        }
    }

    if (count($reqEncabezado['requerimientosDetalle']) > 0) {
        if ($cobrados > 0) {
            $reqEncabezado['puedeEditar'] = false;
        } else {
            $reqEncabezado['puedeEditar'] = true;
        }
    } else {
        $reqEncabezado['puedeEditar'] = true;
    }

    $sqlTotales = "SELECT SUM(importe) as sumaTotal,
        (SELECT SUM(noTambores)FROM requisiciondetalle WHERE idRequisicion = :id) as sumaTambores,
        (SELECT SUM(peso) FROM requisiciondetalle WHERE idRequisicion =  :id) as sumaKilos
        FROM requisiciondetalle WHERE idRequisicion = :id ";
    $datosTotales = $con->prepare($sqlTotales);
    $datosTotales->bindParam(':id', $id);
    $datosTotales->execute();
    if (!$datosTotales) {
        throw new Exception($con->errorInfo());
    }

    $resultadoTotales = $datosTotales->fetch(PDO::FETCH_ASSOC);
    $reqEncabezado['importeTotal'] = $resultadoTotales["sumaTotal"];
    $reqEncabezado['totalTambores'] = $resultadoTotales["sumaTambores"];
    $reqEncabezado['totalKilos'] = $resultadoTotales["sumaKilos"];

    echo json_encode(['error' => false, 'data' => $reqEncabezado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
