<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
date_default_timezone_set('America/Merida');
$json = file_get_contents("php://input");

try {
    $con->beginTransaction();
    if (!$json) {
        throw new Exception('No se recibieron parámetros.');
    } else {
        $datos = json_decode($json);
        $info = $datos->valor;
        $idDescripcion = 0;
        $idCondicion = 0;
        $idPersonalD = 0;
        $idPersonal = 0;
    }

    switch ($info[0]->producto) {
        case '0':
            $entradaysalida_tabla = 'entradaysalida';
            $personaldescarga_tabla = 'personaldescarga';
            break;
        case '1':
            $entradaysalida_tabla = 'entradaysalida';
            $personaldescarga_tabla = 'personaldescarga';
            $almacenencabezado = 'almacenencabezado';
            $almacencera = 'almacenencabezadocera';
            $almacenCubetas = 'cubetasencabezado';
            $mp_encabezado = 'materiaprimaencabezadosalidas';
            break;
        case '2':
            $entradaysalida_tabla = 'entradaysalida_organico';
            $personaldescarga_tabla = 'personaldescarga_organico';
            $almacenencabezado = 'almacenencabezado_organico';
            $almacencera = 'almacenencabezadocera';
            $almacenCubetas = 'cubetasencabezado_organico';
            $mp_encabezado = 'materiaprimaencabezadosalidas_organico';
            break;
        case '5':
            $entradaysalida_tabla = 'entradaysalida_mantequilla';
            $personaldescarga_tabla = 'personaldescarga_mantequilla';
            $almacenencabezado = 'almacenencabezado_mantequilla';
            $almacencera = 'almacenencabezadocera';
            $almacenCubetas = 'cubetasencabezado_mantequilla';
            $mp_encabezado = 'materiaprimaencabezadosalidas_mantequilla';
            break;
        case '6':
            $entradaysalida_tabla = 'entradaysalida_altiplano';
            $personaldescarga_tabla = 'personaldescarga_altiplano';
            $almacenencabezado = 'almacenencabezado_altiplano';
            $almacencera = 'almacenencabezadocera';
            $almacenCubetas = 'cubetasencabezado_altiplano';
            $mp_encabezado = 'materiaprimaencabezadosalidas_altiplano';
            break;
        case '7':
            $entradaysalida_tabla = 'entradaysalida_naranjo';
            $personaldescarga_tabla = 'personaldescarga_naranjo';
            $almacenencabezado = 'almacenencabezado_naranjo';
            $almacencera = 'almacenencabezadocera';
            $almacenCubetas = 'cubetasencabezado_naranjo';
            $mp_encabezado = 'materiaprimaencabezadosalidas_naranjo';
            break;
        case '8':
            $entradaysalida_tabla = 'entradaysalida_aguacate';
            $personaldescarga_tabla = 'personaldescarga_aguacate';
            $almacenencabezado = 'almacenencabezado_aguacate';
            $almacencera = 'almacenencabezadocera';
            $almacenCubetas = 'cubetasencabezado_aguacate';
            $mp_encabezado = 'materiaprimaencabezadosalidas_aguacate';
            break;
        case '9':
            $entradaysalida_tabla = 'entradaysalida_mezquite';
            $personaldescarga_tabla = 'personaldescarga_mezquite';
            $almacenencabezado = 'almacenencabezado_mezquite';
            $almacencera = 'almacenencabezadocera';
            $almacenCubetas = 'cubetasencabezado_mezquite';
            $mp_encabezado = 'materiaprimaencabezadosalidas_mezquite';
            break;
        default:
            throw new Exception('El tipo de miel no es válido');
            break;
    }

    $sqlDescripciones = $con->prepare("INSERT INTO descripciones (producto, cantidad, pesoBruto, pesoTara, pesoNeto)
    VALUES (:producto, :cantidad,'0','0','0')");
    $sqlDescripciones->bindParam(':producto', $info[0]->producto);
    $sqlDescripciones->bindParam(':cantidad', $info[0]->cantidad);
    $sqlDescripciones->execute();
    if ($sqlDescripciones == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $idDescripcion = $con->lastInsertId();

    $sqlCondiciones = $con->prepare("INSERT INTO condicionesunidad (tipo, limpieza, materialExtrano, vehiculoAdecuado, cabello, unas, ropa)
                        VALUES (:tipo, :limpieza, :materialExtrano, :vehiculoAdecuado, :cabello, :unas, :ropa)");
    $sqlCondiciones->bindParam(':tipo', $info[0]->tipo);
    $sqlCondiciones->bindParam(':limpieza', $info[0]->limpieza);
    $sqlCondiciones->bindParam(':materialExtrano', $info[0]->materialExtrano);
    $sqlCondiciones->bindParam(':vehiculoAdecuado', $info[0]->vehiculoAdecuado);
    $sqlCondiciones->bindParam(':cabello', $info[0]->cabello);
    $sqlCondiciones->bindParam(':unas', $info[0]->unas);
    $sqlCondiciones->bindParam(':ropa', $info[0]->ropa);
    $sqlCondiciones->execute();
    if ($sqlCondiciones == FALSE) {
        throw new Exception($con->errorinfo());
    }
    $idCondicion = $con->lastInsertId();

    $sqlExternoTambor = $con->prepare("INSERT INTO externotambores (roto, abolladuras, recipienteAdecuado, lavadoExterior)
        VALUES (:roto, :abolladuras, :recipienteAdecuado, :lavadoExterior)");
    $sqlExternoTambor->bindParam(':roto', $info[0]->roto);
    $sqlExternoTambor->bindParam(':abolladuras', $info[0]->abolladuras);
    $sqlExternoTambor->bindParam(':recipienteAdecuado', $info[0]->recipienteAdecuado);
    $sqlExternoTambor->bindParam(':lavadoExterior', $info[0]->lavadoExterior);
    $sqlExternoTambor->execute();

    if ($sqlExternoTambor == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $idExternoTambor = $con->lastInsertId();

    $sqlPersonal = $con->prepare("INSERT INTO personalacciones (limpiezaPersonal, rotulacion, marcacion, montacargas)
        VALUES (:limpiezaPersonal, :rotulacion, :marcacion, :montacargas)");
    $sqlPersonal->bindParam(':limpiezaPersonal', $info[0]->limpiezaPersonal);
    $sqlPersonal->bindParam(':rotulacion', $info[0]->rotulacion);
    $sqlPersonal->bindParam(':marcacion', $info[0]->marcacion);
    $sqlPersonal->bindParam(':montacargas', $info[0]->montacargas);
    $sqlPersonal->execute();

    if ($sqlPersonal == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $idPersonal = $con->lastInsertId();

    $sqlReporteDescarga = $con->prepare("INSERT INTO $entradaysalida_tabla (fechaImpresion, horaInicio, responsable, 
    supervisor, idOperador, idPlaca, idDescripcion, idExternoTambor, idCondicion, contenedor, sello, lote, idPersonal,
    observaciones, horaFinal, estado, idLoteInterno, idClasificacion, miel, cera, apicolas, tambor, cubeta, clasificacionMiel, mp, envasesFrascos, productosDerivados, folioCotizacion)
    VALUES (:fechaImpresion, :horaInicio, :responsable, :supervisor, 
    :idOperador, :idPlaca, :idDescripcion, :idExternoTambor, :idCondicion,
    :contenedor, :sello  ,:lote, :idPersonal,:observaciones, 
    :horaFinal, '1', :idLoteInterno, :idClasificacion, :miel, :cera, :apicolas, :tambor, :cubeta, :clasificacionMiel, :mp, :envasesFrascos, :productosDerivados, :folioCotizacion)");
    $sqlReporteDescarga->bindParam(':fechaImpresion', $info[0]->fechaImpresion);
    $sqlReporteDescarga->bindParam(':horaInicio', $info[0]->horaInicio);
    $sqlReporteDescarga->bindParam(':responsable', $info[0]->responsable);
    $sqlReporteDescarga->bindParam(':supervisor', $info[0]->supervisor);
    $sqlReporteDescarga->bindParam(':idOperador', $info[0]->idOperador);
    $sqlReporteDescarga->bindParam(':idPlaca', $info[0]->idPlaca);
    $sqlReporteDescarga->bindParam(':contenedor', $info[0]->contenedor);
    $sqlReporteDescarga->bindParam(':sello', $info[0]->sello);
    $sqlReporteDescarga->bindParam(':lote', $info[0]->lote);
    $sqlReporteDescarga->bindParam(':observaciones', $info[0]->observaciones);
    $sqlReporteDescarga->bindParam(':horaFinal', $info[0]->horaFinal);
    $sqlReporteDescarga->bindParam(':idLoteInterno', $info[0]->idLoteInterno);
    $sqlReporteDescarga->bindParam(':idClasificacion', $info[0]->idClasificacion);
    $sqlReporteDescarga->bindParam(':idDescripcion', $idDescripcion);
    $sqlReporteDescarga->bindParam(':idExternoTambor', $idExternoTambor);
    $sqlReporteDescarga->bindParam(':idCondicion', $idCondicion);
    $sqlReporteDescarga->bindParam(':idPersonal', $idPersonal);
    $sqlReporteDescarga->bindParam(':miel', $info[0]->miel);
    $sqlReporteDescarga->bindParam(':cera', $info[0]->cera);
    $sqlReporteDescarga->bindParam(':apicolas', $info[0]->apicolas);
    $sqlReporteDescarga->bindParam(':tambor', $info[0]->tambor);
    $sqlReporteDescarga->bindParam(':cubeta', $info[0]->cubeta);
    $sqlReporteDescarga->bindParam(':clasificacionMiel', $info[0]->clasificacionMiel);
    $sqlReporteDescarga->bindParam(':mp', $info[0]->mp);
    $sqlReporteDescarga->bindParam(':envasesFrascos', $info[0]->envasesFrascos);
    $sqlReporteDescarga->bindParam(':productosDerivados', $info[0]->productosDerivados);
    $sqlReporteDescarga->bindParam(':folioCotizacion', $info[0]->folioCotizacion);
    $sqlReporteDescarga->execute();
    if ($sqlReporteDescarga == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $idReporteCarga = $con->lastInsertId();

    foreach ($info[1] as $personalDescarga) {
        $sqlPersonalDescarga = $con->prepare("INSERT INTO $personaldescarga_tabla (idPersonalOM, idReporte) VALUES (:personal, :reporte)");
        $sqlPersonalDescarga->bindParam(':personal', $personalDescarga);
        $sqlPersonalDescarga->bindParam(':reporte', $idReporteCarga);
        $sqlPersonalDescarga->execute();
        if ($sqlPersonalDescarga == FALSE) {
            throw new Exception($con->errorInfo());
        }
    }


    if ($info[0]->miel == '1') {

        $sqlLista = $con->prepare("INSERT INTO listadepesos (lote, fecha, totalBruto, totalTara, totalNeto, 
        destino, tipoMiel, mielHomogeneizada, tipoDeCliente, idCliente, idReporteCarga) 
        VALUES (:lote, '0000-00-00', '0', '0', '0', '',
        :tipoMiel, '', '', '', :idReporteCarga)");
        $sqlLista->bindParam(':lote', $info[0]->lote);
        $sqlLista->bindParam(':tipoMiel', $info[0]->producto);
        $sqlLista->bindParam(':idReporteCarga', $idReporteCarga);
        $sqlLista->execute();
        if ($sqlLista == false) {
            throw new Exception($con->errorInfo());
        }
    }
    if ($info[0]->cera == '1') {
        $sqlCera = $con->prepare("INSERT INTO $almacencera (fecha, tipoPersona, idProveedor, folio, total, kg, tipo, tipoCera, idReporteDescarga) VALUES (:fecha, '0', '0', '0', '0', '0', '2', :tipoCera, :idReporteDescarga)");
        $sqlCera->bindParam(':fecha', $info[0]->fechaImpresion);
        $sqlCera->bindParam(':tipoCera', $info[0]->producto);
        $sqlCera->bindParam(':idReporteDescarga', $idReporteCarga);
        $sqlCera->execute();
        if ($sqlCera == FALSE) {
            throw new Exception($con->errorInfo());
        }
    }
    if ($info[0]->apicolas == '1') {
        $sqlApicola = $con->prepare("INSERT INTO almacenencabezadoapicola (fecha, tipoPersona, idProveedor, folio, total, tipo, idReporteDescarga) VALUES (:fecha, '0', '0', '0', '0', '2', :idReporteDescarga)");
        $sqlApicola->bindParam(':fecha', $info[0]->fechaImpresion);
        // $sqlApicola->bindParam(':producto', $info[0]->producto);
        $sqlApicola->bindParam(':idReporteDescarga', $idReporteCarga);
        $sqlApicola->execute();
        if ($sqlApicola == FALSE) {
            throw new Exception($con->errorInfo());
        }
    }
    if ($info[0]->mp == '1') {
        $sqlMp = $con->prepare("INSERT INTO $mp_encabezado (fecha, idProveedor, idMotivo, cantidadTotal, importeTotal, tipoCliente, idReporteCarga) VALUES (:fecha, '0', '0', '0', '0.00', '0', :idReporteCarga)");
        $sqlMp->bindParam(':fecha', $info[0]->fechaImpresion);
        $sqlMp->bindParam(':idReporteCarga', $idReporteCarga);
        $sqlMp->execute();
        if ($sqlMp == FALSE) {
            throw new Exception($con->errorInfo());
        }
    }

    if ($info[0]->envasesFrascos == '1') {
        $sqlMp = $con->prepare("INSERT INTO envasesfrascosencabezadosalidas (fecha, idProveedor, cantidadTotal, importeTotal, tipoCliente, idReporteCarga) VALUES (:fecha, '0', '0', '0.00', '0', :idReporteCarga)");
        $sqlMp->bindParam(':fecha', $info[0]->fechaImpresion);
        $sqlMp->bindParam(':idReporteCarga', $idReporteCarga);
        $sqlMp->execute();
        if ($sqlMp == FALSE) {
            throw new Exception($con->errorInfo());
        }
    }

    if ($info[0]->productosDerivados == '1') {
        $sqlApicola = $con->prepare("INSERT INTO derivadosalmacenencabezado_salidas (fecha, tipoPersona, idProveedor, folio, total, tipo, idReporteCarga) VALUES (:fecha, '0', '0', '0', '0', '2', :idReporteCarga)");
        $sqlApicola->bindParam(':fecha', $info[0]->fechaImpresion);
        // $sqlApicola->bindParam(':producto', $info[0]->producto);
        $sqlApicola->bindParam(':idReporteCarga', $idReporteCarga);
        $sqlApicola->execute();
        if ($sqlApicola == FALSE) {
            throw new Exception($con->errorInfo());
        }
    }
    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Nueva Entrada Registrada', 'swal' => 'success']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine(), 'swal' => 'error']);
    exit();
}
