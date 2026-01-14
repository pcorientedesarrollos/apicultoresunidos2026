<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$id = $_GET['id'];
$tipo = $_GET['tipo'];

switch ($tipo) {
    case '1':
        $detalle = 'almacen';
        $encabezado = 'almacenencabezado';
        $detalle_cubeta = 'cubetasdetalle';
        $encabezado_cubeta = 'cubetasencabezado';
        $laboratorio = 'laboratorio';
        break;
    case '2':
        $detalle = 'almacen_organico';
        $encabezado = 'almacenencabezado_organico';
        $detalle_cubeta = 'cubetasdetalle_organico';
        $encabezado_cubeta = 'cubetasencabezado_organico';
        $laboratorio = 'laboratorio_organico';
        break;
    case '5':
        $detalle = 'almacen_mantequilla';
        $encabezado = 'almacenencabezado_mantequilla';
        $detalle_cubeta = 'cubetasdetalle_mantequilla';
        $encabezado_cubeta = 'cubetasencabezado_mantequilla';
        $laboratorio = 'laboratorio_mantequilla';
        break;
    case '6':
        $detalle = 'almacen_altiplano';
        $encabezado = 'almacenencabezado_altiplano';
        $detalle_cubeta = 'cubetasdetalle_altiplano';
        $encabezado_cubeta = 'cubetasencabezado_altiplano';
        $laboratorio = 'laboratorio_altiplano';
        break;
    case '7':
        $detalle = 'almacen_naranjo';
        $encabezado = 'almacenencabezado_naranjo';
        $detalle_cubeta = 'cubetasdetalle_naranjo';
        $encabezado_cubeta = 'cubetasencabezado_naranjo';
        $laboratorio = 'laboratorio_naranjo';
        break;
    case '8':
        $detalle = 'almacen_aguacate';
        $encabezado = 'almacenencabezado_aguacate';
        $detalle_cubeta = 'cubetasdetalle_aguacate';
        $encabezado_cubeta = 'cubetasencabezado_aguacate';
        $laboratorio = 'laboratorio_aguacate';
        break;
    case '9':
        $detalle = 'almacen_mezquite';
        $encabezado = 'almacenencabezado_mezquite';
        $detalle_cubeta = 'cubetasdetalle_mezquite';
        $encabezado_cubeta = 'cubetasencabezado_mezquite';
        $laboratorio = 'laboratorio_mezquite';
        break;
}

$query = "SELECT al.idAlmacen, al.idAlmacenEncabezado, al.zona, al.trazabilidad, 
    al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, al.humedad, al.autorizado,
    al.precio, al.costoTotal, al.aprobado, l.porcentaje
FROM $detalle al
LEFT JOIN $laboratorio l ON l.idAlmacen = al.idAlmacen
WHERE al.idAlmacenEncabezado = :id
ORDER BY al.idAlmacen ASC;
UNION
SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.trazabilidad, cd.pesoLista, cd.bruto, 
    cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, cd.precio, cd.costoTotal, cd.aprobado, '' AS porcentaje
FROM $detalle_cubeta cd
LEFT JOIN $encabezado_cubeta ce ON ce.idAlmacen = cd.idAlmacenEncabezado
LEFT JOIN $encabezado alm ON alm.idAlmacen = ce.folioEntradaTambor
WHERE ce.folioEntradaTambor = :id";
$datos = $con->prepare($query);
$datos->bindParam(':id', $id);
$datos->execute();

$arrayAlmacen = array();
while ($rsAlmacenDetalle = $datos->fetch()) {
    $almacenn = new stdClass();
    $almacenn->idAlmacen = $rsAlmacenDetalle[0];
    $almacenn->idAlmacenEncabezado = $rsAlmacenDetalle["idAlmacenEncabezado"];
    $almacenn->zona = $rsAlmacenDetalle["zona"];
    $almacenn->trazabilidad = $rsAlmacenDetalle["trazabilidad"];
    $almacenn->pesoLista = $rsAlmacenDetalle["pesoLista"];
    $almacenn->bruto = $rsAlmacenDetalle["bruto"];
    $almacenn->tara = $rsAlmacenDetalle["tara"];
    $almacenn->neto = $rsAlmacenDetalle["neto"];
    $almacenn->diferencia = $rsAlmacenDetalle["diferencia"];
    $almacenn->humedad = $rsAlmacenDetalle["humedad"];
    $almacenn->precio = $rsAlmacenDetalle["precio"];
    $almacenn->copiaPrecio = $rsAlmacenDetalle["precio"];
    $almacenn->costoTotal = $rsAlmacenDetalle["costoTotal"];
    $almacenn->porcentaje = $rsAlmacenDetalle["porcentaje"];
    $almacenn->aprobado = $rsAlmacenDetalle["aprobado"];
    $arrayAlmacen[] = $almacenn;
}

# JSON-encode the response
echo $json_response = json_encode($arrayAlmacen);

?>