<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename = Laboratorio miel sobrante.xls");
header("Prafma: no-cache");
header("Expires:0");

try {
    if (!isset($_GET['tipoDeMiel'])) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $tipoDeMiel = $_GET['tipoDeMiel'];
        if ($tipoDeMiel == '1') {
            $tipo = '(MIEL 100% PURA DE ABEJA)';
        } else if ($tipoDeMiel == '2') {
            $tipo = '(MIEL 100% ORGÁNICA)';
        }
    }

    // Traer los datos de la base


    $sql = "SELECT asd.referencia,
    CONCAT(s.codigo, '-', asd.consecutivo) AS folio, asd.bruto, asd.tara, asd.neto,
    CASE WHEN ase.tipo = 2 THEN 'Exportación' WHEN ase.tipo = 1 THEN 'Nacional' END AS calidad,
    lab.porcentaje, lab.color, lab.sf, lab.st, lab.c13, lab.hmf, r.resultado
    FROM almacensobrantes asd
    LEFT JOIN almacensobrantesencabezado ase ON ase.idEncabezadoSobrante = asd.consecutivoEntrada
    LEFT JOIN sobrantes s ON s.idSobrante = asd.sobrante
    LEFT JOIN laboratorio_sobrantes lab ON lab.idAlmacen = asd.consecutivo AND lab.sobrante = asd.sobrante AND lab.tipoDeMiel = :miel
    LEFT JOIN resultadofinal r ON r.idresultadoFinal = lab.resultadoFinal
    WHERE ase.tipoDeMiel = :miel;";

    $query = $con->prepare($sql);
    $query->bindParam(':miel', $tipoDeMiel);
    $query->execute();

    if (!$query) {
        throw new Exception($con->errorInfo());
    }

    $resultadoInfoProyeccion = $query->fetchAll(PDO::FETCH_ASSOC);

    //  Imprimir el formato del excel
    echo '<table>
    <thead>
        <tr style="font-size:18px">
            <th colspan=8>
                Apicultores Unidos de la Peninsula S.A. de C.V.
            </th>
        </tr>
        <tr style="font-size:16px">
            <th colspan=8>
            ' . utf8_decode('ANÁLISIS DE SOBRANTES  ' . strtoupper($tipo)) . '
            </th>
        </tr>
        <tr>
        <th text-align: right;>
        <p><b>' . utf8_decode('Código:') . '</b></p>
        </th>
        </tr>
        <tr></tr><tr></tr>
        <tr>
        <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">' . 'Referencia' . '</th>            
        <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Folio') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Bruto') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Tara') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Neto') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Calidad') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Exp.') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Lote') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('H%') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Color') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('SF') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('ST') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('C13') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('HMF') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('F/G') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Resultado') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Comentarios') . '</th>
        </tr>
    </thead>';

    echo '<tbody>';

    foreach ($resultadoInfoProyeccion as $index => $e) {
        echo '<tr>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['referencia']) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['folio']) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode(number_format(floatval($e['bruto']), 2, '.', ',')) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode(number_format(floatval($e['tara']), 2, '.', ',')) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode(number_format(floatval($e['neto']), 2, '.', ',')) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['calidad']) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"></td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"></td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['porcentaje']) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['color']) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['sf']) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['st']) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['c13']) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['hmf']) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"></td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['resultado']) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"></td>
     </tr>';
    }

    echo '</tbody>
    ';
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
