<?php

if (isset($_GET['miel']) && isset($_GET['sobrante'])) {
    include_once '../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $conexion = $pdo->conectar();

    $miel = $_GET["miel"];
    $sobrante = $_GET["sobrante"];
    if ($miel = '1') {
        $nombreMiel = "MIEL 100% PURA DE ABEJA";
    } else {
        $nombreMiel = "MIEL 100% ORGANICA";
    }
    $fechaReporte = date("d-m-Y");

    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Comparativo de folios sobrantes_$fechaReporte.xls");
    header("Prafma: no-cache");
    header("Expires:0");

    $sqlEncabezado = "SELECT s.nombre FROM sobrantes s WHERE s.idSobrante = :sobrante";
    $queryEncabezado = $conexion->prepare($sqlEncabezado);
    $queryEncabezado->bindParam(':sobrante', $sobrante);
    $queryEncabezado->execute();

    $encabezado = $queryEncabezado->fetch(PDO::FETCH_ASSOC);

    $sqlDetalle = "SELECT CONCAT(s.codigo, '-', a.consecutivo) AS folio,
    CASE WHEN a.referencia IS NOT NULL THEN a.referencia ELSE '---' END AS referencia, 
    CASE WHEN a.lote IS NOT NULL THEN a.lote ELSE '---' END AS lote
    FROM almacensobrantes a
    LEFT JOIN sobrantes s ON s.idSobrante = a.sobrante
    WHERE a.tipoDeMiel = :miel AND a.sobrante = :sobrante 
    ORDER BY a.consecutivo ASC";
    $queryDetalle = $conexion->prepare($sqlDetalle);
    $queryDetalle->bindParam(':miel', $miel);
    $queryDetalle->bindParam(':sobrante', $sobrante);
    $queryDetalle->execute();
    if ($queryDetalle->rowCount() >= 1) {
        $resultadoDetalle = $queryDetalle->fetchAll(PDO::FETCH_ASSOC);
        echo '<table>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th colspan="3">COMPARATIVO DE FOLIOS ' . $nombreMiel . '</th>                   
                </tr>  
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>   
                </tr>
               </table>';
        echo '<table width="100%" border="1">
                <tr style="background:rgb(56,84,40); color:#fff">
                        <th>FOLIO</th>
                        <th>REFERENCIA</th>
                        <th>LOTE</th>
                </tr>';
        foreach ($resultadoDetalle as $k => $consecutivo) {
            echo '<tr align="center">
                    <td>' . $consecutivo['folio'] . '</td>
                    <td>' . $consecutivo['referencia'] . '</td>
                    <td>' . $consecutivo['lote'] . '</td>
                </tr>';
        }
        echo '</table>';
    }
    // else {
    //     $resultadoDetalle = $query->fetchAll(PDO::FETCH_ASSOC);
    //     $total = count($resultadoDetalle);
    //     echo '<table>
    //             <tr><th>Reporte de tambores por miel.</th></tr>
    //             <tr><td>Fecha: ' . $fechaReporte . '</td></tr>
    //             <tr>
    //                 <td>miel: ' . $miel . '</td>
    //                 <td>Tambores: ' . $total . '</td>
    //             </tr>
    //         </table>';
    // }
} else {
    die;
}
