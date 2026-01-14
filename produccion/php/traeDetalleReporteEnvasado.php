<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$data = file_get_contents('php://input');
try {
    if(!$data){
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($data);
        $idReporteEnvasado = $datos->idReporte;
        $tipoDeMiel = $datos->tipoDeMiel;
    }

    switch ($tipoDeMiel){
        case '1':
            $reportesdeenvasados_tabla = 'reportesdeenvasados';
            $personalenvasado_tabla = 'personalenvasado';
            $ayudantesinternos_tabla = 'ayudantesinternos';
            $ayudantesexternos_tabla = 'ayudantesexternos';
            $pesosenvasados_tabla = 'pesosenvasados';
            break;
        case '2':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_organico';
            $personalenvasado_tabla = 'personalenvasado_organico';
            $ayudantesinternos_tabla = 'ayudantesinternos_organico';
            $ayudantesexternos_tabla = 'ayudantesexternos_organico';
            $pesosenvasados_tabla = 'pesosenvasados_organico';
            break;
            
        case '5':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_mantequilla';
            $personalenvasado_tabla = 'personalenvasado_mantequilla';
            $ayudantesinternos_tabla = 'ayudantesinternos_mantequilla';
            $ayudantesexternos_tabla = 'ayudantesexternos_mantequilla';
            $pesosenvasados_tabla = 'pesosenvasados_mantequilla';
            break;
            
        case '6':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_altiplano';
            $personalenvasado_tabla = 'personalenvasado_altiplano';
            $ayudantesinternos_tabla = 'ayudantesinternos_altiplano';
            $ayudantesexternos_tabla = 'ayudantesexternos_altiplano';
            $pesosenvasados_tabla = 'pesosenvasados_altiplano';
            break;
            
        case '7':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_naranjo';
            $personalenvasado_tabla = 'personalenvasado_naranjo';
            $ayudantesinternos_tabla = 'ayudantesinternos_naranjo';
            $ayudantesexternos_tabla = 'ayudantesexternos_naranjo';
            $pesosenvasados_tabla = 'pesosenvasados_naranjo';
            break;
            
        case '8':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_aguacate';
            $personalenvasado_tabla = 'personalenvasado_aguacate';
            $ayudantesinternos_tabla = 'ayudantesinternos_aguacate';
            $ayudantesexternos_tabla = 'ayudantesexternos_aguacate';
            $pesosenvasados_tabla = 'pesosenvasados_aguacate';
            break;
            
        case '9':
            $reportesdeenvasados_tabla = 'reportesdeenvasados_mezquite';
            $personalenvasado_tabla = 'personalenvasado_mezquite';
            $ayudantesinternos_tabla = 'ayudantesinternos_mezquite';
            $ayudantesexternos_tabla = 'ayudantesexternos_mezquite';
            $pesosenvasados_tabla = 'pesosenvasados_mezquite';
            break;
        default:
            throw new Exception('Tipo de miel inválido');
            break;
    }

    $sql = "SELECT * FROM $reportesdeenvasados_tabla
    WHERE idReporteEnvasado = :idReporteEnvasado";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idReporteEnvasado', $idReporteEnvasado);
    $datos->execute();
    if($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }

    $reporteEnvasado = $datos->fetch(PDO::FETCH_ASSOC);
    $reporteEnvasado['personalEnvasado'] = array();
    $reporteEnvasado['personalAyudanteInterno'] = array();
    $reporteEnvasado['personalAyudanteExterno'] = array();
    $reporteEnvasado['envasados'] = array();

    $sqlPersonalEnvasado = "SELECT pc.idEnvasado, pc.idPersonalOM, om.nombre
    FROM $personalenvasado_tabla pc
    INNER JOIN personaloaxaca om ON om.idPersonalOM = pc.idPersonalOM
    WHERE pc.idReporteEnvasado = :idReporteEnvasado";
    $datosPC = $con->prepare($sqlPersonalEnvasado);
    $datosPC->bindParam(':idReporteEnvasado', $idReporteEnvasado);
    $datosPC->execute();
    $reporteEnvasado['personalEnvasado'] = $datosPC->fetchAll(PDO::FETCH_ASSOC);


    $sqlPersonalAyudanteInterno = "SELECT pp.idAyudanteInterno, pp.idPersonalOM, om.nombre 
    FROM $ayudantesinternos_tabla pp
    INNER JOIN personaloaxaca om ON om.idPersonalOM = pp.idPersonalOM
    WHERE pp.idReporteEnvasado = :idReporteEnvasado";
    $datosPP = $con->prepare($sqlPersonalAyudanteInterno);
    $datosPP->bindParam(':idReporteEnvasado', $idReporteEnvasado);
    $datosPP->execute();
    $reporteEnvasado['personalAyudanteInterno'] = $datosPP->fetchAll(PDO::FETCH_ASSOC);


    $sqlPersonalAyudanteExterno = "SELECT pf.idAyudanteExterno, pf.idPersonalOM, om.nombre 
    FROM $ayudantesexternos_tabla pf
    INNER JOIN personaloaxaca om ON om.idPersonalOM = pf.idPersonalOM
    WHERE pf.idReporteEnvasado = :idReporteEnvasado";
    $datosPF = $con->prepare($sqlPersonalAyudanteExterno);
    $datosPF->bindParam(':idReporteEnvasado', $idReporteEnvasado);
    $datosPF->execute();
    $reporteEnvasado['personalAyudanteExterno'] = $datosPF->fetchAll(PDO::FETCH_ASSOC);

    $sqlEnvasados = "SELECT * 
    FROM $pesosenvasados_tabla
    WHERE idReporteEnvasado = :idReporteEnvasado";
    $datosPE = $con->prepare($sqlEnvasados);
    $datosPE->bindParam(':idReporteEnvasado', $idReporteEnvasado);
    $datosPE->execute();
    $reporteEnvasado['envasados'] = $datosPE->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['error'=>false, 'data'=>$reporteEnvasado]);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
    exit();
}

// $idReporteEnvasado = $_GET["idReporteEnvasado"];

// $sql = "SELECT * 
// FROM reportesdeenvasados
// WHERE idReporteEnvasado = :idReporteEnvasado";
// $datos = $con->prepare($sql);
// $datos->bindParam(':idReporteEnvasado', $idReporteEnvasado);
// $datos->execute();

// if ($datos == false) {
//     echo mysql_error();
// } else {
//     while ($rs = $datos->fetch()) {
//         $reporteEnvasado = new stdClass();
//         $reporteEnvasado->idReporteEnvasado = $rs["idReporteEnvasado"];
// //        $reporteEnvasado->fecha = $rs["fecha"];
//         $reporteEnvasado->idLoteInterno = $rs["idLoteInterno"];
//         $reporteEnvasado->numeroTanque = $rs["numeroTanque"];
//         $reporteEnvasado->horaInicio = $rs["horaInicio"];
//         $reporteEnvasado->horaFinal = $rs["horaFinal"];
//         $reporteEnvasado->tiempo = $rs["tiempo"];
//         $reporteEnvasado->observaciones = utf8_encode($rs["observaciones"]);
//         $reporteEnvasado->kilosProcesados = $rs["kilosProcesados"];
//         $reporteEnvasado->netosEnvasados = $rs["netosEnvasados"];
//         $reporteEnvasado->merma = $rs["merma"];
//         $reporteEnvasado->faltante = $rs["faltante"];
//         $reporteEnvasado->observacionesDePesos = $rs["observacionesDePesos"];

//         $reporteEnvasado->personalEnvasado = array();
//         $reporteEnvasado->personalAyudanteInterno = array();
//         $reporteEnvasado->personalAyudanteExterno = array();
//         $reporteEnvasado->envasados = array();

        // $sqlPersonalEnvasado = "SELECT pc.idEnvasado, pc.idPersonalOM, om.nombre
        //         FROM personalenvasado pc
        //         INNER JOIN personaloaxaca om ON om.idPersonalOM = pc.idPersonalOM
        //         WHERE pc.idReporteEnvasado = :idReporteEnvasado";
        // $datosPC = $con->prepare($sqlPersonalEnvasado);
        // $datosPC->bindParam(':idReporteEnvasado', $idReporteEnvasado);
        // $datosPC->execute();
        // while ($rsCont = $datosPC->fetch()) {
        //     $pEnvasado = new stdClass();
        //     $pEnvasado->idEnvasado = $rsCont["idEnvasado"];
        //     $pEnvasado->idPersonalOM = $rsCont["idPersonalOM"];
        //     $pEnvasado->nombre = $rsCont["nombre"];
        //     $reporteEnvasado->personalEnvasado[] = $pEnvasado;
        // }

        // $sqlPersonalAyudanteInterno = "SELECT pp.idAyudanteInterno, pp.idPersonalOM, om.nombre 
        //         FROM ayudantesinternos pp
        //         INNER JOIN personaloaxaca om ON om.idPersonalOM = pp.idPersonalOM
        //         WHERE pp.idReporteEnvasado = :idReporteEnvasado";
        // $datosPP = $con->prepare($sqlPersonalAyudanteInterno);
        // $datosPP->bindParam(':idReporteEnvasado', $idReporteEnvasado);
        // $datosPP->execute();
        // while ($rsCont = $datosPP->fetch()) {
        //     $pInterno = new stdClass();
        //     $pInterno->idAyudanteInterno = $rsCont["idAyudanteInterno"];
        //     $pInterno->idPersonalOM = $rsCont["idPersonalOM"];
        //     $pInterno->nombre = $rsCont["nombre"];
        //     $reporteEnvasado->personalAyudanteInterno[] = $pInterno;
        // }

        // $sqlPersonalAyudanteExterno = "SELECT pf.idAyudanteExterno, pf.idPersonalOM, om.nombre 
        //         FROM ayudantesexternos pf
        //         INNER JOIN personaloaxaca om ON om.idPersonalOM = pf.idPersonalOM
        //         WHERE pf.idReporteEnvasado = :idReporteEnvasado";
        // $datosPF = $con->prepare($sqlPersonalAyudanteExterno);
        // $datosPF->bindParam(':idReporteEnvasado', $idReporteEnvasado);
        // $datosPF->execute();
        // while ($rsCont = $datosPF->fetch()) {
        //     $pExterno = new stdClass();
        //     $pExterno->idAyudanteExterno = $rsCont["idAyudanteExterno"];
        //     $pExterno->idPersonalOM = $rsCont["idPersonalOM"];
        //     $pExterno->nombre = $rsCont["nombre"];
        //     $reporteEnvasado->personalAyudanteExterno[] = $pExterno;
        // }

        // $sqlEnvasados = "SELECT * 
        //         FROM pesosenvasados
        //         WHERE idReporteEnvasado = :idReporteEnvasado";
        // $datosPE = $con->prepare($sqlEnvasados);
        // $datosPE->bindParam(':idReporteEnvasado', $idReporteEnvasado);
        // $datosPE->execute();
        // while ($rsCont = $datosPE->fetch()) {
        //     $envasado = new stdClass();
        //     $envasado->idPesoEnvasado = $rsCont["idPesoEnvasado"];
        //     $envasado->folio = $rsCont["folio"];
        //     $envasado->bruto = $rsCont["bruto"];
        //     $envasado->tara = $rsCont["tara"];
        //     $envasado->neto = $rsCont["neto"];
        //     $reporteEnvasado->envasados[] = $envasado;
        // }
//     }
//     echo json_encode($reporteEnvasado);
// }