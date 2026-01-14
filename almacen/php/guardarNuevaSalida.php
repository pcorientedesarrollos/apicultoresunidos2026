<?php

include_once '../../DAOConeccion/conePDO.php';
date_default_timezone_set('America/Merida');
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$postdata = file_get_contents('php://input');

function obtenerNombreTablaAlmacen($idTipoDeMiel)
{
    $almacen_tabla = '';
    switch ($idTipoDeMiel) {
        case '1':
            $almacen_tabla = 'almacen';
            break;
        case '2':
            $almacen_tabla = 'almacen_organico';
            break;
        default:
            throw new Exception('Tipo de miel inválido');
            break;
    }

    return $almacen_tabla;
}

try {
    if (!$postdata) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $salida = json_decode($postdata);
        $hora = date("H:i:s");
    }

    $con->beginTransaction();
    if (isset($salida->idOtraSalida)) {
        $sql = "UPDATE otrassalidas SET fecha = :fecha, tipoDePersona = :tipoDePersona, idPersona = :idPersona, totalPeso = :totalPeso, totalImporte = :totalImporte WHERE idOtraSalida = :idOtraSalida";
        $datos = $con->prepare($sql);
        $datos->bindParam(':fecha', $salida->fecha);
        $datos->bindParam(':tipoDePersona', $salida->tipoDePersona);
        $datos->bindParam(':idPersona', $salida->idPersona);
        $datos->bindParam(':totalPeso', $salida->totalPeso);
        $datos->bindParam(':totalImporte', $salida->totalImporte);
        $datos->bindParam(':idOtraSalida', $salida->idOtraSalida);
        $datos->execute();
        if ($datos == false) {
            throw new Exception($con->errorInfo());
        }

        // Antes de eliminar, escribir el estado que tenian antes

        $sqlSeleccionaAnteriorEstado = $con->prepare("SELECT idTipoDeMiel, folioTambor, estadoTambor
        FROM `otrassalidasdetalle` WHERE idOtrasSalidas = :idOtrasSalidas AND folioTambor IS NOT NULL AND estadoTambor IS NOT NULL;");
        $sqlSeleccionaAnteriorEstado->bindParam(':idOtrasSalidas', $salida->idOtraSalida);
        $sqlSeleccionaAnteriorEstado->execute();
        if ($sqlSeleccionaAnteriorEstado == false) {
            throw new Exception($con->errorInfo());
        } else {
            foreach ($sqlSeleccionaAnteriorEstado->fetchAll(PDO::FETCH_ASSOC) as $tambor) {

                $almacen_tabla = obtenerNombreTablaAlmacen($tambor['idTipoDeMiel']);

                $sqlUpdateAlmacen = $con->prepare("UPDATE $almacen_tabla SET estado = :estado WHERE idAlmacen = :idAlmacen;");
                $sqlUpdateAlmacen->bindParam(':estado', $tambor['estadoTambor']);
                $sqlUpdateAlmacen->bindParam(':idAlmacen', $tambor['folioTambor']);
                $sqlUpdateAlmacen->execute();
                if ($sqlUpdateAlmacen == false) {
                    throw new Exception($con->errorInfo());
                }
            }
        }

        $sqlDelete = $con->prepare("DELETE FROM otrassalidasdetalle WHERE idOtrasSalidas = :idOtrasSalidas");
        $sqlDelete->bindParam(':idOtrasSalidas', $salida->idOtraSalida);
        $sqlDelete->execute();
        if ($sqlDelete == false) {
            throw new Exception($con->errorInfo());
        }

        $idSalida = $salida->idOtraSalida;
        $mensaje = 'Se ha actualizado el registro';
    } else {
        $datos = $con->prepare("INSERT INTO otrassalidas(fecha, tipoDePersona, idPersona, totalPeso, totalImporte) VALUES (:fecha, :tipoDePersona, :idPersona, :totalPeso, :totalImporte);");
        $datos->bindParam(':fecha', $salida->fecha);
        $datos->bindParam(':tipoDePersona', $salida->tipoDePersona);
        $datos->bindParam(':idPersona', $salida->idPersona);
        $datos->bindParam(':totalPeso', $salida->totalPeso);
        $datos->bindParam(':totalImporte', $salida->totalImporte);
        $datos->execute();
        if ($datos == false) {
            throw new Exception($con->errorInfo());
        }
        $idSalida = $con->lastInsertId();
        $mensaje = 'Se ha registrado una nueva salida';
    }

    foreach ($salida->conceptos as $concepto) {
        if (isset($concepto->cajaChica) && $concepto->cajaChica === '1') {
            $concepto->cajaChica = $concepto->cajaChica;
        } else {
            $concepto->cajaChica = '0';
        }
        if (isset($concepto->folioTambor)) {
            // Si en el concepto señalo un folio de tambor, se guarda el estado y su folio
            $almacen_tabla = obtenerNombreTablaAlmacen(($concepto->idTipoDeMiel));

            $sqlObtenerEstado = $con->prepare("SELECT estado FROM $almacen_tabla WHERE idAlmacen = :idAlmacen;");
            $sqlObtenerEstado->bindParam(':idAlmacen', $concepto->folioTambor);
            $sqlObtenerEstado->bindColumn('estado', $estado_tambor);
            $sqlObtenerEstado->execute();
            if ($sqlObtenerEstado == false) {
                throw new Exception($con->errorInfo());
            } else {
                $sqlObtenerEstado->fetch(PDO::FETCH_BOUND);
            }

            // Actualizar el tambor con su nuevo estado de exportación
            $sqlUpdateAlmacen = $con->prepare("UPDATE $almacen_tabla SET estado = 3 WHERE idAlmacen = :idAlmacen;");
            $sqlUpdateAlmacen->bindParam(':idAlmacen', $concepto->folioTambor);
            $sqlUpdateAlmacen->execute();
            if ($sqlUpdateAlmacen == false) {
                throw new Exception($con->errorInfo());
            }

            $sqlInsertDetail = $con->prepare("INSERT INTO otrassalidasdetalle(idTipoDeMiel, idConcepto, kg, importe, cantidad, idSubconceptoCC, folioTambor, estadoTambor, idOtrasSalidas, cajaChica, observaciones)
            VALUES (:idTipoDeMiel, :idConcepto, :kg, :importe, :cantidad, :idSubconceptoCC, :folioTambor, :estadoTambor, :idOtrasSalidas, :cajaChica, :observaciones)");
            $sqlInsertDetail->bindParam(':folioTambor', $concepto->folioTambor);
            $sqlInsertDetail->bindParam(':estadoTambor', $estado_tambor);
        } else {
            $sqlInsertDetail = $con->prepare("INSERT INTO otrassalidasdetalle(idTipoDeMiel, idConcepto, kg, importe, cantidad, idSubconceptoCC, idOtrasSalidas, cajaChica, observaciones)
            VALUES (:idTipoDeMiel, :idConcepto, :kg, :importe, :cantidad, :idSubconceptoCC, :idOtrasSalidas, :cajaChica, :observaciones)");
        }
        $sqlInsertDetail->bindParam(':idTipoDeMiel', $concepto->idTipoDeMiel);
        $sqlInsertDetail->bindParam(':idConcepto', $concepto->idConcepto);
        $sqlInsertDetail->bindParam(':kg', $concepto->kg);
        $sqlInsertDetail->bindParam(':importe', $concepto->importe);
        $sqlInsertDetail->bindParam(':cantidad', $concepto->cantidad);
        $sqlInsertDetail->bindParam(':idSubconceptoCC', $concepto->producto->idSubSubcuenta);
        $sqlInsertDetail->bindParam(':cajaChica', $concepto->cajaChica);
        $sqlInsertDetail->bindParam(':cajaChica', $concepto->cajaChica);
        $sqlInsertDetail->bindParam(':idOtrasSalidas', $idSalida);
        $sqlInsertDetail->bindParam(':observaciones', $concepto->observaciones);
        $sqlInsertDetail->execute();
        if ($sqlInsertDetail == false) {
            throw new Exception($con->errorInfo());
        }
    }


    $con->commit();
    echo json_encode(['error' => false, 'message' => $mensaje]);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine()]);
}
