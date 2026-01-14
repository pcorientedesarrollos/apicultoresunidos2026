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
            $mp_encabezado = 'materiaprimaencabezadoentradas';
            $traspaso_encabezado = 'almacenencabezadotraspaso';
            break;
        case '2':
            $entradaysalida_tabla = 'entradaysalida_organico';
            $personaldescarga_tabla = 'personaldescarga_organico';
            $almacenencabezado = 'almacenencabezado_organico';
            $almacencera = 'almacenencabezadocera';
            $almacenCubetas = 'cubetasencabezado_organico';
            $mp_encabezado = 'materiaprimaencabezadoentradas_organico';
            $traspaso_encabezado = 'almacenencabezadotraspaso_organico';
            break;
        case '5':
            $entradaysalida_tabla = 'entradaysalida_mantequilla';
            $personaldescarga_tabla = 'personaldescarga_mantequilla';
            $almacenencabezado = 'almacenencabezado_mantequilla';
            $almacencera = 'almacenencabezadocera';
            $almacenCubetas = 'cubetasencabezado_mantequilla';
            $mp_encabezado = 'materiaprimaencabezadoentradas_mantequilla';
            $traspaso_encabezado = 'almacenencabezadotraspaso_mantequilla';
            break;
        case '6':
            $entradaysalida_tabla = 'entradaysalida_altiplano';
            $personaldescarga_tabla = 'personaldescarga_altiplano';
            $almacenencabezado = 'almacenencabezado_altiplano';
            $almacencera = 'almacenencabezadocera';
            $almacenCubetas = 'cubetasencabezado_altiplano';
            $mp_encabezado = 'materiaprimaencabezadoentradas_altiplano';
            $traspaso_encabezado = 'almacenencabezadotraspaso_altiplano';
            break;
        case '7':
            $entradaysalida_tabla = 'entradaysalida_naranjo';
            $personaldescarga_tabla = 'personaldescarga_naranjo';
            $almacenencabezado = 'almacenencabezado_naranjo';
            $almacencera = 'almacenencabezadocera';
            $almacenCubetas = 'cubetasencabezado_naranjo';
            $mp_encabezado = 'materiaprimaencabezadoentradas_naranjo';
            $traspaso_encabezado = 'almacenencabezadotraspaso_naranjo';
            break;
        case '8':
            $entradaysalida_tabla = 'entradaysalida_aguacate';
            $personaldescarga_tabla = 'personaldescarga_aguacate';
            $almacenencabezado = 'almacenencabezado_aguacate';
            $almacencera = 'almacenencabezadocera';
            $almacenCubetas = 'cubetasencabezado_aguacate';
            $mp_encabezado = 'materiaprimaencabezadoentradas_aguacate';
            $traspaso_encabezado = 'almacenencabezadotraspaso_aguacate';
            break;
        case '9':
            $entradaysalida_tabla = 'entradaysalida_mezquite';
            $personaldescarga_tabla = 'personaldescarga_mezquite';
            $almacenencabezado = 'almacenencabezado_mezquite';
            $almacencera = 'almacenencabezadocera';
            $almacenCubetas = 'cubetasencabezado_mezquite';
            $mp_encabezado = 'materiaprimaencabezadoentradas_mezquite';
            $traspaso_encabezado = 'almacenencabezadotraspaso_mezquite';
            break;
        default:
            throw new Exception('El tipo de miel no es válido');
            break;
    }

    $sqlDescripciones = $con->prepare("INSERT INTO descripciones (producto, cantidad, pesoBruto, pesoTara, pesoNeto)
                                    VALUES (:producto,'0','0','0','0')");
    $sqlDescripciones->bindParam(':producto', $info[0]->producto);
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
        throw new Exception($con->errorInfo());
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
                                VALUES (:limpiezaPersonal, '', :marcacion, '')");
    $sqlPersonal->bindParam(':limpiezaPersonal', $info[0]->limpiezaPersonal);
    $sqlPersonal->bindParam(':marcacion', $info[0]->marcacion);
    $sqlPersonal->execute();
    if ($sqlPersonal == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $idPersonal = $con->lastInsertId();

    $sqlReporteDescarga = $con->prepare("INSERT INTO $entradaysalida_tabla (fechaImpresion, horaInicio, responsable, supervisor, idOperador, clasificacionMiel,
                                        idPlaca, idDescripcion, idExternoTambor, idCondicion, contenedor, sello, lote, idPersonal, observaciones, horaFinal, estado, idLoteInterno, miel, cera, apicolas, tambor, cubeta, mp, traspaso, envasesFrascos, productosDerivados)
                                        VALUES (:fechaImpresion, :horaInicio, :responsable, :supervisor, 
                                        :idOperador, :clasificacionMiel , :idPlaca, :idDescripcion, :idExternoTambor, :idCondicion,
                                        :contenedor, :sello  ,:lote, :idPersonal,:observaciones, 
                                        :horaFinal, '0', '0', :miel, :cera, :apicolas, :tambor, :cubeta, :mp, :traspaso, :envasesFrascos, :productosDerivados)");
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
    $sqlReporteDescarga->bindParam(':idDescripcion', $idDescripcion);
    $sqlReporteDescarga->bindParam(':idExternoTambor', $idExternoTambor);
    $sqlReporteDescarga->bindParam(':idCondicion', $idCondicion);
    $sqlReporteDescarga->bindParam(':idPersonal', $idPersonal);
    $sqlReporteDescarga->bindParam(':miel', $info[0]->miel);
    $sqlReporteDescarga->bindParam(':cera', $info[0]->cera);
    $sqlReporteDescarga->bindParam(':apicolas', $info[0]->apicolas);
    $sqlReporteDescarga->bindParam(':tambor', $info[0]->tambor);
    $sqlReporteDescarga->bindParam(':cubeta', $info[0]->cubeta);
    $sqlReporteDescarga->bindParam(':mp', $info[0]->mp);
    $sqlReporteDescarga->bindParam(':traspaso', $info[0]->traspaso);
    $sqlReporteDescarga->bindParam(':clasificacionMiel', $info[0]->clasificacionMiel);
    $sqlReporteDescarga->bindParam(':envasesFrascos', $info[0]->envasesFrascos);
    $sqlReporteDescarga->bindParam(':productosDerivados', $info[0]->productosDerivados);
    $sqlReporteDescarga->execute();
    if ($sqlReporteDescarga == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $idReporteDescarga = $con->lastInsertId();

    foreach ($info[1] as $personalDescarga) {
        $sqlPersonalDescarga = $con->prepare("INSERT INTO $personaldescarga_tabla (idPersonalOM, idReporte) VALUES (:personalDescarga, :idReporteDescarga)");
        $sqlPersonalDescarga->bindParam(':personalDescarga', $personalDescarga);
        $sqlPersonalDescarga->bindParam(':idReporteDescarga', $idReporteDescarga);
        $sqlPersonalDescarga->execute();
        if ($sqlPersonalDescarga == FALSE) {
            throw new Exception($con->errorInfo());
        }
    }

    if ($info[0]->miel == '1') {
        if ($info[0]->tambor == '1') {
            $sqlAlmacen = $con->prepare("INSERT INTO $almacenencabezado (fecha, idProveedor, folio, totalCompra, idReporteDescarga, clasificacionMiel) VALUES (:fecha, '0', '0', '0.00', :idReporteDescarga, :clasificacionMiel)");
            $sqlAlmacen->bindParam(':fecha', $info[0]->fechaImpresion);
            $sqlAlmacen->bindParam(':clasificacionMiel', $info[0]->clasificacionMiel);
            $sqlAlmacen->bindParam(':idReporteDescarga', $idReporteDescarga);
            $sqlAlmacen->execute();
            if ($sqlAlmacen == FALSE) {
                throw new Exception($con->errorInfo());
            }
            $idAlmacenEncabezado = $con->lastInsertId();
            $sqlUpdate = $con->prepare("UPDATE $almacenencabezado set folio = :folio WHERE idAlmacen = :idAlmacen");
            $sqlUpdate->bindParam(':folio', $idAlmacenEncabezado);
            $sqlUpdate->bindParam(':idAlmacen', $idAlmacenEncabezado);
            $sqlUpdate->execute();
            if ($sqlUpdate == false) {
                throw new Exception($con->errorInfo());
            }
        }
        if ($info[0]->cubeta == '1') {
            $sqlAlmacen = $con->prepare("INSERT INTO $almacenCubetas (fecha, idProveedor, totalCompra, folioEntradaTambor, idReporteDescarga) VALUES (:fecha, '0', '0.00', '99999', :idReporteDescarga)");
            $sqlAlmacen->bindParam(':fecha', $info[0]->fechaImpresion);
            $sqlAlmacen->bindParam(':idReporteDescarga', $idReporteDescarga);
            $sqlAlmacen->execute();
            if ($sqlAlmacen == FALSE) {
                throw new Exception($con->errorInfo());
            }
        }
    }
    if ($info[0]->cera == '1') {
        $sqlCera = $con->prepare("INSERT INTO $almacencera (fecha, tipoPersona, idProveedor, folio, total, kg, tipo, tipoCera, idReporteDescarga) VALUES (:fecha, '0', '0', '0', '0', '0', '1', :tipoCera, :idReporteDescarga)");
        $sqlCera->bindParam(':fecha', $info[0]->fechaImpresion);
        $sqlCera->bindParam(':tipoCera', $info[0]->producto);
        $sqlCera->bindParam(':idReporteDescarga', $idReporteDescarga);
        $sqlCera->execute();
        if ($sqlCera == FALSE) {
            throw new Exception($con->errorInfo());
        }
    }
    if ($info[0]->apicolas == '1') {
        $sqlApicola = $con->prepare("INSERT INTO almacenencabezadoapicola (fecha, tipoPersona, idProveedor, folio, total, tipo, idReporteDescarga) VALUES (:fecha, '0', '0', '0', '0', '1', :idReporteDescarga)");
        $sqlApicola->bindParam(':fecha', $info[0]->fechaImpresion);
        $sqlApicola->bindParam(':idReporteDescarga', $idReporteDescarga);
        $sqlApicola->execute();
        if ($sqlApicola == FALSE) {
            throw new Exception($con->errorInfo());
        }
    }
    if ($info[0]->mp == '1') {
        $sqlMp = $con->prepare("INSERT INTO $mp_encabezado (fecha, idProveedor, idMotivo, cantidadTotal, importeTotal, tipoCliente, idReporteDescarga) VALUES (:fecha, '0', '0', '0', '0.00', '0', :idReporteDescarga)");
        $sqlMp->bindParam(':fecha', $info[0]->fechaImpresion);
        $sqlMp->bindParam(':idReporteDescarga', $idReporteDescarga);
        $sqlMp->execute();
        if ($sqlMp == FALSE) {
            throw new Exception($con->errorInfo());
        }
    }
    if ($info[0]->traspaso == '1') {
        $sqlTraspaso = $con->prepare("INSERT INTO $traspaso_encabezado (fecha, idProveedor, folio, totalCompra, idReporteDescarga) VALUES (:fecha, '0', '0', '0.00', :idReporteDescarga)");
        $sqlTraspaso->bindParam(':fecha', $info[0]->fechaImpresion);
        $sqlTraspaso->bindParam(':idReporteDescarga', $idReporteDescarga);
        $sqlTraspaso->execute();
        if ($sqlTraspaso == FALSE) {
            throw new Exception($con->errorInfo());
        }
        $idTraspaso = $con->lastInsertId();
        $sqlUpdate = $con->prepare("UPDATE $traspaso_encabezado set folio = :folio WHERE idAlmacen = :idAlmacen");
        $sqlUpdate->bindParam(':folio', $idTraspaso);
        $sqlUpdate->bindParam(':idAlmacen', $idTraspaso);
        $sqlUpdate->execute();
        if ($sqlUpdate == false) {
            throw new Exception($con->errorInfo());
        }
    }
    if ($info[0]->envasesFrascos == '1') {
        $sqlMp = $con->prepare("INSERT INTO envasesfrascosencabezadoentradas (fecha, idProveedor, cantidadTotal, importeTotal, tipoCliente, idReporteDescarga) VALUES (:fecha, '0', '0', '0.00', '0', :idReporteDescarga)");
        $sqlMp->bindParam(':fecha', $info[0]->fechaImpresion);
        $sqlMp->bindParam(':idReporteDescarga', $idReporteDescarga);
        $sqlMp->execute();
        if ($sqlMp == FALSE) {
            throw new Exception($con->errorInfo());
        }
    }
    if ($info[0]->productosDerivados == '1') {
        $sqlMp = $con->prepare("INSERT INTO derivadosalmacenencabezado (fecha, tipoPersona, idProveedor, folio, total, tipo, idReporteDescarga) VALUES (:fecha, '0', '0', '0', '0', '1', :idReporteDescarga)");
        $sqlMp->bindParam(':fecha', $info[0]->fechaImpresion);
        $sqlMp->bindParam(':idReporteDescarga', $idReporteDescarga);
        $sqlMp->execute();
        if ($sqlMp == FALSE) {
            throw new Exception($con->errorInfo());
        }
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha guardado el registro']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line: ' . $e->getLine()]);
    exit();
}
