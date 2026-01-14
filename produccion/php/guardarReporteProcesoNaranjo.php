<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$json = file_get_contents("php://input");
$fecha = date("Y-m-d");
$idReporteProceso = 0;

// Guardar el reporte de proceso de miel orgánica
// Alejandro Medina
// Mayo 1, 2018

try {
    $con->beginTransaction();
    if (!$json) {
        throw new Exception('No se recibieron datos');
    } else {
        $info = json_decode($json);
    }


    $sqlProceso = $con->prepare("INSERT INTO reportesdeprocesos_naranjo (fechaReporte, idLoteInterno,
    responsable, supervisor, horaInicio, numeroTanque, cabello, cofia, botas, unas,
    cubreBoca, sanitizacionManos, ropa, lavadoManos, sanitizacionBotas ,producto,
    inicioHomogeneizado, finalHomogeneizado, tiempoReposo, observaciones, horaFinal,
    lavadoHerramientas, horaFinalReposo, tiempoHomogeneizacion, otrasHerramientas,
    noProcesada, procesada, observacionesDePesos) VALUES (:fecha, :idLoteInterno,
    :responsable, :supervisor, :horaInicio, :numeroTanque,  :cabello,  :cofia, 
    :botas,  :unas,  :cubreBoca,  :sanitizacionManos,  :ropa,  :lavadoManos,
    :sanitizacionBotas, :producto, :inicioHomogeneizado, :finalHomogeneizado, :tiempoReposo,
    :observaciones, :horaFinal,  :lavadoHerramientas, :horaFinalReposo, :tiempoHomogeneizacion,
    :otrasHerramientas, :noProcesada, :procesada, :observacionesDePesos)");

    $sqlProceso->bindParam(':fecha', $fecha);
    $sqlProceso->bindParam(':idLoteInterno', $info[0]->idLoteInterno);
    $sqlProceso->bindParam(':responsable', $info[0]->responsable);
    $sqlProceso->bindParam(':supervisor', $info[0]->supervisor);
    $sqlProceso->bindParam(':horaInicio', $info[0]->horaInicio);
    $sqlProceso->bindParam(':numeroTanque', $info[0]->numeroTanque);
    $sqlProceso->bindParam(':cabello', $info[0]->cabello);
    $sqlProceso->bindParam(':cofia', $info[0]->cofia);
    $sqlProceso->bindParam(':botas', $info[0]->botas);
    $sqlProceso->bindParam(':unas', $info[0]->unas);
    $sqlProceso->bindParam(':cubreBoca', $info[0]->cubreBoca);
    $sqlProceso->bindParam(':sanitizacionManos', $info[0]->sanitizacionManos);
    $sqlProceso->bindParam(':ropa', $info[0]->ropa);
    $sqlProceso->bindParam(':lavadoManos', $info[0]->lavadoManos);
    $sqlProceso->bindParam(':sanitizacionBotas', $info[0]->sanitizacionBotas);
    $sqlProceso->bindParam(':producto', $info[0]->producto);
    $sqlProceso->bindParam(':inicioHomogeneizado', $info[0]->inicioHomogeneizado);
    $sqlProceso->bindParam(':finalHomogeneizado', $info[0]->finalHomogeneizado);
    $sqlProceso->bindParam(':tiempoReposo', $info[0]->tiempoReposo);
    $sqlProceso->bindParam(':observaciones', $info[0]->observaciones);
    $sqlProceso->bindParam(':horaFinal', $info[0]->horaFinal);
    $sqlProceso->bindParam(':lavadoHerramientas', $info[0]->lavadoHerramientas);
    $sqlProceso->bindParam(':horaFinalReposo', $info[0]->horaFinalReposo);
    $sqlProceso->bindParam(':tiempoHomogeneizacion', $info[0]->tiempoHomogeneizacion);
    $sqlProceso->bindParam(':otrasHerramientas', $info[0]->otrasHerramientas);
    $sqlProceso->bindParam(':noProcesada', $info[0]->noProcesada);
    $sqlProceso->bindParam(':procesada', $info[0]->procesada);
    $sqlProceso->bindParam(':observacionesDePesos', $info[0]->observacionesDePesos);

    $sqlProceso->execute();
    if ($sqlProceso == false) {
        throw new Exception($con->errorInfo());
    }

    $idReporteProceso = $con->lastInsertId();

    foreach ($info[1] as $personalConformacion) {
        $sqlPersonalConformacion = $con->prepare("INSERT INTO conformaciondelotes_naranjo
        (idPersonalOM, idReporteProceso) VALUES (:idPersonalOM, :idReporteProceso)");
        $sqlPersonalConformacion->bindParam(':idPersonalOM', $personalConformacion);
        $sqlPersonalConformacion->bindParam(':idReporteProceso', $idReporteProceso);
        $sqlPersonalConformacion->execute();

        if ($sqlPersonalConformacion == false) {
            throw new Exception($con->errorInfo());
        }
    }

    foreach ($info[2] as $personalProceso) {
        $sqlPersonalProceso = $con->prepare("INSERT INTO procesozonainocua_naranjo
        (idPersonalOM, idReporteProceso) VALUES (:idPersonalOM, :idReporteProceso)");

        $sqlPersonalProceso->bindParam(':idPersonalOM', $personalProceso);
        $sqlPersonalProceso->bindParam(':idReporteProceso', $idReporteProceso);
        $sqlPersonalProceso->execute();

        if ($sqlPersonalProceso == false) {
            throw new Exception($con->errorInfo());
        }
    }

    foreach ($info[3] as $personalFolios) {
        $sqlPersonalFolios = $con->prepare("INSERT INTO busquedadefolios_naranjo
        (idPersonalOM, idReporteProceso) VALUES (:idPersonalOM, :idReporteProceso)");
        $sqlPersonalFolios->bindParam(':idPersonalOM', $personalFolios);
        $sqlPersonalFolios->bindParam(':idReporteProceso', $idReporteProceso);
        $sqlPersonalFolios->execute();
        if ($sqlPersonalFolios == false) {
            throw new Exception($con->errorInfo());
        }
    }

    $sqlHerramienta = $con->prepare("INSERT INTO herramientas_naranjo (herramientaLlave, cantidadLlave,
    condicionLlave, herramientaPala, cantidadPala, condicionPala, herramientaOtras, cantidadOtras,
    condicionOtras, idReporteProceso) VALUES ('Llave', :cantidadLlave, :condicionLlave, 'Pala',
    :cantidadPala, :condicionPala, 'Otras', :cantidadOtras, :condicionOtras, :idReporteProceso )");

    $sqlHerramienta->bindParam(':cantidadLlave', $info[4]->cantidadLlave);
    $sqlHerramienta->bindParam(':condicionLlave', $info[4]->condicionLlave);
    $sqlHerramienta->bindParam(':cantidadPala', $info[4]->cantidadPala);
    $sqlHerramienta->bindParam(':condicionPala', $info[4]->condicionPala);
    $sqlHerramienta->bindParam(':cantidadOtras', $info[4]->cantidadOtras);
    $sqlHerramienta->bindParam(':condicionOtras', $info[4]->condicionOtras);
    $sqlHerramienta->bindParam(':idReporteProceso', $idReporteProceso);

    $sqlHerramienta->execute();

    if ($sqlHerramienta == false) {
        throw new Exception($con->errorInfo());
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha guardado el reporte']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
