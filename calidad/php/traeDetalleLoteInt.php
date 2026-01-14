<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = new stdClass();
try {

    if (!isset($_GET['miel']) || !isset($_GET['idLoteInterno'])) {
        throw new Exception('No se recibieron datos');
    } else {
        $miel = $_GET['miel'];
        $idLoteInterno = $_GET['idLoteInterno'];
    }

    switch ($miel) {
        case '1':
            $calidad_tabla = 'calidad';
            $tamboreslotes_tabla = 'tamboreslotes';
            $almacen_tabla = 'almacen';
            $almacenencabezado_tabla = 'almacenencabezado';
            $laboratorio_tabla = 'laboratorio';
            $traspaso = 'almacentraspaso';
            $traspasoEncabezado = 'almacenencabezadotraspaso';
            break;
        case '2':
            $calidad_tabla = 'calidad_organico';
            $tamboreslotes_tabla = 'tamboreslotes_organico';
            $almacen_tabla = 'almacen_organico';
            $almacenencabezado_tabla = 'almacenencabezado_organico';
            $laboratorio_tabla = 'laboratorio_organico';
            $traspaso = 'almacentraspaso_organico';
            $traspasoEncabezado = 'almacenencabezadotraspaso_organico';
            break;
        case '5':
            $calidad_tabla = 'calidad_mantequilla';
            $tamboreslotes_tabla = 'tamboreslotes_mantequilla';
            $almacen_tabla = 'almacen_mantequilla';
            $almacenencabezado_tabla = 'almacenencabezado_mantequilla';
            $laboratorio_tabla = 'laboratorio_mantequilla';
            $traspaso = 'almacentraspaso_mantequilla';
            $traspasoEncabezado = 'almacenencabezadotraspaso_mantequilla';
            break;
        case '6':
            $calidad_tabla = 'calidad_altiplano';
            $tamboreslotes_tabla = 'tamboreslotes_altiplano';
            $almacen_tabla = 'almacen_altiplano';
            $almacenencabezado_tabla = 'almacenencabezado_altiplano';
            $laboratorio_tabla = 'laboratorio_altiplano';
            $traspaso = 'almacentraspaso_altiplano';
            $traspasoEncabezado = 'almacenencabezadotraspaso_altiplano';
            break;
        case '7':
            $calidad_tabla = 'calidad_naranjo';
            $tamboreslotes_tabla = 'tamboreslotes_naranjo';
            $almacen_tabla = 'almacen_naranjo';
            $almacenencabezado_tabla = 'almacenencabezado_naranjo';
            $laboratorio_tabla = 'laboratorio_naranjo';
            $traspaso = 'almacentraspaso_naranjo';
            $traspasoEncabezado = 'almacenencabezadotraspaso_naranjo';
            break;
        case '8':
            $calidad_tabla = 'calidad_aguacate';
            $tamboreslotes_tabla = 'tamboreslotes_aguacate';
            $almacen_tabla = 'almacen_aguacate';
            $almacenencabezado_tabla = 'almacenencabezado_aguacate';
            $laboratorio_tabla = 'laboratorio_aguacate';
            $traspaso = 'almacentraspaso_aguacate';
            $traspasoEncabezado = 'almacenencabezadotraspaso_aguacate';
            break;
        case '9':
            $calidad_tabla = 'calidad_mezquite';
            $tamboreslotes_tabla = 'tamboreslotes_mezquite';
            $almacen_tabla = 'almacen_mezquite';
            $almacenencabezado_tabla = 'almacenencabezado_mezquite';
            $laboratorio_tabla = 'laboratorio_mezquite';
            $traspaso = 'almacentraspaso_mezquite';
            $traspasoEncabezado = 'almacenencabezadotraspaso_mezquite';
            break;
    }

    $sqlEncabezado = "SELECT idLoteInterno, fechaProceso, fechaEnvasado, loteCliente,
    marcaFinalCliente, observaciones, muestraInterna, idLoteExperimental, numeroDeTambores,
    kilosTotales FROM $calidad_tabla WHERE idLoteInterno = :idLoteInterno";
    $datos = $con->prepare($sqlEncabezado);
    $datos->bindParam(':idLoteInterno', $idLoteInterno);
    $datos->execute();
    if ($datos == FALSE) {
        // throw new Exception($con->errorInfo());
        throw new Exception('Error');
    } else {
        $resultado = $datos->fetch(PDO::FETCH_ASSOC);
        if ($resultado == false) {
            throw new Exception('El lote es 0');
        }
        $resultado['informacionCalidad'] = array();

        if ($resultado['fechaProceso'] == "1969-12-31") {
            $resultado['fechaProceso'] = "Aún no asignada";
        }

        if ($resultado['fechaEnvasado'] == "1969-12-31") {
            $resultado['fechaEnvasado'] = "Aún no asignada";
        }
    }

    $sql = "SELECT tex.* FROM $tamboreslotes_tabla tex WHERE tex.idLoteInterno = :idLoteInterno";
    $sqlDetalle = $con->prepare($sql);
    $sqlDetalle->bindParam(':idLoteInterno', $idLoteInterno);
    $sqlDetalle->execute();

    if ($sqlDetalle == FALSE) {
        throw new Exception($con->errorInfo());
    } else {

        $resultado['informacionCalidad'] = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultado['informacionCalidad'] as $index => $e) {
            if ($e['tipo'] == "0") {
                $sql2 = "SELECT tex.folioTambor, tex.idLoteInterno, al.bruto, al.tara,
                al.neto, al.humedad, lab.color, lab.idFloracion, ale.fecha, ale.clasificacionMiel, pr.nombre, pr.idSagarpa, l.localidad, lab.porcentaje,
                lab.sfDescripcion, lab.stDescripcion, lab.adulteracionDescripcion, lab.hmf,
                lab.resultadoFinal, rs.resultado, rs.idResultadoFinal, tex.tipo,
                CASE WHEN al.referencia > 0 THEN al.referencia ELSE '---' END AS referencia 
                FROM $tamboreslotes_tabla tex
                INNER JOIN $almacen_tabla al ON al.idAlmacen = tex.folioTambor
                INNER JOIN $almacenencabezado_tabla ale ON ale.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = pr.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                LEFT JOIN  $laboratorio_tabla lab ON lab.idAlmacen = al.idAlmacen
                LEFT JOIN resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal
                WHERE tex.folioTambor = :folio AND tex.tipo = '0'
                ORDER BY tex.folioTambor ASC";
                $sqlDetalle2 = $con->prepare($sql2);
                $sqlDetalle2->bindParam(':folio', $e['folioTambor']);
                $sqlDetalle2->execute();
                $e2 = $sqlDetalle2->fetch(PDO::FETCH_ASSOC);
                $new = array_merge($e, $e2);
                $resultado['informacionCalidad'][$index] = $new;
            } else if ($e['tipo'] == "1") {
                $sql2 = "SELECT alms.bruto, alms.tara, alms.neto, alms.fecha, ls.adulteracionDescripcion, fl.floracion,
                ls.hmf, ls.idFloracion, rs.resultado, rs.idresultadoFinal, '---' AS idSagarpa, '---' AS localidad,
                s.nombre, ls.porcentaje, ls.sfDescripcion, ls.stDescripcion, tex.tipo
                FROM almacensobrantes alms
                LEFT JOIN $tamboreslotes_tabla tex ON tex.folioTambor = alms.consecutivo AND tex.tipo = '1' AND tex.clasificacion = :clasificacion
                LEFT JOIN sobrantes s ON s.idSobrante = alms.sobrante 
                LEFT JOIN laboratorio_sobrantes ls ON ls.idAlmacen = alms.consecutivo AND ls.sobrante = :clasificacion AND ls.tipoDeMiel = :miel
                LEFT JOIN floraciones fl ON fl.idFloracion = ls.idFloracion      
                LEFT JOIN resultadofinal rs ON rs.idresultadoFinal = ls.resultadoFinal              
                WHERE tex.tipo = '1' AND alms.consecutivo = :folio AND alms.sobrante = :clasificacion AND alms.tipoDeMiel = :miel
                ORDER BY tex.folioTambor ASC";
                $sqlDetalle2 = $con->prepare($sql2);
                $sqlDetalle2->bindParam(':folio', $e['folioTambor']);
                $sqlDetalle2->bindParam(':clasificacion', $e['clasificacion']);
                $sqlDetalle2->bindParam(':miel', $_GET['miel']);
                $sqlDetalle2->execute();
                $e2 = $sqlDetalle2->fetch(PDO::FETCH_ASSOC);
                $new = array_merge($e, $e2);
                $resultado['informacionCalidad'][$index] = $new;
            } else if ($e['tipo'] == "2") {
                $sql2 = "SELECT tex.folioTambor, tex.idLoteInterno, al.bruto, al.tara,
                al.neto, al.humedad, lab.color, lab.idFloracion, ale.fecha, pr.nombre, pr.idSagarpa, l.localidad, lab.porcentaje,
                lab.sfDescripcion, lab.stDescripcion, lab.adulteracionDescripcion, lab.hmf,
                lab.resultadoFinal, rs.resultado, rs.idResultadoFinal, tex.tipo 
                FROM $tamboreslotes_tabla tex
                INNER JOIN $traspaso al ON al.idAlmacen = tex.folioTambor
                INNER JOIN $traspasoEncabezado ale ON ale.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = pr.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                LEFT JOIN laboratorio_traspaso lab ON lab.idAlmacen = al.idAlmacen AND lab.tipoDeMiel = :miel
                LEFT JOIN resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal
                WHERE tex.folioTambor = :folio AND tex.tipo = '2'
                ORDER BY tex.folioTambor ASC";
                $sqlDetalle2 = $con->prepare($sql2);
                $sqlDetalle2->bindParam(':folio', $e['folioTambor']);
                $sqlDetalle2->bindParam(':miel', $_GET['miel']);
                $sqlDetalle2->execute();
                $e2 = $sqlDetalle2->fetch(PDO::FETCH_ASSOC);
                $new = array_merge($e, $e2);
                $resultado['informacionCalidad'][$index] = $new;
            }
        }
    }

    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => $resultado]);
    exit();
}
