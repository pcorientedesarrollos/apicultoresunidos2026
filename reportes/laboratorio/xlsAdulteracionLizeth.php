<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();
$fecha = date("d-m-y");

//Inicio de la instacia de la exportaion
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachmen; filename = Reporte C13_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

$tipoDeMiel = $_GET['tipoDeMiel'];
switch($tipoDeMiel){
    case '1':
        $almacen_tabla = 'almacen';
        $laboratorio_tabla = 'laboratorio';
        $almacenencabezado_tabla = 'almacenencabezado';
        $titulo = "Reporte C13 Miel 100% pura de abeja";
    break;
    case '2':
        $almacen_tabla = 'almacen_organico';
        $laboratorio_tabla = 'laboratorio_organico';
        $almacenencabezado_tabla = 'almacenencabezado_organico';
        $titulo = "Reporte C13 Miel 100% orgánica";
    break;
    case '5':
        $almacen_tabla = 'almacen_mantequilla';
        $laboratorio_tabla = 'laboratorio_mantequilla';
        $almacenencabezado_tabla = 'almacenencabezado_mantequilla';
        $titulo = "Reporte C13 Miel 100% Mantequilla";
    break;
    case '6':
        $almacen_tabla = 'almacen_altiplano';
        $laboratorio_tabla = 'laboratorio_altiplano';
        $almacenencabezado_tabla = 'almacenencabezado_altiplano';
        $titulo = "Reporte C13 Miel 100% Altiplano";
    break;
    case '7':
        $almacen_tabla = 'almacen_naranjo';
        $laboratorio_tabla = 'laboratorio_naranjo';
        $almacenencabezado_tabla = 'almacenencabezado_naranjo';
        $titulo = "Reporte C13 Miel 100% Naranjo";
    break;
    case '8':
        $almacen_tabla = 'almacen_aguacate';
        $laboratorio_tabla = 'laboratorio_aguacate';
        $almacenencabezado_tabla = 'almacenencabezado_aguacate';
        $titulo = "Reporte C13 Miel 100% Aguacate";
    break;
    case '9':
        $almacen_tabla = 'almacen_mezquite';
        $laboratorio_tabla = 'laboratorio_mezquite';
        $almacenencabezado_tabla = 'almacenencabezado_mezquite';
        $titulo = "Reporte C13 Miel 100% Mezquite";
    break;
    default:
        return;
    break;
}

$sql = "SELECT al.idAlmacen, pr.nombre, lab.c13, ale.clasificacionMiel
        FROM $almacen_tabla al
        LEFT JOIN $laboratorio_tabla lab ON lab.idAlmacen = al.idAlmacen
        INNER JOIN $almacenencabezado_tabla ale ON  ale.idAlmacen = al.idalmacenEncabezado
        INNER JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
        ORDER BY al.idAlmacen ASC";
$datos = $conexion->prepare($sql);
$datos->execute();

if ($datos == false) {
    echo mysql_error();
} else {
    $array = array();
    $cont = 0;
    while ($rs = $datos->fetch()) {
        $laboratorioC13 = new stdClass();
        $laboratorioC13->idAlmacen = $rs["idAlmacen"];
        $laboratorioC13->clasificacionMiel = $rs["clasificacionMiel"];
        $laboratorioC13->nombre = $rs["nombre"];
        $laboratorioC13->c13 = $rs["c13"];
        $array[$cont] = $laboratorioC13;
        $cont++;
    }
}

$registros = $array;

echo '<table>
	<tr>
		<td></td>
		<td colspan="2">
                <p style="font-size:22px; font-weight:bold">' . utf8_decode($titulo) . '</p>
                </td>
	</tr>
</table>';

echo '<table width="100%" border="1">
	<tr align="center">
		<td style="background:#bdc3c7; font-weight:bold">Folio</td>
		<td style="background:#bdc3c7; font-weight:bold">Proveedor</td>
		<td style="background:#bdc3c7; font-weight:bold">C13</td>
		</tr>';

foreach ($registros as $reg) {
    echo '<tr>
	<td>
		' . $reg->idAlmacen . ' ' . $reg->clasificacionMiel . '
	</td>
	<td>
		' . utf8_decode($reg->nombre) . '
	</td>

	<td>
		' . number_format($reg->c13, 2, '.', ',') . '
	</td>
       </tr>';
}
echo '</table>';
