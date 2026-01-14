<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename = Inventario de Equipos.xls");
header("Prafma: no-cache");
header("Expires:0");

try {
    if (!isset($_GET['idArea'])) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $idArea = $_GET['idArea'];
    }

// Traer los datos de la base

    $resultado = array();

    $dato = $con->prepare("SELECT area FROM areas WHERE idArea = :idArea");
    $dato->bindParam(':idArea', $idArea);
    $dato->execute();

    if ($dato == false) {
        throw new Exception($con->errorInfo());
    } else {
        $resultadoSelectArea = $dato->fetch(PDO::FETCH_ASSOC);
        $resultado['area'] = $resultadoSelectArea['area'];
    }

    $resultado['equipos'] = array();

    $datos = $con->prepare("SELECT e.idEquipo, e.nombre, e.caracteristicas,
    a.area, e.mantto, e.marca, e.modelo, e.noSerie, e.costo,
    (CONCAT(s.subarea,' - ',s.nombre)) AS zona
    FROM equipos e
    INNER JOIN areas a ON a.idArea = e.idArea
    LEFT JOIN subareas s ON s.idSubarea = e.idSubarea
    WHERE a.idArea = :idArea
    ORDER BY e.idEquipo ASC");
    $datos->bindParam(':idArea', $idArea);
    $datos->execute();

    if ($datos == false) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado['equipos'] = $datos->fetchAll(PDO::FETCH_ASSOC);
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
                ' . utf8_decode('INVENTARIO DE EQUIPOS EN EL ÁREA: ' . strtoupper($resultado['area'])) . '
            </th>
        </tr>

        <tr>
            <th class="bgm-blue" style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('N°') . '</th>
            <th class="bgm-blue" style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Código') . '</th>
            <th class="bgm-blue" style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Nombre') . '</th>
            <th class="bgm-blue" style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Marca') . '</th>
            <th class="bgm-blue" style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Modelo') . '</th>
            <th class="bgm-blue" style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('N° Serie') . '</th>
            <th class="bgm-blue" style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Descripción') . '</th>
            <th class="bgm-blue" style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"> ' . utf8_decode('Costo') . '</th>
        </tr>
    </thead>';

    echo '<tbody>';

    foreach ($resultado['equipos'] as $index => $e) {
        echo '<tr>

        <td class="bgm-blue" style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode(intval($index + 1)) . '</td>
        <td class="bgm-blue" style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode('AF-MID-' . $e['idEquipo']) . '</td>
        <td class="bgm-blue" style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['nombre']) . '</td>
        <td class="bgm-blue" style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['marca']) . '</td>
        <td class="bgm-blue" style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['modelo']) . '</td>
        <td class="bgm-blue" style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['noSerie']) . '</td>
        <td class="bgm-blue" style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['caracteristicas']) . '</td>
        <td class="bgm-blue" style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode($e['costo']) . '</td>
    </tr>';
    }

    echo '</tbody></table>';

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}