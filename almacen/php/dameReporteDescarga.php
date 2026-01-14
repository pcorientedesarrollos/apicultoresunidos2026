<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$datos = file_get_contents('php://input');

try {
    if (!$datos) {
        throw new Exception('No se recibieron parámentros');
    } else {
        $datos = json_decode($datos);
        $idReporte = $datos->idReporte;
        $tipoDeMiel = $datos->tipoDeMiel;
    }

    switch ($tipoDeMiel) {
        case '0':
            $entradaysalida_tabla = 'entradaysalida';
            $personaldescarga_tabla = 'personaldescarga';
            $cubetasencabezado = 'cubetasencabezado';
            $cubetasDetalle = 'cubetasdetalle';
            $mp_encabezado = 'materiaprimaencabezadoentradas';
            $mp_detalle = 'materiaprimadetalleentradas';
            $traspaso_encabezado = 'almacenencabezadotraspaso';
            $traspaso_detalle = 'almacentraspaso';
            break;
        case '1':
            $entradaysalida_tabla = 'entradaysalida';
            $personaldescarga_tabla = 'personaldescarga';
            $cubetasencabezado = 'cubetasencabezado';
            $cubetasDetalle = 'cubetasdetalle';
            $mp_encabezado = 'materiaprimaencabezadoentradas';
            $mp_detalle = 'materiaprimadetalleentradas';
            $traspaso_encabezado = 'almacenencabezadotraspaso';
            $traspaso_detalle = 'almacentraspaso';
            $tipo = '1';
            break;
        case '2':
            $entradaysalida_tabla = 'entradaysalida_organico';
            $personaldescarga_tabla = 'personaldescarga_organico';
            $cubetasencabezado = 'cubetasencabezado_organico';
            $cubetasDetalle = 'cubetasdetalle_organico';
            $mp_encabezado = 'materiaprimaencabezadoentradas_organico';
            $mp_detalle = 'materiaprimadetalleentradas_organico';
            $traspaso_encabezado = 'almacenencabezadotraspaso_organico';
            $traspaso_detalle = 'almacentraspaso_organico';
            $tipo = '2';
            break;
        case '5':
            $entradaysalida_tabla = 'entradaysalida_mantequilla';
            $personaldescarga_tabla = 'personaldescarga_mantequilla';
            $cubetasencabezado = 'cubetasencabezado_mantequilla';
            $cubetasDetalle = 'cubetasdetalle_mantequilla';
            $mp_encabezado = 'materiaprimaencabezadoentradas_mantequilla';
            $mp_detalle = 'materiaprimadetalleentradas_mantequilla';
            $traspaso_encabezado = 'almacenencabezadotraspaso_mantequilla';
            $traspaso_detalle = 'almacentraspaso_mantequilla';
            $tipo = '5';
            break;
        case '6':
            $entradaysalida_tabla = 'entradaysalida_altiplano';
            $personaldescarga_tabla = 'personaldescarga_altiplano';
            $cubetasencabezado = 'cubetasencabezado_altiplano';
            $cubetasDetalle = 'cubetasdetalle_altiplano';
            $mp_encabezado = 'materiaprimaencabezadoentradas_altiplano';
            $mp_detalle = 'materiaprimadetalleentradas_altiplano';
            $traspaso_encabezado = 'almacenencabezadotraspaso_altiplano';
            $traspaso_detalle = 'almacentraspaso_altiplano';
            $tipo = '6';
            break;
        case '7':
            $entradaysalida_tabla = 'entradaysalida_naranjo';
            $personaldescarga_tabla = 'personaldescarga_naranjo';
            $cubetasencabezado = 'cubetasencabezado_naranjo';
            $cubetasDetalle = 'cubetasdetalle_naranjo';
            $mp_encabezado = 'materiaprimaencabezadoentradas_naranjo';
            $mp_detalle = 'materiaprimadetalleentradas_naranjo';
            $traspaso_encabezado = 'almacenencabezadotraspaso_naranjo';
            $traspaso_detalle = 'almacentraspaso_naranjo';
            $tipo = '7';
            break;
        case '8':
            $entradaysalida_tabla = 'entradaysalida_aguacate';
            $personaldescarga_tabla = 'personaldescarga_aguacate';
            $cubetasencabezado = 'cubetasencabezado_aguacate';
            $cubetasDetalle = 'cubetasdetalle_aguacate';
            $mp_encabezado = 'materiaprimaencabezadoentradas_aguacate';
            $mp_detalle = 'materiaprimadetalleentradas_aguacate';
            $traspaso_encabezado = 'almacenencabezadotraspaso_aguacate';
            $traspaso_detalle = 'almacentraspaso_aguacate';
            $tipo = '8';
            break;
        case '9':
            $entradaysalida_tabla = 'entradaysalida_mezquite';
            $personaldescarga_tabla = 'personaldescarga_mezquite';
            $cubetasencabezado = 'cubetasencabezado_mezquite';
            $cubetasDetalle = 'cubetasdetalle_mezquite';
            $mp_encabezado = 'materiaprimaencabezadoentradas_mezquite';
            $mp_detalle = 'materiaprimadetalleentradas_mezquite';
            $traspaso_encabezado = 'almacenencabezadotraspaso_mezquite';
            $traspaso_detalle = 'almacentraspaso_mezquite';
            $tipo = '9';
            break;
        default:
            throw new Exception('Tipo de miel no es válido');
            break;
    }

    $sql = "SELECT rd.idReporte, rd.fechaImpresion, rd.responsable, rd.idOperador, rd.idPlaca, 
    rd.lote, rd.observaciones, rd.estado,
    rd.idClasificacion, rd.miel, rd.clasificacionMiel, rd.cera, rd.apicolas, rd.tambor, rd.cubeta, rd.mp, rd.traspaso, rd.envasesFrascos, rd.productosDerivados,
    rd.idDescripcion, rd.idCondicion, rd.idExternoTambor, rd.idPersonal, 
    cu.limpieza, cu.materialExtrano, cu.vehiculoAdecuado,
        cu.cabello, cu.unas, cu.ropa,
        d.producto, p.limpiezaPersonal, p.marcacion,
        t.operador, t.compania, t.licencia, t.vigencia,
        ex.roto, ex.abolladuras, ex.recipienteAdecuado, ex.lavadoExterior, pch.placa, pch.tipo,
        pch.marca, tr.remolque, pch.modelo, pch.marcaRemolque, pch.modeloRemolque, pch.placaRemolque,
            (SELECT ce.idAlmacen FROM $cubetasencabezado ce 
            INNER JOIN $cubetasDetalle cd ON cd.idAlmacenEncabezado = ce.idAlmacen
            WHERE ce.idReporteDescarga = :idReporte GROUP BY ce.idAlmacen) AS entradaCubeta, 
            (SELECT ce.idAlmacen FROM almacenencabezadocera ce 
            INNER JOIN almacencera cd ON cd.idAlmacenEncabezado = ce.idAlmacen
            WHERE ce.idReporteDescarga = :idReporte AND ce.tipoCera = :tipo GROUP BY ce.idAlmacen) AS entradaCera, 
            (SELECT ea.idAlmacen FROM almacenencabezadoapicola ea
            INNER JOIN almacenapicola ad ON ad.idAlmacenEncabezado = ea.idAlmacen
            WHERE ea.idReporteDescarga = :idReporte AND ea.fecha = rd.fechaImpresion GROUP BY ea.idAlmacen) AS entradaApicola, 
            (SELECT mpe.idEntradaMateria FROM $mp_encabezado mpe
            INNER JOIN $mp_detalle mpd ON mpd.idEntradaMateria = mpe.idEntradaMateria 
            WHERE mpe.idReporteDescarga = :idReporte GROUP BY mpe.idEntradaMateria) AS entradaMP,
            (SELECT ate.idAlmacen FROM $traspaso_encabezado ate
         	INNER JOIN $traspaso_detalle atd ON atd.idAlmacenEncabezado = ate.idAlmacen 
         	WHERE ate.idReporteDescarga = :idReporte GROUP BY ate.idAlmacen) AS entradaTraspaso,
            (SELECT enfre.idEntradaEnvases FROM envasesfrascosencabezadoentradas enfre
            INNER JOIN envasesfrascosdetalleentradas enfrd ON enfrd.idEntradaEnvases = enfre.idEntradaEnvases 
            WHERE enfre.idReporteDescarga = :idReporte GROUP BY enfre.idEntradaEnvases) AS entradaEnvasesFrascos,
            (SELECT pde.idEntrada FROM derivadosalmacenencabezado pde
            INNER JOIN derivadosalmacendetalle pdd ON pdd.idEntrada = pde.idEntrada 
            WHERE pde.idReporteDescarga = :idReporte GROUP BY pde.idEntrada) AS entradaProductosDerivados    
        FROM $entradaysalida_tabla rd
        LEFT JOIN condicionesunidad cu ON cu.idCondicion = rd.idCondicion
        LEFT JOIN descripciones d ON d.idDescripcion = rd.idDescripcion
        LEFT JOIN personalacciones p ON p.idPersonal = rd.idPersonal
        LEFT JOIN choferes t ON t.idOperador = rd.idOperador
        LEFT JOIN externotambores ex ON ex.idExternoTambor = rd.idExternoTambor
        LEFT JOIN placaschoferes pch ON pch.idPlaca = rd.idPlaca
    LEFT JOIN tiposremolque tr ON tr.idRemolque = pch.remolque
        WHERE rd.idReporte = :idReporte";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idReporte', $idReporte);
    $datos->bindParam(':tipo', $tipo);
    $datos->execute();

    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }

    $resultado = $datos->fetch(PDO::FETCH_ASSOC);
    $resultado['personalDescargas'] = array();

    $sqlPersonalDescarga = "SELECT pd.idPersonalDescarga, pd.idPersonalOM, om.nombre 
    FROM $personaldescarga_tabla pd 
    LEFT JOIN personaloaxaca om ON om.idPersonalOM = pd.idPersonalOM
    WHERE idReporte = :idReporte ";
    $datosPersonalDescarga = $con->prepare($sqlPersonalDescarga);
    $datosPersonalDescarga->bindParam(':idReporte', $idReporte);
    $datosPersonalDescarga->execute();

    if ($datosPersonalDescarga == FALSE) {
        throw new Exception($con->errorInfo());
    }

    foreach ($datosPersonalDescarga->fetchAll(PDO::FETCH_ASSOC) as $personal) {
        array_push($resultado['personalDescargas'], $personal);
    }
    echo json_encode($resultado);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit();
}
