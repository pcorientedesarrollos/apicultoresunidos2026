<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$postdata = file_get_contents('php://input');

try {
    $con->beginTransaction();

    if (!$postdata) {
        throw new Exception('No se recibieron datos');
    } else {
        $datosGuardar = json_decode($postdata);
        switch ($datosGuardar->tipoDeMiel) {
            case '1':
                $encabezado = 'certificadoloteterminado_encabezado';
                $detalle = 'certificadoloteterminado_detalle';
                break;
            case '2':
                $encabezado = 'certificadoloteterminado_encabezado_organico';
                $detalle = 'certificadoloteterminado_detalle_organico';
                break;
            case '5':
                $encabezado = 'certificadoloteterminado_encabezado_mantequilla';
                $detalle = 'certificadoloteterminado_detalle_mantequilla';
                break;
            case '6':
                $encabezado = 'certificadoloteterminado_encabezado_altiplano';
                $detalle = 'certificadoloteterminado_detalle_altiplano';
                break;
            case '7':
                $encabezado = 'certificadoloteterminado_encabezado_naranjo';
                $detalle = 'certificadoloteterminado_detalle_naranjo';
                break;
            case '8':
                $encabezado = 'certificadoloteterminado_encabezado_aguacate';
                $detalle = 'certificadoloteterminado_detalle_aguacate';
                break;
            case '9':
                $encabezado = 'certificadoloteterminado_encabezado_mezquite';
                $detalle = 'certificadoloteterminado_detalle_mezquite';
                break;
        }
    }

    // iniciamos una transaccion

    $sqlEncabezado = "UPDATE $encabezado SET fechaCalidad = :fechaCalidad, tipoDeCliente = :tipoDeCliente, cliente = :cliente, loteCalidad = :loteCalidad, cantidad = :cantidad, fechaCaducidad = :fechaCaducidad, factura = :factura, sello = :sello, chofer = :chofer WHERE idLote = :idLoteInterno";
    $queryInsertaEncabezado = $con->prepare($sqlEncabezado);
    $queryInsertaEncabezado->bindParam(':fechaCalidad', $datosGuardar->fechaCalidad);
    $queryInsertaEncabezado->bindParam(':tipoDeCliente', $datosGuardar->tipoDeCliente);
    $queryInsertaEncabezado->bindParam(':cliente', $datosGuardar->cliente);
    $queryInsertaEncabezado->bindParam(':loteCalidad', $datosGuardar->loteCalidad);
    $queryInsertaEncabezado->bindParam(':cantidad', $datosGuardar->cantidad);
    $queryInsertaEncabezado->bindParam(':fechaCaducidad', $datosGuardar->fechaCaducidad);
    $queryInsertaEncabezado->bindParam(':factura', $datosGuardar->factura);
    $queryInsertaEncabezado->bindParam(':sello', $datosGuardar->sello);
    $queryInsertaEncabezado->bindParam(':chofer', $datosGuardar->chofer);
    $queryInsertaEncabezado->bindParam(':idLoteInterno', $datosGuardar->idLote);
    $queryInsertaEncabezado->execute();
    if ($queryInsertaEncabezado == false) {
        throw new Exception($con->errorInfo());
    }


    // Si llega a este punto, es que no hubo algun error
    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha guardado un nuevo registro']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit();
}







