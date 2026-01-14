<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$tipoDeMiel = file_get_contents('php://input');
$resultado = array();

try {

    $info = json_decode($tipoDeMiel);

    switch ($info[0]) {  //tipo de miel
        case '0':  //otros productos
            $entradaysalida_tabla = 'entradaysalida';
            $calidad_tabla = 'calidad';
            $mp_encabezado = 'materiaprimaencabezadosalidas';
            $mp_detalle = 'materiaprimadetallesalidas';
            break;
        case '1':  //convencionales
            $entradaysalida_tabla = 'entradaysalida';
            $calidad_tabla = 'calidad';
            $mp_encabezado = 'materiaprimaencabezadosalidas';
            $mp_detalle = 'materiaprimadetallesalidas';
            $tipo = '1';
            break;
        case '2':  //orgánicos
            $entradaysalida_tabla = 'entradaysalida_organico';
            $calidad_tabla = 'calidad_organico';
            $mp_encabezado = 'materiaprimaencabezadosalidas_organico';
            $mp_detalle = 'materiaprimadetallesalidas_organico';
            $tipo = '2';
            break;
        case '5':  //mantequilla
            $entradaysalida_tabla = 'entradaysalida_mantequilla';
            $calidad_tabla = 'calidad_mantequilla';
            $mp_encabezado = 'materiaprimaencabezadosalidas_mantequilla';
            $mp_detalle = 'materiaprimadetallesalidas_mantequilla';
            $tipo = '5';
            break;
        case '6':  //altiplano
            $entradaysalida_tabla = 'entradaysalida_altiplano';
            $calidad_tabla = 'calidad_altiplano';
            $mp_encabezado = 'materiaprimaencabezadosalidas_altiplano';
            $mp_detalle = 'materiaprimadetallesalidas_altiplano';
            $tipo = '6';
            break;
        case '7':  //naranjo
            $entradaysalida_tabla = 'entradaysalida_naranjo';
            $calidad_tabla = 'calidad_naranjo';
            $mp_encabezado = 'materiaprimaencabezadosalidas_naranjo';
            $mp_detalle = 'materiaprimadetallesalidas_naranjo';
            $tipo = '7';
            break;
        case '8':  //aguacate
            $entradaysalida_tabla = 'entradaysalida_aguacate';
            $calidad_tabla = 'calidad_aguacate';
            $mp_encabezado = 'materiaprimaencabezadosalidas_aguacate';
            $mp_detalle = 'materiaprimadetallesalidas_aguacate';
            $tipo = '8';
            break;
        case '9':  //mezquite
            $entradaysalida_tabla = 'entradaysalida_mezquite';
            $calidad_tabla = 'calidad_mezquite';
            $mp_encabezado = 'materiaprimaencabezadosalidas_mezquite';
            $mp_detalle = 'materiaprimadetallesalidas_mezquite';
            $tipo = '9';
            break;
    }

    if (isset($_GET['mes'])) {
        $mes = $_GET['mes'];
    }

    $sql = "SELECT rd.idReporte, rd.fechaImpresion,rd.folioCotizacion, rd.lote, rd.miel, rd.cera, rd.apicolas, rd.mp, rd.envasesFrascos, rd.productosDerivados
    FROM $entradaysalida_tabla rd
    LEFT JOIN $calidad_tabla c ON c.idLoteInterno = rd.idLoteInterno
    WHERE rd.estado = '1'";

    if ($info[0] == '0') { //Otros productos
        if (isset($_GET['mes'])) {
            $sql .= " AND rd.miel = '0'AND rd.cera = '0' AND rd.mp = '0' AND SUBSTR(rd.fechaImpresion FROM 6 FOR 2) = $mes AND rd.apicolas = '1' OR rd.envasesFrascos = '1' OR rd.productosDerivados = '1' AND SUBSTR(rd.fechaImpresion FROM 6 FOR 2) = $mes AND rd.estado = '1'";
        } else {
            $sql .= " AND rd.miel = '0' AND rd.cera = '0' AND rd.mp = '0' AND rd.apicolas = '1' OR rd.envasesFrascos = '1' OR rd.productosDerivados = '1' AND rd.estado = '1'";
        }
    } else if ($info[0] !== '0') { // productos convencionales u organicos
        if (isset($_GET['mes'])) {
            $sql .= " AND rd.miel = '1' AND SUBSTR(rd.fechaImpresion FROM 6 FOR 2) = $mes 
            OR rd.estado = '1' AND rd.cera = '1' AND SUBSTR(rd.fechaImpresion FROM 6 FOR 2) = $mes
            OR rd.estado = '1' AND rd.mp = '1' AND SUBSTR(rd.fechaImpresion FROM 6 FOR 2) = $mes";
        } else {
            $sql .= " AND rd.miel = '1' 
            OR rd.estado = 1 AND rd.cera = '1'
            OR rd.estado = 1 AND rd.mp = '1'";
        }
    }

    $sql .= " ORDER BY rd.fechaImpresion DESC, rd.idReporte DESC";
    $datos = $con->prepare($sql);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }

    foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $res) {
        $datosR = $con->prepare("SELECT COUNT(rd.idReporte) AS registros, 
        (SELECT lp.idTamborPeso FROM listadepesos lp 
INNER JOIN tamboreslistapesos tp ON tp.idTamborPeso = lp.idTamborPeso
WHERE lp.idReporteCarga = :idReporte GROUP BY lp.idTamborPeso LIMIT 1) AS entradaMiel,
         	(SELECT ce.idAlmacen FROM almacenencabezadocera ce 
         	INNER JOIN almacencera cd ON cd.idAlmacenEncabezado = ce.idAlmacen
         	WHERE ce.idReporteDescarga = :idReporte AND ce.tipo = '2' AND ce.tipoCera = :tipo GROUP BY ce.idAlmacen) AS entradaCera, 
         	(SELECT ea.idAlmacen FROM almacenencabezadoapicola ea
         	INNER JOIN almacenapicola ad ON ad.idAlmacenEncabezado = ea.idAlmacen
         	WHERE ea.idReporteDescarga = :idReporte AND ea.fecha = :fecha GROUP BY ea.idAlmacen) AS entradaApicola, 
         	(SELECT mpe.idSalidaMateria FROM $mp_encabezado mpe
         	INNER JOIN $mp_detalle mpd ON mpd.idSalidaMateria = mpe.idSalidaMateria 
         	WHERE mpe.idReporteCarga = :idReporte GROUP BY mpe.idSalidaMateria) AS entradaMP,
            (SELECT enfre.idSalidaEnvases FROM envasesfrascosencabezadosalidas enfre
         	INNER JOIN envasesfrascosdetallesalidas enfrd ON enfrd.idSalidaEnvases = enfre.idSalidaEnvases 
         	WHERE enfre.idReporteCarga = :idReporte GROUP BY enfre.idSalidaEnvases) AS entradaEnvasesFrascos,
             (SELECT ea.idSalida FROM derivadosalmacenencabezado_salidas ea
         	INNER JOIN derivadosalmacendetalle_salidas ad ON ad.idSalida = ea.idSalida
         	WHERE ea.idReporteCarga = :idReporte AND ea.fecha = :fecha GROUP BY ea.idSalida) AS entradaProductosDerivados     
             FROM $entradaysalida_tabla rd
             WHERE rd.idReporte = :idReporte");
        $datosR->bindParam(':idReporte', $res['idReporte']);
        $datosR->bindParam(':fecha', $res['fechaImpresion']);
        $datosR->bindParam(':tipo', $tipo);
        $datosR->execute();
        if ($datosR == FALSE) {
            throw new Exception($con->errorInfo());
        }
        $condiciones = $datosR->fetch(PDO::FETCH_ASSOC);
        foreach ($condiciones as $key => $value) {
            $res[$key] = $condiciones[$key];
        }
        array_push($resultado, $res);
    };

    echo json_encode(['error' => false, 'message' => '', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => $resultado]);
    exit();
}
