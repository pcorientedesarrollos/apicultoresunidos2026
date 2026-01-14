<?php

require_once '../../vendor/autoload.php'; // Asegúrate de que la ruta sea correcta
include_once '../../DAOConeccion/conePDO.php';
require_once '../numberToText.php';
include_once '../../controlAdministrativo/php/nombreDePersona.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$pdo = new conePDO();
$con = $pdo->conectar();

function findConcept($dbh, $idSubcuenta)
{
    $concepto = '';
    $seleccionaConcepto = $dbh->prepare("SELECT subcuenta FROM subcuentas WHERE idSubcuenta = :idSubcuenta");
    $seleccionaConcepto->bindParam(':idSubcuenta', $idSubcuenta, PDO::PARAM_INT);
    $seleccionaConcepto->execute();
    $seleccionaConcepto->bindColumn('subcuenta', $concepto);
    $seleccionaConcepto->fetch(PDO::FETCH_BOUND);
    return $concepto;
}

function getInfo($dbh, $idCajaChica)
{
    $resultado = array();
    try {
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $consulta = $dbh->prepare("SELECT * FROM cajachica WHERE idCajaChica = :idCajaChica");
        $consulta->bindParam(':idCajaChica', $idCajaChica, PDO::PARAM_INT);
        $consulta->execute();
        if ($consulta->rowCount() < 1) {
            return [];
        }
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    return $resultado;
}

$idCajaChica = filter_input(INPUT_GET, 'idCajaChica', FILTER_SANITIZE_NUMBER_INT);
$infoCajaChica = getInfo($con, $idCajaChica);

if (empty($infoCajaChica)) {
    die("No se encontró información para el ID de Caja Chica proporcionado.");
}

// Imprimir el contenido del array $infoCajaChica
/* echo '<pre>';
print_r($infoCajaChica);
echo '</pre>';
 */

$concepto = findConcept($con, $infoCajaChica['idSubcuenta']);
$fecha = date("d-m-Y", strtotime($infoCajaChica['fecha']));
$monto = number_format($infoCajaChica['total'], 2, '.', ',');

$html = '
<html>
<head>
    <style>
        body { font-family: sans-serif; }
        h1 { color: navy; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Movimiento de Caja Chica</h1>
    <p><strong>Fecha:</strong> ' . $fecha . '</p>
    <p><strong>Concepto:</strong> ' . $concepto . '</p>
    <p><strong>Monto:</strong> $' . $monto . ' (' . numberToText($monto) . ')</p>
    <p><strong>Persona:</strong> ' . $nombrePersona . '</p>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>' . $infoCajaChica['idCajaChica'] . '</td>
                <td>' . $infoCajaChica['descripcion'] . '</td>
                <td>$' . $monto . '</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
';

// Configurar Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

// Cargar el contenido HTML en Dompdf
$dompdf->loadHtml($html);

// (Opcional) Configurar el tamaño y la orientación del papel
$dompdf->setPaper('A4', 'portrait');

// Renderizar el PDF
$dompdf->render();

// Enviar el PDF al navegador
$dompdf->stream('Movimiento_Caja_Chica.pdf', ['Attachment' => false]);

?>