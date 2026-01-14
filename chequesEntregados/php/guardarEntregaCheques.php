<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$miInfo = file_get_contents("php://input");


try {
    if (!$miInfo) {
        throw new Exception('No se recibieron datos');
    } else {
        $datos = json_decode($miInfo);
    }

    $con->beginTransaction();

    if (isset($_GET['idEncabezado'])) {
        $sqlAgregar = "INSERT INTO chequesentregados_detalle (idEncabezado, folioCheque, tipoDePersona,
        idPersona, importe)
        VALUES (:idEncabezado, :folioCheque, :tipoDePersona, :idPersona, :importe)";
        $insertDetalle = $con->prepare($sqlAgregar);
        $insertDetalle->bindParam(':idEncabezado', $_GET['idEncabezado']);
        $insertDetalle->bindParam(':folioCheque', $datos->folioCheque);
        $insertDetalle->bindParam(':tipoDePersona', $datos->tipoDePersona);
        $insertDetalle->bindParam(':idPersona', $datos->idPersona);
        $insertDetalle->bindParam(':importe', $datos->importe);
        $insertDetalle->execute();
        if ($insertDetalle == false) {
            throw new Exception($con->errorInfo());
        }
        echo json_encode(['error' => false, 'message' => 'Registro guardado']);
        $con->commit();
    } else {
        $sqlEncabezado = "INSERT chequesentregados_encabezado (fechaEntrega, idBanco, tipoCatalogo, nombreRecibe) 
        VALUES (:fechaEntrega, :idBanco, :tipoCatalogo, :nombreRecibe)";
        $insertEncabezado = $con->prepare($sqlEncabezado);
        $insertEncabezado->bindParam(':fechaEntrega', $datos[0]->fechaEntrega);
        $insertEncabezado->bindParam(':idBanco', $datos[0]->idBanco);
        $insertEncabezado->bindParam(':tipoCatalogo', $datos[0]->tipoCatalogo);
        $insertEncabezado->bindParam(':nombreRecibe', $datos[0]->nombreRecibe);
        $insertEncabezado->execute();

        if ($insertEncabezado->rowCount() == 1) {
            $idEncabezado = $con->lastInsertId();

            foreach ($datos[1] as $i) {
                $sqlDetalle = "INSERT INTO chequesentregados_detalle (idEncabezado, folioCheque)
                VALUES (:idEncabezado, :folioCheque)";
                $insertDetalle = $con->prepare($sqlDetalle);
                $insertDetalle->bindParam(':idEncabezado', $idEncabezado);
                $insertDetalle->bindParam(':folioCheque', $i->folioCheque);
                // $insertDetalle->bindParam(':tipoDePersona', $i->tipoDePersona);
                // $insertDetalle->bindParam(':idPersona', $i->idPersona);
                // $insertDetalle->bindParam(':importe', $i->importe);
                $insertDetalle->execute();
                if ($insertDetalle == false) {
                    throw new Exception($con->errorInfo());
                }
            }

            // echo json_encode(['error' => false, 'message' => 'Registro guardado']);
            $con->commit();
            $_url = 'reportes/compras/pdfEntregaDeCheques.php?idEncabezado=' . $idEncabezado;
            echo json_encode(['error' => false, 'message' => 'Registro guardado', 'url' => $_url]);
        }
    }
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
