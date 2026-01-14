<?php

include_once '../clases/consultas.php';
include_once '../DAOConeccion/conePDO.php';

$dao = new consultas();
$pdo = new conePDO();
$conexion = $pdo->conectar();
//$prov = $dao->proveedorsagarpa();

//Fecha de exportacion
$fecha = date("d-m-y");

//Inicio de la instancia para la exportacion

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Relacion de Proveedores para Sagarpa_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

$Titulo = 'Relacion de Proveedores';

echo '<table  width="100%">';
echo '<tr>';

echo '<td width = "50%" style="color:#0000;"> <span style="font-weight: bold; font-size: 18pt;">' . $Titulo . '</span> </td> ';
echo '</tr>';
echo '</table>';
echo '<br>';

echo '<table  width="100%" border=1">';
echo '<tr>';
echo '<th>No</th>';
echo '<th>IDSagarpa</th>';
echo '<th>Proveedor</th>';
echo '<th>Domicilio</td>';
echo '<th>Estado</th>';
echo '<th>Localidad</th>';
echo '<th>Domicilio Fiscal</th>';
echo '<th>Colonia</th>';
echo '<th>' . utf8_decode('Teléfono') . '</th>';
echo'<th>Correo Electronico</th>';
echo '<th>ton/litros</th>';
echo '</tr>';

$cont = 0;
$toneladas = 0;
$consultaProvedoresSagarpa = $dao->proveedorsagarpa();
$dataProveSagarpa = $conexion->prepare($consultaProvedoresSagarpa);
$dataProveSagarpa->execute();
while ($proveedorsagarpa = $dataProveSagarpa->fetch()){
    $proveedor  = $proveedorsagarpa['proveedor'];
    $idSagarpaP = $proveedorsagarpa['sagarpa'];
    $localidadP = $proveedorsagarpa['localidad'];
    $estadoP    = $proveedorsagarpa['estado'];
    $domicilio  = $proveedorsagarpa['direccion'];
    $fisico     = $proveedorsagarpa['direccion'];
    $colonia    = $proveedorsagarpa['colonia'];
    $telefonoP  = $proveedorsagarpa['telefono'];
    $correo     = $proveedorsagarpa['correo'];
    $kilogra    = $proveedorsagarpa['neto'];
    $cont = $cont + 1;
    $toneladas = $kilogra / 1000;
    
    echo '<tr>';
    echo '<td>' . $cont . '</td>';
    echo '<td>' . $idSagarpaP . '</td>';
    echo '<td>' . $proveedor. '</td>';
    echo '<td>' . $domicilio . '</td>';
    echo '<td>' . $estadoP . '</td>';
    echo '<td>' . $localidadP . '</td>';
    echo '<td>' . $fisico . '</td>';
    echo '<td>' . $colonia . '</td>';
    echo '<td>' . $telefonoP . '</td>';
    echo '<td>' . $correo . '</td>';
    echo '<td>' . number_format($toneladas, 2, '.', ',') . '</td>';
    echo '</tr>';
    
}
    echo '</table>';