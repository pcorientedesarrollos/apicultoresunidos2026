<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$json = file_get_contents("php://input");

try {
    $con->beginTransaction();
    if (!$json) {
        throw new Exception('No se recibieron los parámetros');
    } else {
        $datos = json_decode($json);
    }

    if (isset($_GET['edicion'])) {
        $miel = $datos->tipoMiel;
    } else {
        $numeroTambos = sizeof($datos[1]);
        $miel = $datos[0]->tipoMiel;
    }

    switch ($miel) {
        case '1':
            $conformacion = 'conformacionhomogeneo_encabezado';
            $foliosT = 'conformacionhomogeneo_folios';
            $reactivosT = 'conformacionhomogeneo_reactivos';
            break;
        case '2':
            $conformacion = 'conformacionhomogeneo_encabezado_organico';
            $foliosT = 'conformacionhomogeneo_folios_organico';
            $reactivosT = 'conformacionhomogeneo_reactivos_organico';
            break;
    }

    if (isset($_GET['edicion'])) {
        $sql = "UPDATE $conformacion SET resultado = :resultado, personal = :personal WHERE idEncabezado = :idEncabezado";
        $editarEncabezado = $con->prepare($sql);
        $editarEncabezado->bindParam(':resultado', $datos->resultado);
        $editarEncabezado->bindParam(':personal', $datos->personal);
        $editarEncabezado->bindParam(':idEncabezado', $datos->idEncabezado);
        $editarEncabezado->execute();
        if ($editarEncabezado == false) {
            throw new Exception($con->errorInfo());
        }
    } else {
        $sql = "INSERT INTO $conformacion (fecha, tipoAnalisis, numeroTambos, resultado, personal, experimental, busqueda)
        VALUES (:fecha, :tipoAnalisis, :numeroTambos, :resultado, :personal, :experimental, :busqueda)";
        $insertarEncabezado = $con->prepare($sql);
        $insertarEncabezado->bindParam(':fecha', $datos[0]->fecha);
        $insertarEncabezado->bindParam(':tipoAnalisis', $datos[0]->tipoAnalisis);
        $insertarEncabezado->bindParam(':numeroTambos', $numeroTambos);
        $insertarEncabezado->bindParam(':resultado', $datos[0]->resultado);
        $insertarEncabezado->bindParam(':personal', $datos[0]->personal);
        $insertarEncabezado->bindParam(':experimental', $datos[0]->experimental);
        $insertarEncabezado->bindParam(':busqueda', $datos[0]->opcionBusqueda);
        $insertarEncabezado->execute();

        if ($insertarEncabezado == false) {
            throw new Exception($con->errorInfo());
        } else {
            $idSalida = $con->lastInsertid();

            if ($datos[0]->opcionBusqueda == '3') {
                foreach ($datos[1] as $folios) {
                    $sqlSinFolios = "INSERT INTO conformacionhomogeneo_sinfolios (folio, nombre, localidad, tipoMiel, idEncabezado)
                    VALUES (:folio, :nombre, :localidad, :tipoMiel, :idEncabezado)";
                    $insertarSinFolios = $con->prepare($sqlSinFolios);
                    $insertarSinFolios->bindParam(':folio', $folios->folio);
                    $insertarSinFolios->bindParam(':nombre', $folios->nombre);
                    $insertarSinFolios->bindParam(':localidad', $folios->localidad);
                    $insertarSinFolios->bindParam(':tipoMiel', $datos[0]->tipoMiel);
                    $insertarSinFolios->bindParam(':idEncabezado', $idSalida);
                    $insertarSinFolios->execute();
                    if ($insertarSinFolios == false) {
                        throw new Exception($con->errorInfo());
                    }
                }
            } else if ($datos[0]->opcionBusqueda == '4') {
                foreach ($datos[1] as $folios) {
                    $sqlFolios = "INSERT INTO $foliosT (folio, almacen, sobrante, idEncabezado)
                    VALUES (:folio, :almacen, :sobrante, :idEncabezado)";
                    $insertarFolios = $con->prepare($sqlFolios);
                    $insertarFolios->bindParam(':folio', $folios->folio);
                    $insertarFolios->bindParam(':almacen', $folios->almacen);
                    $insertarFolios->bindParam(':sobrante', $folios->sobrante);
                    $insertarFolios->bindParam(':idEncabezado', $idSalida);
                    $insertarFolios->execute();
                    if ($insertarFolios == false) {
                        throw new Exception($con->errorInfo());
                    }
                }
            } else {
                foreach ($datos[1] as $folios) {
                    $sqlFolios = "INSERT INTO $foliosT (folio, almacen, sobrante, idEncabezado)
                    VALUES (:folio, :almacen, :sobrante, :idEncabezado)";
                    $insertarFolios = $con->prepare($sqlFolios);
                    if ($folios->almacen == '1') {
                        $insertarFolios->bindParam(':folio', $folios->folio);
                    } else {
                        $insertarFolios->bindParam(':folio', $folios->consecutivo);
                    }
                    $insertarFolios->bindParam(':almacen', $folios->almacen);
                    $insertarFolios->bindParam(':sobrante', $folios->sobrante);
                    $insertarFolios->bindParam(':idEncabezado', $idSalida);
                    $insertarFolios->execute();
                    if ($insertarFolios == false) {
                        throw new Exception($con->errorInfo());
                    }
                }
            }

            foreach ($datos[2] as $reactivos) {
                $sqlReactivos = "INSERT INTO $reactivosT (reactivo, cantidad, resultado, idEncabezado)
    VALUES (:reactivo, :cantidad, :resultado, :idEncabezado)";
                $insertarReactivos = $con->prepare($sqlReactivos);
                $insertarReactivos->bindParam(':reactivo', $reactivos->reactivo);
                $insertarReactivos->bindParam(':cantidad', $reactivos->cantidad);
                $insertarReactivos->bindParam(':resultado', $reactivos->resultadoReactivo);
                $insertarReactivos->bindParam(':idEncabezado', $idSalida);
                $insertarReactivos->execute();
                if ($insertarReactivos == false) {
                    throw new Exception($con->errorInfo());
                }
            }
        }
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se han guardado los datos', 'swal' => 'success']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine(), 'swal' => 'error']);
    exit();
}
