<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$postdata = file_get_contents('php://input');

try {
    // Hacer una transacción en caso de haber errores
    $con->beginTransaction();

    if (!$postdata || !isset($_GET['eliminarReporte'])) {
        throw new Exception('No se recibió ningún dato.');
    } else {
        $reporte = json_decode($postdata);
        if (!isset($reporte->idTamborPeso)
            || !isset($reporte->idLoteInterno)) {
            throw new Exception('No se recibió todos los parámetros.');
        }
    }

    // Obtener el tipo y tratamiento de miel desde la tabla 'listapesos'
    $sqlSelecciona = $con->prepare("SELECT tipoMiel, mielHomogeneizada
    FROM listadepesos WHERE idTamborPeso = :idTamborPeso");
    $sqlSelecciona->bindParam(':idTamborPeso', $reporte->idTamborPeso);
    $sqlSelecciona->bindColumn('tipoMiel', $reporte->tipoMiel);
    $sqlSelecciona->bindColumn('mielHomogeneizada', $reporte->mielHomogeneizada);
    $sqlSelecciona->execute();
    if ($sqlSelecciona == false) {
        throw new Exception($con->errorInfo());
    } else {
        $sqlSelecciona->fetch(PDO::FETCH_BOUND);
    }

    // Verificar que existan y sean válidas las propiedades
    if (!isset($reporte->mielHomogeneizada) && !$reporte->mielHomogeneizada) {
        throw new Exception('Falta el tipo de tratamiento de la miel');
    }

    if (!isset($reporte->tipoMiel) && !$reporte->tipoMiel) {
        throw new Exception('Falta el tipo de miel');
    }

    // Cambiar el estado de los tambores a 2 (lote interno)

    // Revisa el tipo de miel para saber a que tabla modificar
    switch ($reporte->tipoMiel) {
        case '1':
            $almacen_tabla = 'almacen';
            $tamboreslotes_tabla = 'tamboreslotes';
            break;
        case '2':
            $almacen_tabla = 'almacen_organico';
            $tamboreslotes_tabla = 'tamboreslotes_organico';
            break;
        default:
            throw new Exception('El tipo de miel no es válido');
            break;
    }

    $folios = array(); //Guardar los folios en este arreglo con formato: folioTambor-tipo-clasificacion

    switch ($reporte->mielHomogeneizada) {
        case '1':
            // Si el tratamiento es homogeneizado, selecciona los folios de la tabla tamboreslotes

            $sqlSelect = $con->prepare("SELECT CONCAT(folioTambor,'-',tipo, '-', clasificacion) as folioTambor FROM $tamboreslotes_tabla WHERE idLoteInterno = :idLoteInterno");
            $sqlSelect->bindParam(':idLoteInterno', $reporte->idLoteInterno);
            $sqlSelect->execute();
            if ($sqlSelect == false) {
                throw new Exception($con->errorInfo());
            }
            foreach ($sqlSelect->fetchAll(PDO::FETCH_ASSOC) as $tambor) {
                array_push($folios, $tambor['folioTambor']);
            }
            break;
        case '2':
            // Si es miel no homogeneizada, tomar los folios de la tabla tamboreslistapesos
            $sqlSelect = $con->prepare("SELECT CONCAT(folio,'-',tipo, '-', clasificacion) as folio FROM tamboreslistapesos WHERE idTamborPeso = :idTamborPeso;");
            $sqlSelect->bindParam(':idTamborPeso', $reporte->idTamborPeso);
            $sqlSelect->execute();
            if ($sqlSelect == false) {
                throw new Exception($con->errorInfo());
            }
            foreach ($sqlSelect->fetchAll(PDO::FETCH_ASSOC) as $tambor) {
                array_push($folios, $tambor['folio']);
            }
            break;
        default:
            throw new Exception('El tipo de tratamiento no es válido');
            break;
    }

    // Cambiar el estado de los tambores que están en el arreglo $folios

    foreach ($folios as $folio) {

        $folio = explode('-', $folio);

        // $folio[0] -> idAlmacen
        // $folio[1] -> sobrante
        // $folio[2] -> clasificacion, en caso de ser sobrante

        if ($folio[1] == '1' && $folio[2] != '0') {
            $sqlUpdate = $con->prepare("UPDATE almacensobrantes SET estado = 2 WHERE consecutivo = :idAlmacen AND tipoDeMiel = :tipoDeMiel AND sobrante = :clasificacion");
            $sqlUpdate->bindParam(':idAlmacen', $folio[0]);
            $sqlUpdate->bindParam(':tipoDeMiel', $reporte->tipoMiel);
            $sqlUpdate->bindParam(':clasificacion', $folio[2]);
            $sqlUpdate->execute();
            if ($sqlUpdate == false) {
                throw new Exception($con->errorInfo());
            }
        } else {
            $sqlUpdate = $con->prepare("UPDATE $almacen_tabla SET estado = 2 WHERE idAlmacen = :idAlmacen");
            $sqlUpdate->bindParam(':idAlmacen', $folio[0]);
            $sqlUpdate->execute();
            if ($sqlUpdate == false) {
                throw new Exception($con->errorInfo());
            }
        }

    }

    
    // Eliminar de la tabla 'listadepesos'
    $sqlEliminar = $con->prepare("DELETE FROM listadepesos WHERE idTamborPeso = :idTamborPeso");
    $sqlEliminar->bindParam(':idTamborPeso', $reporte->idTamborPeso);
    $sqlEliminar->execute();
    if ($sqlEliminar == false) {
        throw new Exception($con->errorInfo());
    }

    // Eliminar de la tabla 'tamboreslistapesos'
    $sqlEliminar2 = $con->prepare("DELETE FROM tamboreslistapesos WHERE idTamborPeso = :idTamborPeso");
    $sqlEliminar2->bindParam(':idTamborPeso', $reporte->idTamborPeso);
    $sqlEliminar2->execute();
    if ($sqlEliminar2 == false) {
        throw new Exception($con->errorInfo());
    }

    $con->commit(); // Cerramos la transacción para que haga los cambios
    echo json_encode(['error' => false, 'message' => 'Se ha eliminado el registro y los tambores están disponibles en el lote interno.']);
} catch (Exception $e) {
    // Hacemos un Rollback a la transacción
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}