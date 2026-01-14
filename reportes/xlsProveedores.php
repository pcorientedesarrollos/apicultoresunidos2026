<?php
include_once '../clases/consultas.php';
include_once '../DAOConeccion/conePDO.php';

$dao =new consultas();
$pdo = new conePDO();
$conexion = $pdo->conectar();

//Fecha de exportacion
$fecha = date("d-m-y");

//Inicio de la instancia para la exportacion

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Relacion de Proveedores_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

$Titulo = 'Relacion de Proveedores';

echo '<table  width="100%">';
echo '<tr>';

echo '<td width = "50%" style="color:#0000;"> <span style="font-weight: bold; font-size: 18pt;">'.$Titulo.'</span> </td> ';
echo '</tr>';
echo '</table>';
echo '<br>';
echo '<table  width="100%" border=1">';
echo '<tr>';
echo '<th>No</th>';
echo '<th>Proveedor</th>';
echo '<th>Sagarpa</th>';
echo '<th>Zona</th>';
echo '<th>Localidad</th>';
echo '<th>Estado</th>';
echo '<th>Comprador</th>';
echo '</tr>';

$consultaProveedores = $dao ->Proveedor();
$proveedores = $conexion ->prepare($consultaProveedores);
$proveedores->execute();

$cont= 0 ;

while ($datosProveedores = $proveedores->fetch()){
    $nombrePr   = $datosProveedores["nombre"];
    $sagarpa    = $datosProveedores["idSagarpa"];
    $zona       = $datosProveedores["zona"];
    $localidad  = $datosProveedores["localidad"];
    $estado     = $datosProveedores["estado"];
    $comprador  = $datosProveedores["comprador"];
    
    $cont = $cont + 1;
    echo '<tr>';
    echo '<td>' . $cont . "</td>";
    echo '<td>' . $nombrePr . "</td>";
    echo '<td>' . $sagarpa . "</td>";
    echo '<td>' . $zona . "</td>";
    echo '<td>' . $localidad . "</td>";
    echo '<td>' . $estado . "</td>";
    echo '<td>' . $comprador . "</td>";
    echo '</tr>';
}
echo '</table>';