<?php

if (isset($_GET['zona'])) {
    include_once '../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $conexion = $pdo->conectar();

    $zona = $_GET["zona"];
    $fechaReporte = date("d-m-Y");

    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Reporte de tambores por zona_$fechaReporte.xls");
    header("Prafma: no-cache");
    header("Expires:0");

    $sql = "SELECT al.idAlmacen, p.nombre, l.localidad, al.bruto, al.tara, al.neto
            FROM almacen al
            LEFT JOIN almacenencabezado bza ON al.idAlmacenEncabezado = bza.idAlmacen
            LEFT JOIN proveedor p ON p.idProveedor = bza.idProveedor
            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
            LEFT JOIN localidades l ON l.idLocalidad = d.idLocalidad
            WHERE al.zona = :zona AND al.estado != 2";

    $query = $conexion->prepare($sql);
    $query->bindParam(':zona', $zona);
    $query->execute();
    if ($query->rowCount() >= 1) {
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
        $total = count($resultado);
        $totalNeto = 0;
        foreach ($resultado as $tambor) {
            $totalNeto = $totalNeto + $tambor['neto'];
        }
        echo '<table>
                <tr>
                    <th></th>
                    <th>Reporte de tambores de la ZONA ' . $zona . '</th>
                    <th></th>
                    <td>Fecha: ' . $fechaReporte . '</td>
                </tr>
                <tr>
                    <td>Tambores: ' . $total . '</td>
                </tr>               
                <tr>
                    <td>Total Neto: ' . number_format($totalNeto, 2, '.', ',') . '</td>
                </tr>
            </table>';
        echo '<table width="100%" border="1">
                <tr style="background:rgb(56,84,40); color:#fff">
                        <th>' . utf8_decode('N°') . '</th>
                        <th>FOLIO</th>
                        <th>PROVEEDOR</th>
                        <th>LOCALIDAD</th>
                        <th>BRUTO</th>
                        <th>TARA</th>
                        <th>NETO</th>
                </tr>';
        foreach ($resultado as $k => $tambor) {
            echo '<tr align="center">
                    <td>' . utf8_decode($k + 1) . '</td>
                    <td>' . $tambor['idAlmacen'] . '</td>
                    <td>' . utf8_decode($tambor['nombre']) . '</td>
                    <td>' . utf8_decode($tambor['localidad']) . '</td>
                    <td>' . $tambor['bruto'] . '</td>
                    <td>' . $tambor['tara'] . '</td>
                    <td>' . $tambor['neto'] . '</td>
                </tr>';
        }
        echo '</table>';
    } else {
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
        $total = count($resultado);
        echo '<table>
                <tr><th>Reporte de tambores por zona.</th></tr>
                <tr><td>Fecha: ' . $fechaReporte . '</td></tr>
                <tr>
                    <td>Zona: ' . $zona . '</td>
                    <td>Tambores: ' . $total . '</td>
                </tr>
            </table>';
    }
} else {
    die;
}

