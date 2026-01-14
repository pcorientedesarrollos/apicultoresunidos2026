<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename = Proyección Vs Real.xls");
header("Prafma: no-cache");
header("Expires:0");

try {
    if (!isset($_GET['idProyeccion'])) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $idProyeccionSolicitado = $_GET['idProyeccion'];
    }

    // Traer los datos de la base


    $sql = "SELECT idProyeccion, nombre, inicio, fin, totalProyeccion
    FROM proyeccionencabezado WHERE idProyeccion = :idProyeccion;";

    $query = $con->prepare($sql);
    $query->bindParam(':idProyeccion', $idProyeccionSolicitado);
    $query->execute();

    if (!$query) {
        throw new Exception($con->errorInfo());
    }

    $resultadoInfoProyeccion = $query->fetch(PDO::FETCH_ASSOC);


    // calcular el real y la diferencia

    $sqlConsultaReal = "SELECT SUM(al.neto) as total
    FROM almacen al 
    LEFT JOIN almacenencabezado ae ON al.idAlmacenEncabezado = ae.idAlmacen
    WHERE ae.fecha BETWEEN :iniciosemana AND :finsemana";

    $queryRealSemana = $con->prepare($sqlConsultaReal);
    $queryRealSemana->bindParam(':iniciosemana', $resultadoInfoProyeccion['inicio']);
    $queryRealSemana->bindParam(':finsemana', $resultadoInfoProyeccion['fin']);
    $queryRealSemana->execute();

    if (!$queryRealSemana) {
        throw new Exception($con->errorInfo());
    } else {
        $resultadoReal = $queryRealSemana->fetch(PDO::FETCH_ASSOC);
    }

    if (!$resultadoReal['total']) { // Si no hay,la suma da NULL
        $resultadoReal['total'] = 0;
    }

    $resultadoInfoProyeccion['totalReal'] = $resultadoReal['total'];
    $resultadoInfoProyeccion['diferencia'] = $resultadoReal['total'] - $resultadoInfoProyeccion['totalProyeccion'];


    // Ahora, seleccionar las zonas guardadas como detalle de esa proyeccio
    $querySeleccionaZonas = $con->prepare("SELECT pd.idProyeccionDetalle, pd.idProyeccion, pd.idZona as idzona, pd.proyeccion, pd.kilogramos, z.zona, z.referencia
    FROM proyecciondetalle pd
    LEFT JOIN zonas z ON pd.idZona = z.idzona
    WHERE idProyeccion = :idProyeccion;");
    $querySeleccionaZonas->bindParam(':idProyeccion', $idProyeccionSolicitado);

    $querySeleccionaZonas->execute();

    if (!$querySeleccionaZonas) {
        throw new Exeption($con->errorInfo());
    }

    $resultadoZonasProyeccion = $querySeleccionaZonas->fetchAll(PDO::FETCH_ASSOC);

    $resultadoInfoProyeccion['listaZonas'] = array();

    foreach ($resultadoZonasProyeccion as $zona) {
        // Convertir propiedad proyeccion a número

        $zona['proyeccion'] = intval($zona['proyeccion']);

        // Calcular el real

        $sqlRealZona = "SELECT SUM(a.neto) as total FROM almacen a
            LEFT JOIN almacenencabezado ae ON a.idAlmacenEncabezado = ae.idAlmacen
            LEFT JOIN proveedor p ON ae.idProveedor = p.idProveedor
            LEFT JOIN direccion d ON p.idDireccion = d.idDireccion
            LEFT JOIN localidades l ON d.idlocalidad = l.idlocalidad
            WHERE ae.fecha BETWEEN :inicio AND :fin AND l.idzona = :idZona";
        $queryRealZona = $con->prepare($sqlRealZona);
        $queryRealZona->bindParam(':inicio', $resultadoInfoProyeccion['inicio']);
        $queryRealZona->bindParam(':fin', $resultadoInfoProyeccion['fin']);
        $queryRealZona->bindParam(':idZona', $zona['idzona']);
        $queryRealZona->execute();

        if (!$queryRealZona) {
            throw new Exeption($con->errorInfo());
        }

        $resultadoRealZona = $queryRealZona->fetch(PDO::FETCH_ASSOC);

        if (!$resultadoRealZona['total']) { // Si no hay,la suma da NULL
            $resultadoRealZona['total'] = 0;
        }

        $zona['real'] = floatval($resultadoRealZona['total']);

        // Meter al arreglo de zonas de la proyeccion
        array_push($resultadoInfoProyeccion['listaZonas'], $zona);
    }


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
                ' . utf8_decode('PROYECCIÓN VS REAL: ' . strtoupper($resultadoInfoProyeccion['nombre'])) . '
            </th>
        </tr>
        <tr>
            <th colspan=8>
            ' . utf8_decode('DEL ' . $resultadoInfoProyeccion['inicio'] . ' AL ' . $resultadoInfoProyeccion['fin']) . '
            </th>
        </tr>
        <tr>
        <th text-align: right;>
        <p><b>' . utf8_decode('Código:') . 'RCO-PS-03</b></p>
        </th>
        </tr>
        <tr>
        <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">' . '#' . '</th>            
        <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Zona') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Referencia') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Proyección (Tambores)') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Kilogramos') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Real (kg)') . '</th>
        </tr>
    </thead>';

    echo '<tbody>';

    foreach ($resultadoInfoProyeccion['listaZonas'] as $index => $e) {
        echo '<tr>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode(intval($index + 1)) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['zona']) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['referencia']) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['proyeccion']) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode(number_format(floatval($e['kilogramos']), 2, '.', ',')) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode(number_format(floatval($e['real']), 2, '.', ',')) . '</td>
    </tr>';
    }

    echo '</tbody></table>
    <br>
    <table>
        <thead>
           <tr>
                <th style="background-color: #ffe558;" colspan="2">
                    Totales (Kg)
                </th>
            </tr>
        </thead>
        <tbody>
          <tr>
                <td>' . utf8_decode('Total Proyección') . '</td>
                <td  style="text-align:right;">' . utf8_decode(number_format(floatval($resultadoInfoProyeccion['totalProyeccion']), 2, '.', ',')) . '</td>
            </tr>
            <tr>
                <td>Total Real</td>
                <td  style="text-align:right;">' . utf8_decode(number_format(floatval($resultadoInfoProyeccion['totalReal']), 2, '.', ',')) . '</td>
            </tr>
            <tr>
                <td>Diferencia</td>
                <td  style="text-align:right;">' . utf8_decode(number_format(floatval($resultadoInfoProyeccion['diferencia']), 2, '.', ',')) . '</td>
            </tr>
        </tbody>
    </table>
    
    ';
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
