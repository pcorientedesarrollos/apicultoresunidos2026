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
            $cubetasencabezado = 'cubetasencabezado';
            $cubetasdetalle = 'cubetasdetalle';
            $mp_encabezado = 'materiaprimaencabezadoentradas';
            $mp_detalle = 'materiaprimadetalleentradas';
            $traspaso_encabezado = 'almacenencabezadotraspaso';
            $traspaso_detalle = 'almacentraspaso';
            break;
        case '1':  //convencionales
            $entradaysalida_tabla = 'entradaysalida';
            $cubetasencabezado = 'cubetasencabezado';
            $cubetasdetalle = 'cubetasdetalle';
            $mp_encabezado = 'materiaprimaencabezadoentradas';
            $mp_detalle = 'materiaprimadetalleentradas';
            $traspaso_encabezado = 'almacenencabezadotraspaso';
            $traspaso_detalle = 'almacentraspaso';
            $tipo = '1';
            break;
        case '2':  //orgánicos
            $entradaysalida_tabla = 'entradaysalida_organico';
            $cubetasencabezado = 'cubetasencabezado_organico';
            $cubetasdetalle = 'cubetasdetalle_organico';
            $mp_encabezado = 'materiaprimaencabezadoentradas_organico';
            $mp_detalle = 'materiaprimadetalleentradas_organico';
            $traspaso_encabezado = 'almacenencabezadotraspaso_organico';
            $traspaso_detalle = 'almacentraspaso_organico';
            $tipo = '2';
            break;
        case '5':  //MANTEQUILLA
            $entradaysalida_tabla = 'entradaysalida_mantequilla';
            $cubetasencabezado = 'cubetasencabezado_mantequilla';
            $cubetasdetalle = 'cubetasdetalle_mantequilla';
            $mp_encabezado = 'materiaprimaencabezadoentradas_mantequilla';
            $mp_detalle = 'materiaprimadetalleentradas_mantequilla';
            $traspaso_encabezado = 'almacenencabezadotraspaso_mantequilla';
            $traspaso_detalle = 'almacentraspaso_mantequilla';
            $tipo = '5';
            break;
        case '6':  //ALTIPLANO
            $entradaysalida_tabla = 'entradaysalida_altiplano';
            $cubetasencabezado = 'cubetasencabezado_altiplano';
            $cubetasdetalle = 'cubetasdetalle_altiplano';
            $mp_encabezado = 'materiaprimaencabezadoentradas_altiplano';
            $mp_detalle = 'materiaprimadetalleentradas_altiplano';
            $traspaso_encabezado = 'almacenencabezadotraspaso_altiplano';
            $traspaso_detalle = 'almacentraspaso_altiplano';
            $tipo = '6';
            break;
        case '7':  //NARANJO
            $entradaysalida_tabla = 'entradaysalida_naranjo';
            $cubetasencabezado = 'cubetasencabezado_naranjo';
            $cubetasdetalle = 'cubetasdetalle_naranjo';
            $mp_encabezado = 'materiaprimaencabezadoentradas_naranjo';
            $mp_detalle = 'materiaprimadetalleentradas_naranjo';
            $traspaso_encabezado = 'almacenencabezadotraspaso_naranjo';
            $traspaso_detalle = 'almacentraspaso_naranjo';
            $tipo = '7';
            break;
        case '8':  //aguacate
            $entradaysalida_tabla = 'entradaysalida_aguacate';
            $cubetasencabezado = 'cubetasencabezado_aguacate';
            $cubetasdetalle = 'cubetasdetalle_aguacate';
            $mp_encabezado = 'materiaprimaencabezadoentradas_aguacate';
            $mp_detalle = 'materiaprimadetalleentradas_aguacate';
            $traspaso_encabezado = 'almacenencabezadotraspaso_aguacate';
            $traspaso_detalle = 'almacentraspaso_aguacate';
            $tipo = '8';
            break;
        case '9':  //mezquite
            $entradaysalida_tabla = 'entradaysalida_mezquite';
            $cubetasencabezado = 'cubetasencabezado_mezquite';
            $cubetasdetalle = 'cubetasdetalle_mezquite';
            $mp_encabezado = 'materiaprimaencabezadoentradas_mezquite';
            $mp_detalle = 'materiaprimadetalleentradas_mezquite';
            $traspaso_encabezado = 'almacenencabezadotraspaso_mezquite';
            $traspaso_detalle = 'almacentraspaso_mezquite';
            $tipo = '9';
            break;
    }

    if (isset($_GET['mes'])) {
        $mes = $_GET['mes'];
    }

    $sql = "SELECT rd.idReporte, rd.fechaImpresion, rd.lote, rd.tambor, rd.cubeta, rd.cera, rd.apicolas, rd.mp, rd.traspaso, rd.envasesFrascos, rd.productosDerivados, t.operador
     FROM $entradaysalida_tabla rd
     LEFT JOIN choferes t ON t.idOperador = rd.idOperador
     WHERE rd.estado = '0'";

    if ($info[0] == '0') { //productos apícolas
        if (isset($_GET['mes'])) {
            $sql .= " AND rd.tambor = '0' AND rd.cubeta = '0' AND rd.cera = '0' AND rd.mp = '0' AND rd.traspaso = '0' AND rd.apicolas = '1' AND SUBSTR(rd.fechaImpresion FROM 6 FOR 2) = $mes OR rd.envasesFrascos = '1' AND rd.estado = '0' AND SUBSTR(rd.fechaImpresion FROM 6 FOR 2) = $mes OR rd.productosDerivados = '1' AND rd.estado = '0' AND SUBSTR(rd.fechaImpresion FROM 6 FOR 2) = $mes";
        } else {
            $sql .= " AND rd.tambor = '0' AND rd.cubeta = '0' AND rd.cera = '0' AND rd.mp = '0' AND rd.traspaso = '0' AND rd.apicolas = '1' OR rd.envasesFrascos = '1' OR rd.productosDerivados = '1' AND rd.estado = '0'";
        }
    } else if ($info[0] !== '0') { // productos convencionales u organicos
        if (isset($_GET['mes'])) {
            $sql .= " AND rd.tambor = '1' AND SUBSTR(rd.fechaImpresion FROM 6 FOR 2) = $mes OR rd.estado = '0' AND rd.cubeta = '1' AND SUBSTR(rd.fechaImpresion FROM 6 FOR 2) = $mes OR rd.estado = '0' AND rd.cera = '1' AND SUBSTR(rd.fechaImpresion FROM 6 FOR 2) = $mes  OR rd.estado = '0' AND rd.mp = '1' AND SUBSTR(rd.fechaImpresion FROM 6 FOR 2) = $mes OR rd.estado = '0' AND rd.traspaso = '1' AND SUBSTR(rd.fechaImpresion FROM 6 FOR 2) = $mes";
        } else {
            $sql .= " AND rd.tambor = '1' OR rd.estado = '0' AND rd.cubeta = '1' OR rd.estado = '0' AND rd.cera = '1' OR rd.estado = '0' AND rd.mp = '1' OR rd.estado = '0' AND rd.traspaso = '1'";
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
         	(SELECT ce.idAlmacen FROM $cubetasencabezado ce 
         	INNER JOIN $cubetasdetalle cd ON cd.idAlmacenEncabezado = ce.idAlmacen
         	WHERE ce.idReporteDescarga = :idReporte GROUP BY ce.idAlmacen) AS entradaCubeta, 
         	(SELECT ce.idAlmacen FROM almacenencabezadocera ce 
         	INNER JOIN almacencera cd ON cd.idAlmacenEncabezado = ce.idAlmacen
         	WHERE ce.idReporteDescarga = :idReporte AND ce.tipo = '1' AND ce.tipoCera = :tipo GROUP BY ce.idAlmacen) AS entradaCera, 
         	(SELECT ea.idAlmacen FROM almacenencabezadoapicola ea
         	INNER JOIN almacenapicola ad ON ad.idAlmacenEncabezado = ea.idAlmacen
         	WHERE ea.idReporteDescarga = :idReporte AND ea.fecha = :fecha GROUP BY ea.idAlmacen) AS entradaApicola, 
         	(SELECT mpe.idEntradaMateria FROM $mp_encabezado mpe
         	INNER JOIN $mp_detalle mpd ON mpd.idEntradaMateria = mpe.idEntradaMateria 
         	WHERE mpe.idReporteDescarga = :idReporte GROUP BY mpe.idEntradaMateria) AS entradaMP,
            (SELECT altren.idAlmacen FROM $traspaso_encabezado altren
         	INNER JOIN $traspaso_detalle altrde ON altrde.idAlmacenEncabezado = altren.idAlmacen 
         	WHERE altren.idReporteDescarga = :idReporte GROUP BY altren.idAlmacen) AS entradaTraspaso,
            (SELECT enfre.idEntradaEnvases FROM envasesfrascosencabezadoentradas enfre
            INNER JOIN envasesfrascosdetalleentradas enfrd ON enfrd.idEntradaEnvases = enfre.idEntradaEnvases 
            WHERE enfre.idReporteDescarga = :idReporte GROUP BY enfre.idEntradaEnvases) AS entradaEnvasesFrascos,
            (SELECT pde.idEntrada FROM derivadosalmacenencabezado pde
            INNER JOIN derivadosalmacendetalle pdd ON pdd.idEntrada = pde.idEntrada 
            WHERE pde.idReporteDescarga = :idReporte GROUP BY pde.idEntrada) AS entradaProductosDerivados
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

    echo json_encode(['error' => false, 'message' => 'Consulta de datos satisfactoria', 'data' => $resultado]);
} catch (Exception $e) {
    // echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => $resultado]);
    exit();
}
