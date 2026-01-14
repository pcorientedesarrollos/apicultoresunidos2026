<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = new stdClass();
try {

    if (!isset($_GET['miel']) && !isset($_GET['idLoteExperimental'])) {
        throw new Exception('No se recibieron datos');
    } else {
        $miel = $_GET['miel'];
        $idLoteExperimental = $_GET['idLoteExperimental'];
    }
    $totalNeto = 0;
    $totalTambos = 0;

    switch ($_GET['miel']) {
        case '1':
            $experimental = 'experimental';
            $tambores = 'tamboresexperimentales';
            $almacen = 'almacen';
            $almacenEncabezado = 'almacenencabezado';
            $traspaso = 'almacentraspaso';
            $traspasoEncabezado = 'almacenencabezadotraspaso';
            $contrato = 'lotescontratados';
            $laboratorio = 'laboratorio';
            break;
        case '2':
            $experimental = 'experimental_organico';
            $tambores = 'tamboresexperimentales_organico';
            $almacen = 'almacen_organico';
            $almacenEncabezado = 'almacenencabezado_organico';
            $traspaso = 'almacentraspaso_organico';
            $traspasoEncabezado = 'almacenencabezadotraspaso_organico';
            $contrato = 'lotescontratados';
            $laboratorio = 'laboratorio_organico';
            break;
        case '5':
            $experimental = 'experimental_mantequilla';
            $tambores = 'tamboresexperimentales_mantequilla';
            $almacen = 'almacen_mantequilla';
            $almacenEncabezado = 'almacenencabezado_mantequilla';
            $traspaso = 'almacentraspaso_mantequilla';
            $traspasoEncabezado = 'almacenencabezadotraspaso_mantequilla';
            $contrato = 'lotescontratados';
            $laboratorio = 'laboratorio_mantequilla';
            break;
        case '6':
            $experimental = 'experimental_altiplano';
            $tambores = 'tamboresexperimentales_altiplano';
            $almacen = 'almacen_altiplano';
            $almacenEncabezado = 'almacenencabezado_altiplano';
            $traspaso = 'almacentraspaso_altiplano';
            $traspasoEncabezado = 'almacenencabezadotraspaso_altiplano';
            $contrato = 'lotescontratados';
            $laboratorio = 'laboratorio_altiplano';
            break;
        case '7':
            $experimental = 'experimental_naranjo';
            $tambores = 'tamboresexperimentales_naranjo';
            $almacen = 'almacen_naranjo';
            $almacenEncabezado = 'almacenencabezado_naranjo';
            $traspaso = 'almacentraspaso_naranjo';
            $traspasoEncabezado = 'almacenencabezadotraspaso_naranjo';
            $contrato = 'lotescontratados';
            $laboratorio = 'laboratorio_naranjo';
            break;
        case '8':
            $experimental = 'experimental_aguacate';
            $tambores = 'tamboresexperimentales_aguacate';
            $almacen = 'almacen_aguacate';
            $almacenEncabezado = 'almacenencabezado_aguacate';
            $traspaso = 'almacentraspaso_aguacate';
            $traspasoEncabezado = 'almacenencabezadotraspaso_aguacate';
            $contrato = 'lotescontratados';
            $laboratorio = 'laboratorio_aguacate';
            break;
        case '9':
            $experimental = 'experimental_mezquite';
            $tambores = 'tamboresexperimentales_mezquite';
            $almacen = 'almacen_mezquite';
            $almacenEncabezado = 'almacenencabezado_mezquite';
            $traspaso = 'almacentraspaso_mezquite';
            $traspasoEncabezado = 'almacenencabezadotraspaso_mezquite';
            $contrato = 'lotescontratados';
            $laboratorio = 'laboratorio_mezquite';
            break;
    }

    $sql = "SELECT ex.idLoteExperimental, ex.fecha, ex.humedad, ex.numeroDeTambores,
            ex.kilosTotales, ex.resultadoLaboratorio, ex.loteInterno,
            rf.idresultadoFinal, rf.resultado, c.contrato, ex.numContrato, ex.contrato AS existe
            FROM $experimental ex
            LEFT JOIN resultadofinal rf ON rf.idresultadoFinal = ex.resultadoLaboratorio
            LEFT JOIN $contrato c ON c.idLoteContratado = ex.numContrato
            WHERE ex.idLoteExperimental = :idLoteExperimental";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idLoteExperimental', $idLoteExperimental);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $resultado = $datos->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT tex.*, tex.folioTambor AS folio, tex.clasificacion AS sobrante, CASE WHEN tex.clasificacion > 0 THEN 2 ELSE 1 END AS almacen FROM $tambores tex WHERE tex.idLoteExperimental = :idLoteExperimental";
    $sqlDetalle = $con->prepare($sql);
    $sqlDetalle->bindParam(':idLoteExperimental', $idLoteExperimental);
    $sqlDetalle->execute();
    $totalTambos += $sqlDetalle->rowCount();

    if ($sqlDetalle == FALSE) {
        throw new Exception($con->errorInfo());
    } else {

        $resultado['experimental'] = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultado['experimental'] as $index => $e) {
            if ($e['tipo'] == "0") {
                $sql2 = "SELECT al.bruto, al.tara,
                            al.neto, ale.fecha, ale.clasificacionMiel, pr.nombre, pr.idSagarpa, l.localidad,
                            lab.porcentaje, lab.sfDescripcion, lab.stDescripcion, 
                            lab.adulteracionDescripcion, lab.hmf,
                            lab.resultadoFinal, rs.resultado, rs.idresultadoFinal,
                            fl.idFloracion, fl.floracion, tex.tipo,
                            CASE WHEN al.referencia > 0 THEN al.referencia ELSE '---' END AS referencia
                        FROM $almacen al 
                        INNER JOIN $tambores tex ON al.idAlmacen = tex.folioTambor
                        INNER JOIN $almacenEncabezado ale ON ale.idAlmacen = al.idAlmacenEncabezado
                        LEFT JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
                        LEFT JOIN direccion d ON d.idDireccion = pr.idDireccion
                        LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                        LEFT JOIN $laboratorio lab ON lab.idAlmacen = al.idAlmacen
                        LEFT JOIN resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal
                        LEFT JOIN floraciones fl ON fl.idFloracion = lab.idFloracion
                        WHERE tex.folioTambor = :folio AND tex.tipo = '0'
                        ORDER BY tex.folioTambor ASC";
                $sqlDetalle2 = $con->prepare($sql2);
                $sqlDetalle2->bindParam(':folio', $e['folioTambor']);
                $sqlDetalle2->execute();
                $e2 = $sqlDetalle2->fetch(PDO::FETCH_ASSOC);
                $totalNeto += $e2['neto'];
                $new = array_merge($e, $e2);
                $resultado['experimental'][$index] = $new;
            } else if ($e['tipo'] == "1") {
                $sql2 = "SELECT alms.bruto, alms.tara, alms.neto, alms.fecha, ls.adulteracionDescripcion, fl.floracion,
                ls.hmf, ls.idFloracion, rs.resultado, rs.idresultadoFinal, '---' AS idSagarpa, '---' AS localidad,
                s.nombre, ls.porcentaje, ls.sfDescripcion, ls.stDescripcion, tex.tipo
                FROM almacensobrantes alms
                LEFT JOIN $tambores tex ON tex.folioTambor = alms.consecutivo AND tex.tipo = '1' AND tex.clasificacion = :clasificacion
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
                $totalNeto += $e2['neto'];
                $new = array_merge($e, $e2);
                $resultado['experimental'][$index] = $new;
            } else if($e['tipo'] == "2") {
                $sql2 = "SELECT al.bruto, al.tara,
                            al.neto, ale.fecha, pr.nombre, pr.idSagarpa, l.localidad,
                            lab.porcentaje, lab.sfDescripcion, lab.stDescripcion, 
                            lab.adulteracionDescripcion, lab.hmf,
                            lab.resultadoFinal, rs.resultado, rs.idresultadoFinal,
                            fl.idFloracion, fl.floracion, tex.tipo
                        FROM $traspaso al 
                        INNER JOIN $tambores tex ON al.idAlmacen = tex.folioTambor
                        INNER JOIN $traspasoEncabezado ale ON ale.idAlmacen = al.idAlmacenEncabezado
                        LEFT JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
                        LEFT JOIN direccion d ON d.idDireccion = pr.idDireccion
                        LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                        INNER JOIN laboratorio_traspaso lab ON lab.idAlmacen = al.idAlmacen AND lab.tipoDeMiel = :miel
                        LEFT JOIN resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal
                        LEFT JOIN floraciones fl ON fl.idFloracion = lab.idFloracion
                        WHERE tex.folioTambor = :folio AND tex.tipo = '2'
                        ORDER BY tex.folioTambor ASC";
                $sqlDetalle2 = $con->prepare($sql2);
                $sqlDetalle2->bindParam(':folio', $e['folioTambor']);
                $sqlDetalle2->bindParam(':miel', $_GET['miel']);
                $sqlDetalle2->execute();
                $e2 = $sqlDetalle2->fetch(PDO::FETCH_ASSOC);
                $totalNeto += $e2['neto'];
                $new = array_merge($e, $e2);
                $resultado['experimental'][$index] = $new;
            }
        }
        $resultado['totalKilos'] = $totalNeto;
        $resultado['totalTambores'] = $totalTambos;
    }

    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => $resultado]);
    exit();
}
