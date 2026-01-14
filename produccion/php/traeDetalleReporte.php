<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idReporteProceso = $_GET["idReporteProceso"];

if(isset($_GET["organica"])){
    switch ($_GET['organica']) {
        case 0:
            $reporte_procesos = 'reportesdeprocesos_organico';
            $herramientas = 'herramientas_organico';
            $conformacion = 'conformaciondelotes_organico';
            $procesoinocuo = 'procesozonainocua_organico';
            $busqueda = 'busquedadefolios_organico';
            break;
        case 1:
            $reporte_procesos = 'reportesdeprocesos_mantequilla';
            $herramientas = 'herramientas_mantequilla';
            $conformacion = 'conformaciondelotes_mantequilla';
            $procesoinocuo = 'procesozonainocua_mantequilla';
            $busqueda = 'busquedadefolios_mantequilla';
            break;
        case 2:
            $reporte_procesos = 'reportesdeprocesos_altiplano';
            $herramientas = 'herramientas_altiplano';
            $conformacion = 'conformaciondelotes_altiplano';
            $procesoinocuo = 'procesozonainocua_altiplano';
            $busqueda = 'busquedadefolios_altiplano';
            break;
        case 3:
            $reporte_procesos = 'reportesdeprocesos_altiplano';
            $herramientas = 'herramientas_naranjo';
            $conformacion = 'conformaciondelotes_naranjo';
            $procesoinocuo = 'procesozonainocua_naranjo';
            $busqueda = 'busquedadefolios_naranjo';
            break;
        case 4:
            $reporte_procesos = 'reportesdeprocesos_aguacate';
            $herramientas = 'herramientas_aguacate';
            $conformacion = 'conformaciondelotes_aguacate';
            $procesoinocuo = 'procesozonainocua_aguacate';
            $busqueda = 'busquedadefolios_aguacate';
            break;
        case 5:
            $reporte_procesos = 'reportesdeprocesos_mezquite';
            $herramientas = 'herramientas_mezquite';
            $conformacion = 'conformaciondelotes_mezquite';
            $procesoinocuo = 'procesozonainocua_mezquite';
            $busqueda = 'busquedadefolios_mezquite';
            break;
    }
    $sql = "SELECT r.*, 
    h.cantidadLlave, h.condicionLlave, h.cantidadOtras, h.condicionOtras, h.cantidadPala, h.condicionPala
     FROM $reporte_procesos r
    LEFT JOIN $herramientas h
    ON h.idReporteProceso = r.idReporteProceso
    WHERE r.idReporteProceso = :idReporteProceso";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idReporteProceso', $idReporteProceso);
    $datos->execute();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $reporteProceso = new stdClass();
            $reporteProceso->idReporteProceso = $rs["idReporteProceso"];
            $reporteProceso->fechaReporte = $rs["fechaReporte"];
            $reporteProceso->idLoteInterno = $rs["idLoteInterno"];
            $reporteProceso->responsable = $rs["responsable"];
            $reporteProceso->supervisor = $rs["supervisor"];
            $reporteProceso->horaInicio = $rs["horaInicio"];
            $reporteProceso->numeroTanque = $rs["numeroTanque"];
    
            $reporteProceso->cabello = $rs["cabello"];
            $reporteProceso->cofia = $rs["cofia"];
            $reporteProceso->botas = $rs["botas"];
            $reporteProceso->unas = $rs["unas"];
            $reporteProceso->cubreBoca = $rs["cubreBoca"];
            $reporteProceso->sanitizacionManos = $rs["sanitizacionManos"];
            $reporteProceso->ropa = $rs["ropa"];
            $reporteProceso->lavadoManos = $rs["lavadoManos"];
            $reporteProceso->sanitizacionBotas = $rs["sanitizacionBotas"];
            $reporteProceso->lavadoHerramientas = $rs["lavadoHerramientas"];
            $reporteProceso->horaFinalReposo = $rs["horaFinalReposo"];
            $reporteProceso->tiempoHomogeneizacion = $rs["tiempoHomogeneizacion"];
            $reporteProceso->otrasHerramientas = $rs["otrasHerramientas"];
            $reporteProceso->noProcesada = $rs["noProcesada"];
            $reporteProceso->procesada = $rs["procesada"];
            $reporteProceso->observacionesDePesos = $rs["observacionesDePesos"];
    
            $reporteProceso->producto = $rs["producto"];
            $reporteProceso->inicioHomogeneizado = $rs["inicioHomogeneizado"];
            $reporteProceso->finalHomogeneizado = $rs["finalHomogeneizado"];
            $reporteProceso->tiempoReposo = $rs["tiempoReposo"];
            $reporteProceso->observaciones = $rs["observaciones"];
            $reporteProceso->horaFinal = $rs["horaFinal"];
            $reporteProceso->cantidadLlave = $rs["cantidadLlave"];
            $reporteProceso->condicionLlave = $rs["condicionLlave"];
            $reporteProceso->cantidadPala = $rs["cantidadPala"];
            $reporteProceso->condicionPala = $rs["condicionPala"];
            $reporteProceso->cantidadOtras = $rs["cantidadOtras"];
            $reporteProceso->condicionOtras = $rs["condicionOtras"];
    
            $reporteProceso->personalConformacion = array();
            $reporteProceso->personalProcesoInocuo = array();
            $reporteProceso->personalFolios = array();
    
            $sqlPersonalConformacion = "SELECT pc.idConformacionLote, pc.idPersonalOM, om.nombre
                    FROM $conformacion pc
                    INNER JOIN personaloaxaca om ON om.idPersonalOM = pc.idPersonalOM
                    WHERE pc.idReporteProceso = :idReporteProceso";
            $datosPC = $con->prepare($sqlPersonalConformacion);
            $datosPC->bindParam(':idReporteProceso', $idReporteProceso);
            $datosPC->execute();
            while ($rsCont = $datosPC->fetch()) {
                $conformacion = new stdClass();
                $conformacion->idConformacionLote = $rsCont["idConformacionLote"];
                $conformacion->idPersonalOM = $rsCont["idPersonalOM"];
                $conformacion->nombre = $rsCont["nombre"];
                $reporteProceso->personalConformacion[] = $conformacion;
            }
    
            $sqlPersonalProceso = "SELECT pp.idProceso, pp.idPersonalOM, om.nombre 
                    FROM $procesoinocuo pp
                    INNER JOIN personaloaxaca om ON om.idPersonalOM = pp.idPersonalOM
                    WHERE pp.idReporteProceso = :idReporteProceso ";
            $datosPP = $con->prepare($sqlPersonalProceso);
            $datosPP->bindParam(':idReporteProceso', $idReporteProceso);
            $datosPP->execute();
            while ($rsCont = $datosPP->fetch()) {
                $proceso = new stdClass();
                $proceso->idProceso = $rsCont["idProceso"];
                $proceso->idPersonalOM = $rsCont["idPersonalOM"];
                $proceso->nombre = $rsCont["nombre"];
                $reporteProceso->personalProcesoInocuo[] = $proceso;
            }
    
            $sqlPersonalFolios = "SELECT pf.idBusqueda, pf.idPersonalOM, om.nombre 
                    FROM $busqueda pf
                    INNER JOIN personaloaxaca om ON om.idPersonalOM = pf.idPersonalOM
                    WHERE pf.idReporteProceso = :idReporteProceso";
            $datosPF = $con->prepare($sqlPersonalFolios);
            $datosPF->bindParam(':idReporteProceso', $idReporteProceso);
            $datosPF->execute();
            while ($rsCont = $datosPF->fetch()) {
                $folios = new stdClass();
                $folios->idBusqueda = $rsCont["idBusqueda"];
                $folios->idPersonalOM = $rsCont["idPersonalOM"];
                $folios->nombre = $rsCont["nombre"];
                $reporteProceso->personalFolios[] = $folios;
            }
        }
        echo json_encode($reporteProceso);
    }
}else{
    $sql = "SELECT r.*, 
    h.cantidadLlave, h.condicionLlave, h.cantidadOtras, h.condicionOtras, h.cantidadPala, h.condicionPala
     FROM reportesdeprocesos r
    LEFT JOIN herramientas h
    ON h.idReporteProceso = r.idReporteProceso
    WHERE r.idReporteProceso = :idReporteProceso";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idReporteProceso', $idReporteProceso);
    $datos->execute();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $reporteProceso = new stdClass();
            $reporteProceso->idReporteProceso = $rs["idReporteProceso"];
            $reporteProceso->fechaReporte = $rs["fechaReporte"];
            $reporteProceso->idLoteInterno = $rs["idLoteInterno"];
            $reporteProceso->responsable = $rs["responsable"];
            $reporteProceso->supervisor = $rs["supervisor"];
            $reporteProceso->horaInicio = $rs["horaInicio"];
            $reporteProceso->numeroTanque = $rs["numeroTanque"];
    
            $reporteProceso->cabello = $rs["cabello"];
            $reporteProceso->cofia = $rs["cofia"];
            $reporteProceso->botas = $rs["botas"];
            $reporteProceso->unas = $rs["unas"];
            $reporteProceso->cubreBoca = $rs["cubreBoca"];
            $reporteProceso->sanitizacionManos = $rs["sanitizacionManos"];
            $reporteProceso->ropa = $rs["ropa"];
            $reporteProceso->lavadoManos = $rs["lavadoManos"];
            $reporteProceso->sanitizacionBotas = $rs["sanitizacionBotas"];
            $reporteProceso->lavadoHerramientas = $rs["lavadoHerramientas"];
            $reporteProceso->horaFinalReposo = $rs["horaFinalReposo"];
            $reporteProceso->tiempoHomogeneizacion = $rs["tiempoHomogeneizacion"];
            $reporteProceso->otrasHerramientas = $rs["otrasHerramientas"];
            $reporteProceso->noProcesada = $rs["noProcesada"];
            $reporteProceso->procesada = $rs["procesada"];
            $reporteProceso->observacionesDePesos = $rs["observacionesDePesos"];
    
            $reporteProceso->producto = $rs["producto"];
            $reporteProceso->inicioHomogeneizado = $rs["inicioHomogeneizado"];
            $reporteProceso->finalHomogeneizado = $rs["finalHomogeneizado"];
            $reporteProceso->tiempoReposo = $rs["tiempoReposo"];
            $reporteProceso->observaciones = $rs["observaciones"];
            $reporteProceso->horaFinal = $rs["horaFinal"];
            $reporteProceso->cantidadLlave = $rs["cantidadLlave"];
            $reporteProceso->condicionLlave = $rs["condicionLlave"];
            $reporteProceso->cantidadPala = $rs["cantidadPala"];
            $reporteProceso->condicionPala = $rs["condicionPala"];
            $reporteProceso->cantidadOtras = $rs["cantidadOtras"];
            $reporteProceso->condicionOtras = $rs["condicionOtras"];
    
            $reporteProceso->personalConformacion = array();
            $reporteProceso->personalProcesoInocuo = array();
            $reporteProceso->personalFolios = array();
    
            $sqlPersonalConformacion = "SELECT pc.idConformacionLote, pc.idPersonalOM, om.nombre
                    FROM conformaciondelotes pc
                    INNER JOIN personaloaxaca om ON om.idPersonalOM = pc.idPersonalOM
                    WHERE pc.idReporteProceso = :idReporteProceso";
            $datosPC = $con->prepare($sqlPersonalConformacion);
            $datosPC->bindParam(':idReporteProceso', $idReporteProceso);
            $datosPC->execute();
            while ($rsCont = $datosPC->fetch()) {
                $conformacion = new stdClass();
                $conformacion->idConformacionLote = $rsCont["idConformacionLote"];
                $conformacion->idPersonalOM = $rsCont["idPersonalOM"];
                $conformacion->nombre = $rsCont["nombre"];
                $reporteProceso->personalConformacion[] = $conformacion;
            }
    
            $sqlPersonalProceso = "SELECT pp.idProceso, pp.idPersonalOM, om.nombre 
                    FROM procesozonainocua pp
                    INNER JOIN personaloaxaca om ON om.idPersonalOM = pp.idPersonalOM
                    WHERE pp.idReporteProceso = :idReporteProceso ";
            $datosPP = $con->prepare($sqlPersonalProceso);
            $datosPP->bindParam(':idReporteProceso', $idReporteProceso);
            $datosPP->execute();
            while ($rsCont = $datosPP->fetch()) {
                $proceso = new stdClass();
                $proceso->idProceso = $rsCont["idProceso"];
                $proceso->idPersonalOM = $rsCont["idPersonalOM"];
                $proceso->nombre = $rsCont["nombre"];
                $reporteProceso->personalProcesoInocuo[] = $proceso;
            }
    
            $sqlPersonalFolios = "SELECT pf.idBusqueda, pf.idPersonalOM, om.nombre 
                    FROM busquedadefolios pf
                    INNER JOIN personaloaxaca om ON om.idPersonalOM = pf.idPersonalOM
                    WHERE pf.idReporteProceso = :idReporteProceso";
            $datosPF = $con->prepare($sqlPersonalFolios);
            $datosPF->bindParam(':idReporteProceso', $idReporteProceso);
            $datosPF->execute();
            while ($rsCont = $datosPF->fetch()) {
                $folios = new stdClass();
                $folios->idBusqueda = $rsCont["idBusqueda"];
                $folios->idPersonalOM = $rsCont["idPersonalOM"];
                $folios->nombre = $rsCont["nombre"];
                $reporteProceso->personalFolios[] = $folios;
            }
        }
        echo json_encode($reporteProceso);
    }
}
?>