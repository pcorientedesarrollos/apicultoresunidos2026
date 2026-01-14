<?php

include_once '../../DAOConeccion/conePDO.php';
include_once '../../controlAdministrativo/php/nombreDePersona.php';
$pdo = new conePDO(); $con = $pdo->conectar();

function getInfo($idAlmacen)
{
    global $con;
    $resultado = array();
    try {
        // Consulta el encabeado
        $consultaEncabezado = $con->prepare("SELECT CONCAT('AL-CER-',al.idAlmacen) as folio, al.fecha, al.total, al.tipo, al.tipoPersona, al.idProveedor, al.tipoCera
        FROM almacenencabezadocera al
        WHERE al.idAlmacen = :idAlmacen");
        $consultaEncabezado->bindParam(':idAlmacen', $idAlmacen);
        $consultaEncabezado->execute();
        if ($consultaEncabezado == false) {
            throw new Exception($con->errorInfo());
        } else if($consultaEncabezado->rowCount() == 0){
            throw new Exception('El reporte no existe');
        } else {
            $resultado['encabezado'] = $consultaEncabezado->fetch(PDO::FETCH_ASSOC);
            $resultado['encabezado']['nombre'] = retornarNombre($con, $resultado['encabezado']['tipoPersona'], $resultado['encabezado']['idProveedor']);
            $resultado['encabezado']['tipoCera'] = $resultado['encabezado']['tipoCera'] == '1' ? 'Cera Convencional' : 'Cera orgánica';
        }
        // Consulta el detalle
        $detalle = $con->prepare("SELECT ac.cantidad, ac.descripcion, ac.costoUnitario, ac.importe, CONCAT(ac.subcuenta, ' - ' , ac.concepto) AS concepto 
        FROM almacencera ac
        WHERE idAlmacenEncabezado = :idAlmacen");
        $detalle->bindParam(':idAlmacen', $idAlmacen);
        $detalle->execute();
        if ($detalle == false) {
            throw new Exception($con->errorInfo());
        }
        $resultado['detalle'] = $detalle->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    } catch (Exception $e) {
        return ['error' => true, 'message' => $e->getMessage()];
    }
};


if (isset($_GET['idAlmacen'])) {
    $reporte = getInfo($_GET['idAlmacen']);
    $tipo_reporte = $reporte['encabezado']['tipo'] == '1' ? 'Entrada de cera. ' : 'Salida de cera. ';
    $codigo = $reporte['encabezado']['tipo'] == '1' ? 'RAL-EC-01 - REVISIÓN: 01' : 'RAL-SC-01 - REVISIÓN: 01';
    $nombre_archivo = 'Reporte de ' . $tipo_reporte . $reporte['encabezado']['folio'] . '.xls';
    $reporte['encabezado']['fecha'] = DateTime::createFromFormat('Y-m-d', $reporte['encabezado']['fecha']);
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=$nombre_archivo");
    header("Pragma: no-cache");
    header("Expires:0");

    echo '<table>
            <tr style="text-align: center;">
                <td colspan="5"><b>OAXACA MIEL S.A. DE C.V.</b></td>
            </tr>

            <tr style="text-align: center;">
                <td colspan="5"><b> </b></td>
            </tr>

            <tr style="text-align: center;">
                <td colspan="5"><b>' . utf8_decode('') . '</b></td>
            </tr>

            <tr style="text-align: center;">
                <td colspan="5"><b>' . utf8_decode('') . '</b></td>
            </tr>

            <tr style="text-align: center;">
                <td colspan="5"><b>Tel: (999) 9.88.09.90</b></td>
            </tr></table>';

    echo '<table style="border-collapse: collapse">
            <tr>
                <td><b>' . utf8_decode($tipo_reporte) . '</b></td>
                <td></td>
            </tr>
            <tr>
                <td>Folio:</td>
                <td>' . utf8_decode($reporte['encabezado']['folio']) . '</td>
            </tr>
            <tr>
                <td>Fecha:</td>
                <td style="text-align:left;">' . utf8_decode(date_format($reporte['encabezado']['fecha'], 'd/m/Y')) . '</td>
            </tr>
            <tr>
                <td>Proveedor:</td>
                <td>' . utf8_decode($reporte['encabezado']['nombre']) . '</td>
            </tr>
            <tr>
                <td>Tipo de cera:</td>
                <td>' . utf8_decode($reporte['encabezado']['tipoCera']) . '</td>
            </tr>
        </table>';

    echo '<table>
            <tr>
            <th></th>
            <th></th>
            <th></th>
            <th>
            ' . utf8_decode($codigo) . '
            </th>
            </tr>
        </table>';

    echo '<table border=1 style="border-collapse: collapse">
            <thead>
                <th>
                    ' . utf8_decode('CANTIDAD') . '
                </th>
                <th>
                    ' . utf8_decode('CONCEPTO') . '
                </th>
                <th>
                    ' . utf8_decode('DESCRIPCIÓN') . '
                </th>
                <th>
                    ' . utf8_decode('P.U.') . '
                </th>
                <th>
                    ' . utf8_decode('IMPORTE') . '
                </th>
            </thead>
            <tbody>';
    foreach($reporte['detalle'] as $detalle) {
        echo    '<tr>
                    <td style="text-align:right;">' . utf8_decode(number_format($detalle['cantidad'], 2, '.', ',')) . '</td>
                    <td style="text-align:left;">' . utf8_decode($detalle['concepto']) . '</td>
                    <td style="text-align:left;">' . utf8_decode($detalle['descripcion']) . '</td>
                    <td style="text-align:right;">$' . utf8_decode(number_format($detalle['costoUnitario'], 2, '.', ',')) . '</td>
                    <td style="text-align:right;">$' . utf8_decode(number_format($detalle['importe'], 2, '.', ',')) . '</td>
                </tr>';
    };

    echo        '<tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align:right;">Total</td>
                    <td style="text-align:right;">$' . utf8_decode(number_format($reporte['encabezado']['total'], 2, '.', ',')) . '</td>
                </tr>';

    echo    '</tbody>
        </table>';

    // Firmas
    // echo '<table>
    //         <tr>
    //             <td>Entregado por</td>
    //             <td></td>
    //             <td>Recibido por</td>
    //         </tr>
    //         <tr>

    //         </tr>
    //         <tr>
    //             <td>__________</td>
    //             <td></td>
    //             <td>__________</td>
    //         </tr>
    //     </table>';
    

} else {
    $nombre_archivo = 'Documento.xls';

    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=$nombre_archivo");
    header("Pragma: no-cache");
    header("Expires:0");
}