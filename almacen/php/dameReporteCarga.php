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
            $mp_encabezado = 'materiaprimaencabezadosalidas';
            $mp_detalle = 'materiaprimadetallesalidas';
            break;
        case '1':
            $entradaysalida_tabla = 'entradaysalida';
            $personaldescarga_tabla = 'personaldescarga';
            $mp_encabezado = 'materiaprimaencabezadosalidas';
            $mp_detalle = 'materiaprimadetallesalidas';
            $tipo = '1';
            break;
        case '2':
            $entradaysalida_tabla = 'entradaysalida_organico';
            $personaldescarga_tabla = 'personaldescarga_organico';
            $mp_encabezado = 'materiaprimaencabezadosalidas_organico';
            $mp_detalle = 'materiaprimadetallesalidas_organico';
            $tipo = '2';
            break;
        case '5':
            $entradaysalida_tabla = 'entradaysalida_mantequilla';
            $personaldescarga_tabla = 'personaldescarga_mantequilla';
            $mp_encabezado = 'materiaprimaencabezadosalidas_mantequilla';
            $mp_detalle = 'materiaprimadetallesalidas_mantequilla';
            $tipo = '5';
            break;
        case '6':
            $entradaysalida_tabla = 'entradaysalida_altiplano';
            $personaldescarga_tabla = 'personaldescarga_altiplano';
            $mp_encabezado = 'materiaprimaencabezadosalidas_altiplano';
            $mp_detalle = 'materiaprimadetallesalidas_altiplano';
            $tipo = '6';
            break;
        case '7':
            $entradaysalida_tabla = 'entradaysalida_naranjo';
            $personaldescarga_tabla = 'personaldescarga_naranjo';
            $mp_encabezado = 'materiaprimaencabezadosalidas_naranjo';
            $mp_detalle = 'materiaprimadetallesalidas_naranjo';
            $tipo = '7';
            break;
        case '8':
            $entradaysalida_tabla = 'entradaysalida_aguacate';
            $personaldescarga_tabla = 'personaldescarga_aguacate';
            $mp_encabezado = 'materiaprimaencabezadosalidas_aguacate';
            $mp_detalle = 'materiaprimadetallesalidas_aguacate';
            $tipo = '8';
            break;
        case '9':
            $entradaysalida_tabla = 'entradaysalida_mezquite';
            $personaldescarga_tabla = 'personaldescarga_mezquite';
            $mp_encabezado = 'materiaprimaencabezadosalidas_mezquite';
            $mp_detalle = 'materiaprimadetallesalidas_mezquite';
            $tipo = '9';
            break;

        default:
            throw new Exception('Tipo de miel inválido');
            break;
    }

    $sql = "SELECT rd.idReporte, rd.fechaImpresion, rd.responsable, rd.idOperador, rd.idPlaca, 
    rd.lote, rd.idLoteInterno, rd.contenedor, rd.sello, rd.observaciones, rd.estado,
    rd.idClasificacion, rd.miel,rd.folioCotizacion, rd.cera,rd.clasificacionMiel, rd.apicolas, rd.mp, rd.envasesFrascos, rd.productosDerivados,
    rd.idDescripcion, rd.idCondicion, rd.idExternoTambor, rd.idPersonal, 
    cu.limpieza, cu.materialExtrano, cu.vehiculoAdecuado,
        cu.cabello, cu.unas, cu.ropa,
        d.producto, d.cantidad, p.limpiezaPersonal, p.marcacion, p.rotulacion, p.montacargas,
        t.operador, t.compania, t.licencia, t.vigencia,
        ex.roto, ex.abolladuras, ex.recipienteAdecuado, ex.lavadoExterior, pch.placa, pch.tipo,
        pch.marca, tr.remolque, pch.modelo, pch.marcaRemolque, pch.modeloRemolque, pch.placaRemolque,
            (SELECT ce.idAlmacen FROM almacenencabezadocera ce 
            INNER JOIN almacencera cd ON cd.idAlmacenEncabezado = ce.idAlmacen
            WHERE ce.idReporteDescarga = :idReporte AND ce.tipoCera = :tipo GROUP BY ce.idAlmacen) AS entradaCera, 
            (SELECT ea.idAlmacen FROM almacenencabezadoapicola ea
            INNER JOIN almacenapicola ad ON ad.idAlmacenEncabezado = ea.idAlmacen
            WHERE ea.idReporteDescarga = :idReporte AND ea.fecha = rd.fechaImpresion GROUP BY ea.idAlmacen) AS entradaApicola, 
            (SELECT mpe.idSalidaMateria FROM $mp_encabezado mpe
            INNER JOIN $mp_detalle mpd ON mpd.idSalidaMateria = mpe.idSalidaMateria 
            WHERE mpe.idReporteCarga = :idReporte GROUP BY mpe.idSalidaMateria) AS entradaMP,
            (SELECT enfre.idSalidaEnvases FROM envasesfrascosencabezadosalidas enfre
            INNER JOIN envasesfrascosdetallesalidas enfrd ON enfrd.idSalidaEnvases = enfre.idSalidaEnvases 
            WHERE enfre.idReporteCarga = :idReporte GROUP BY enfre.idSalidaEnvases) AS entradaEnvasesFrascos,
            (SELECT ea.idSalida FROM derivadosalmacenencabezado_salidas ea
            INNER JOIN derivadosalmacendetalle_salidas ad ON ad.idSalida = ea.idSalida
            WHERE ea.idReporteCarga = :idReporte AND ea.fecha = rd.fechaImpresion GROUP BY ea.idSalida) AS entradaProductosDerivados    
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
    $resultado['personalCargas'] = array();

    $sqlPersonalDescarga = "SELECT pd.idPersonalDescarga, pd.idPersonalOM,
    om.nombre FROM $personaldescarga_tabla pd
    LEFT JOIN personaloaxaca om ON om.idPersonalOM = pd.idPersonalOM
    WHERE idReporte = :idReporte";
    $datosPersonalDescarga = $con->prepare($sqlPersonalDescarga);
    $datosPersonalDescarga->bindParam(':idReporte', $idReporte);
    $datosPersonalDescarga->execute();
    if ($datosPersonalDescarga == FALSE) {
        throw new Exception($con->errorInfo());
    }

    foreach ($datosPersonalDescarga->fetchAll(PDO::FETCH_ASSOC) as $personal) {
        array_push($resultado['personalCargas'], $personal);
    }

    echo json_encode($resultado);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit();
}
