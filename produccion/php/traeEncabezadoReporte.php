<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

//$query = "             
//SELECT idReporteProceso, fechaReporte, idLoteInterno
//FROM reportesdeprocesos
//ORDER BY fechaReporte DESC
//";
if(isset($_GET["organica"])){
    switch ($_GET['organica']) {
        case 0:
            $tabla = 'reportesdeprocesos_organico';
            $tabla_calidad = 'calidad_organico';
            break;
        case 1:
            $tabla = 'reportesdeprocesos_mantequilla';
            $tabla_calidad = 'calidad_mantequilla';
            break;
        case 2:
            $tabla = 'reportesdeprocesos_altiplano';
            $tabla_calidad = 'calidad_altiplano';
            break;
        case 3:
            $tabla = 'reportesdeprocesos_naranjo';
            $tabla_calidad = 'calidad_naranjo';
            break;
        case 4:
            $tabla = 'reportesdeprocesos_aguacate';
            $tabla_calidad = 'calidad_aguacate';
            break;
        case 5:
            $tabla = 'reportesdeprocesos_mezquite';
            $tabla_calidad = 'calidad_mezquite';
            break;
    }
    $query = "SELECT rp.idReporteProceso, rp.idLoteInterno, 
    li.fechaProceso,li.numeroDeTambores, li.kilosTotales
    FROM $tabla rp
    LEFT JOIN $tabla_calidad li ON li.idLoteInterno = rp.idLoteInterno
    ORDER BY rp.idLoteInterno DESC";
    $datos = $con->prepare($query);
    $datos->execute();
    
    $arrayR = array();
    while ($row = $datos->fetch()) {
        $menuProceso = new stdClass();
        $menuProceso->idReporteProceso = $row["idReporteProceso"];
        $menuProceso->fechaProceso = $row["fechaProceso"];
        $menuProceso->idLoteInterno = $row["idLoteInterno"];
        $menuProceso->numeroDeTambores = $row["numeroDeTambores"];
        $menuProceso->kilosTotales = $row["kilosTotales"];
        $arrayR[] = $menuProceso;
    }
    
    echo json_encode($arrayR);
}else{
    $query = "SELECT rp.idReporteProceso, rp.idLoteInterno, 
    li.fechaProceso,li.numeroDeTambores, li.kilosTotales
    FROM reportesdeprocesos rp
    LEFT JOIN calidad li ON li.idLoteInterno = rp.idLoteInterno
    ORDER BY rp.idLoteInterno DESC";
    $datos = $con->prepare($query);
    $datos->execute();
    
    $arrayR = array();
    while ($row = $datos->fetch()) {
        $menuProceso = new stdClass();
        $menuProceso->idReporteProceso = $row["idReporteProceso"];
        $menuProceso->fechaProceso = $row["fechaProceso"];
        $menuProceso->idLoteInterno = $row["idLoteInterno"];
        $menuProceso->numeroDeTambores = $row["numeroDeTambores"];
        $menuProceso->kilosTotales = $row["kilosTotales"];
        $arrayR[] = $menuProceso;
    }
    
    echo json_encode($arrayR);
}

?>