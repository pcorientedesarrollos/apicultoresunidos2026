<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$json = file_get_contents("php://input");
$idReporteProceso = 0;

try {

    if (!$json) {
        throw new Exception('No se recibieron datos');
    } else {
        $info = json_decode($json);
    }

    $tabla = isset($_GET['organica']) ? 'reportesdeprocesos_organico' : 'reportesdeprocesos';
    
    if(isset($_GET["organica"])){
        switch ($_GET['organica']) {
            case 0:
                $tabla = 'reportesdeprocesos_organico';
                break;
            case 1:
                $tabla = 'reportesdeprocesos_mantequilla';
                break;
            case 2:
                $tabla = 'reportesdeprocesos_altiplano';
                break;
            case 3:
                $tabla = 'reportesdeprocesos_naranjo';
                break;
            case 4:
                $tabla = 'reportesdeprocesos_aguacate';
                break;
            case 5:
                $tabla = 'reportesdeprocesos_mezquite';
                break;
        }}

    $sqlProceso = "UPDATE $tabla SET idLoteInterno = :idLoteInterno, responsable = :responsable, supervisor = :supervisor, horaInicio = :horaInicio, numeroTanque = :numeroTanque, cabello = :cabello, cofia = :cofia, botas = :botas, unas = :unas, cubreBoca = :cubreBoca, sanitizacionManos = :sanitizacionManos, ropa = :ropa, lavadoManos = :lavadoManos, sanitizacionBotas = :sanitizacionBotas, producto = :producto, inicioHomogeneizado = :inicioHomogeneizado, finalHomogeneizado = :finalHomogeneizado, tiempoReposo = :tiempoReposo, observaciones = :observaciones, horaFinal = :horaFinal, lavadoHerramientas = :lavadoHerramientas, horaFinalReposo = :horaFinalReposo, tiempoHomogeneizacion = :tiempoHomogeneizacion, otrasHerramientas = :otrasHerramientas, noProcesada= :noProcesada, procesada = :procesada, observacionesDePesos = :observacionesDePesos WHERE idReporteProceso = :idReporteProceso";
    $dato = $con->prepare($sqlProceso);
    $dato->bindParam(':idLoteInterno', $info->idLoteInterno);
    $dato->bindParam(':responsable', $info->responsable);
    $dato->bindParam(':supervisor', $info->supervisor);
    $dato->bindParam(':horaInicio', $info->horaInicio);
    $dato->bindParam(':numeroTanque', $info->numeroTanque);
    $dato->bindParam(':cabello', $info->cabello);
    $dato->bindParam(':cofia', $info->cofia);
    $dato->bindParam(':botas', $info->botas);
    $dato->bindParam(':unas', $info->unas);
    $dato->bindParam(':cubreBoca', $info->cubreBoca);
    $dato->bindParam(':sanitizacionManos', $info->sanitizacionManos);
    $dato->bindParam(':ropa', $info->ropa);
    $dato->bindParam(':lavadoManos', $info->lavadoManos);
    $dato->bindParam(':sanitizacionBotas', $info->sanitizacionBotas);
    $dato->bindParam(':producto', $info->producto);
    $dato->bindParam(':inicioHomogeneizado', $info->inicioHomogeneizado);
    $dato->bindParam(':finalHomogeneizado', $info->finalHomogeneizado);
    $dato->bindParam(':tiempoReposo', $info->tiempoReposo);
    $dato->bindParam(':observaciones', $info->observaciones);
    $dato->bindParam(':horaFinal', $info->horaFinal);
    $dato->bindParam(':lavadoHerramientas', $info->lavadoHerramientas);
    $dato->bindParam(':horaFinalReposo', $info->horaFinalReposo);
    $dato->bindParam(':tiempoHomogeneizacion', $info->tiempoHomogeneizacion);
    $dato->bindParam(':otrasHerramientas', $info->otrasHerramientas);
    $dato->bindParam(':noProcesada', $info->noProcesada);
    $dato->bindParam(':procesada', $info->procesada);
    $dato->bindValue(':observacionesDePesos', $info->observacionesDePesos);
    $dato->bindParam(':idReporteProceso', $info->idReporteProceso);
    $dato->execute();

    if ($dato == false) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'message' => 'Se ha guardado los cambios']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
