<?php

if (isset($_GET['tmp']) && isset($_GET['idLoteExp'])) {
    include_once '../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $conexion = $pdo->conectar();
    $resultado = new stdClass();
    $miel = $_GET["tmp"];
    $idLoteExp = $_GET["idLoteExp"];
    if ($miel =='1') {
        $nombreMiel = "MIEL 100% PURA DE ABEJA";
        $experimental = 'experimental';
        $tambores = 'tamboresexperimentales';
        $almacen = 'almacen';
        $almacenEncabezado = 'almacenencabezado';
    } else if ($miel == '5') {
        $nombreMiel = "MIEL 100% MANTEQUILLA";
        $experimental = 'experimental_mantequilla';
        $tambores = 'tamboresexperimentales_mantequilla';
        $almacen = 'almacen_mantequilla';
        $almacenEncabezado = 'almacenencabezado_mantequilla';
    } else if ($miel == '6') {
        $nombreMiel = "MIEL 100% ALTIPLANO";
        $experimental = 'experimental_altiplano';
        $tambores = 'tamboresexperimentales_altiplano';
        $almacen = 'almacen_altiplano';
        $almacenEncabezado = 'almacenencabezado_altiplano';
    } else if ($miel == '7') {
        $nombreMiel = "MIEL 100% NARANJO";
        $experimental = 'experimental_naranjo';
        $tambores = 'tamboresexperimentales_naranjo';
        $almacen = 'almacen_naranjo';
        $almacenEncabezado = 'almacenencabezado_naranjo';
    }  else if ($miel == '8') {
        $nombreMiel = "MIEL 100% AGUACATE";
        $experimental = 'experimental_aguacate';
        $tambores = 'tamboresexperimentales_aguacate';
        $almacen = 'almacen_aguacate';
        $almacenEncabezado = 'almacenencabezado_aguacate';
    } else if ($miel == '9') {
        $nombreMiel = "MIEL 100% MEZQUITE";
        $experimental = 'experimental_mezquite';
        $tambores = 'tamboresexperimentales_mezquite';
        $almacen = 'almacen_mezquite';
        $almacenEncabezado = 'almacenencabezado_mezquite';
    }  else {
        $nombreMiel = "MIEL 100% ORGANICA";
        $experimental = 'experimental_organico';
        $tambores = 'tamboresexperimentales_organico';
        $almacen = 'almacen_organico';
        $almacenEncabezado = 'almacenencabezado_organico';
    }
    $fechaReporte = date("d-m-Y");

    $totalNeto = 0;
    $totalTambos = 0;

    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Conformacion lote experimental_$fechaReporte.xls");
    header("Prafma: no-cache");
    header("Expires:0");

    $sqlEncabezado = "SELECT ex.idLoteExperimental, ex.fecha, ex.humedad, ex.numeroDeTambores,
    ex.kilosTotales, ex.resultadoLaboratorio, ex.loteInterno
    FROM $experimental ex
    WHERE ex.idLoteExperimental = :idLoteExp";
    $queryEncabezado = $conexion->prepare($sqlEncabezado);
    $queryEncabezado->bindParam(':idLoteExp', $idLoteExp);
    $queryEncabezado->execute();
    $resultado = $queryEncabezado->fetch(PDO::FETCH_ASSOC);

    switch ($resultado['resultadoLaboratorio']) {
        case '0':
            $resultado['resultadoLaboratorio'] = " ";
            break;
        case '1':
            $resultado['resultadoLaboratorio'] = "EXPORTACION";
            break;
        case '2':
            $resultado['resultadoLaboratorio'] = "NACIONAL";
            break;
        case '3':
            $resultado['resultadoLaboratorio'] = "INVENTARIO ANTERIOR";
            break;
        default:
            echo "";
            break;
    }


    $sql = "SELECT tex.* FROM $tambores tex WHERE tex.idLoteExperimental = :idLoteExperimental";
    $sqlDetalle = $conexion->prepare($sql);
    $sqlDetalle->bindParam(':idLoteExperimental', $idLoteExp);
    $sqlDetalle->execute();
    $totalTambos += $sqlDetalle->rowCount();

    $resultado['experimental']= $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);

    foreach ($resultado['experimental'] as $index => $e) {
        if($e['tipo'] == "0"){
            $sql2 = "SELECT al.bruto, al.tara,
                        al.neto, ale.fecha, pr.nombre, pr.idSagarpa, l.localidad,
                        lab.porcentaje, lab.sfDescripcion, lab.stDescripcion, 
                        lab.adulteracionDescripcion, lab.hmf,
                        lab.resultadoFinal, rs.resultado, rs.idresultadoFinal,
                        fl.idFloracion, fl.floracion
                    FROM $almacen al 
                    INNER JOIN $tambores tex ON al.idAlmacen = tex.folioTambor
                    INNER JOIN $almacenEncabezado ale ON ale.idAlmacen = al.idAlmacenEncabezado
                    LEFT JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
                    LEFT JOIN direccion d ON d.idDireccion = pr.idDireccion
                    LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                    INNER JOIN  laboratorio lab ON lab.idAlmacen = al.idAlmacen
                    LEFT JOIN resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal
                    LEFT JOIN floraciones fl ON fl.idFloracion = lab.idFloracion
                    WHERE tex.folioTambor = :folio AND tex.tipo = '0'
                    ORDER BY tex.folioTambor ASC";
            $sqlDetalle2 = $conexion->prepare($sql2);
            $sqlDetalle2->bindParam(':folio', $e['folioTambor']);
            $sqlDetalle2->execute();
            // $totalTambos += $sqlDetalle2->rowCount();
            $e2 = $sqlDetalle2->fetch(PDO::FETCH_ASSOC);
            $totalNeto += $e2['neto']; 
            $new = array_merge($e, $e2);
            $resultado['experimental'][$index] = $new;
        }else if($e['tipo'] == "1"){
            // $sql2 = "SELECT alms.bruto, alms.tara, alms.neto, alms.fecha, '--' AS adulteracionDescripcion, '--' AS floracion,
            //         '--' AS hmf, '-' AS idFloracion, '--' AS idresultadoFinal, '--' AS idSagarpa, '----' AS localidad,
            //         s.nombre, '--' AS porcentaje, '--' AS resultado, '--' AS resultadofinal, '--' AS sfDescripcion, '--' AS stDescripcion
            //         FROM almacensobrantes alms
            //         LEFT JOIN $tambores tex ON tex.folioTambor = alms.consecutivo
            //         LEFT JOIN sobrantes s ON s.idSobrante = alms.sobrante  
            //         WHERE tex.tipo = '1' AND alms.consecutivo = :folio AND alms.sobrante = :clasificacion AND alms.tipoDeMiel = :miel
            //         ORDER BY tex.folioTambor ASC";
            $sql2 = "SELECT alms.bruto, alms.tara, alms.neto, alms.fecha, ls.adulteracionDescripcion, fl.floracion,
            ls.hmf, ls.idFloracion, rs.resultado, rs.idresultadoFinal, '---' AS idSagarpa, '---' AS localidad,
            s.nombre, ls.porcentaje, ls.sfDescripcion, ls.stDescripcion
            FROM almacensobrantes alms
            LEFT JOIN $tambores tex ON tex.folioTambor = alms.consecutivo AND tex.tipo = '1' AND tex.clasificacion = :clasificacion
            LEFT JOIN sobrantes s ON s.idSobrante = alms.sobrante 
            LEFT JOIN laboratorio_sobrantes ls ON ls.idAlmacen = alms.consecutivo AND ls.sobrante = :clasificacion AND ls.tipoDeMiel = :miel
            LEFT JOIN floraciones fl ON fl.idFloracion = ls.idFloracion      
            LEFT JOIN resultadofinal rs ON rs.idresultadoFinal = ls.resultadoFinal              
            WHERE tex.tipo = '1' AND alms.consecutivo = :folio AND alms.sobrante = :clasificacion AND alms.tipoDeMiel = :miel
                        ORDER BY tex.folioTambor ASC";
            $sqlDetalle2 = $conexion->prepare($sql2);
            $sqlDetalle2->bindParam(':folio', $e['folioTambor']);
            $sqlDetalle2->bindParam(':clasificacion', $e['clasificacion']);
            $sqlDetalle2->bindParam(':miel', $miel);                                                
            $sqlDetalle2->execute();        
            // $totalTambos += $sqlDetalle2->rowCount();  
            $e2 = $sqlDetalle2->fetch(PDO::FETCH_ASSOC);
            $totalNeto += $e2['neto']; 
            $new = array_merge($e, $e2);
            $resultado['experimental'][$index] = $new;
        }
    }  
    $resultado['totalKilos'] = $totalNeto;
    $resultado['totalTambores'] = $totalTambos;

    // echo json_encode($resultado);

    // if ($queryDetalle->rowCount() >= 1) {
    //     $resultadoDetalle = $queryDetalle->fetchAll(PDO::FETCH_ASSOC);
        echo '<table>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th colspan="3">CONFORMACION DE LOTE EXPERIMENTAL ' . $nombreMiel . '</th>                   
                </tr>  
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>   
                </tr>
                <tr>
                <td>LOTE EXP. EC-'. $resultado['idLoteExperimental'].' </td>
                <td>FECHA: '. $resultado['fecha'].' </td>
                </tr>
                <tr>
                <td>NUMERO TAMBOS: '. $resultado['numeroDeTambores'].' </td>
                <td>KILOS TOTALES: '. $resultado['kilosTotales'].' </td>
                </tr>
                <tr>
                <td>HUMEDAD: '. $resultado['humedad'].' </td>
                <td>RESULTADO LABORATORIO: '. $resultado['resultadoLaboratorio'] .'</td>
                </tr>
                <tr>
                <td></td>
                <td></td>
                <td></td>   
            </tr>
               </table>';
        echo '<table width="100%" border="1">
                <tr style="background:rgb(56,84,40); color:#fff">
                    <th>ENTRADA</th>
                    <th>FOLIO</th>
                    <th>PROVEEDOR</th>
                    <th>IDSAGARPA</th>
                    <th>LOCALIDAD</th>
                    <th>BRUTO</th>
                    <th>TARA</th>
                    <th>NETO</th>
                    <th>%H</th>
                    <th>SF</th>
                    <th>ST</th>
                    <th>C13</th>
                    <th>HMF</th>
                    <th>FLORACION</th>
                    <th>R. FINAL</th>
                </tr>';
        foreach ($resultado['experimental'] as $consecutivo) {
            echo '<tr align="center">
                    <td>' . $consecutivo['fecha'] . '</td>
                    <td>' . $consecutivo['folioTambor'] . '</td>
                    <td>' . $consecutivo['nombre'] . '</td>
                    <td>' . $consecutivo['idSagarpa'] . '</td>
                    <td>' . $consecutivo['localidad'] . '</td>
                    <td>' . $consecutivo['bruto'] . '</td>
                    <td>' . $consecutivo['tara'] . '</td>
                    <td>' . $consecutivo['neto'] . '</td>
                    <td>' . $consecutivo['porcentaje'] . '</td>
                    <td>' . $consecutivo['sfDescripcion'] . '</td>
                    <td>' . $consecutivo['stDescripcion'] . '</td>
                    <td>' . $consecutivo['adulteracionDescripcion'] . '</td>
                    <td>' . $consecutivo['hmf'] . '</td>
                    <td>' . $consecutivo['floracion'] . '</td>
                    <td>' . $consecutivo['resultado'] . '</td>
                </tr>';
        }
        echo '</table>';
    // }

} else {
    die;
}
