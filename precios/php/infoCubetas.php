<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$id = $_GET['id'];

if(isset($_GET['organica'])){
    $query = "
    SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.trazabilidad, cd.pesoLista, cd.bruto, 
           cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, cd.precio, cd.costoTotal, cd.aprobado
    FROM cubetasdetalle_organico cd
    LEFT JOIN cubetasencabezado_organico ce ON ce.idAlmacen = cd.idAlmacenEncabezado
    LEFT JOIN almacenencabezado_organico alm ON alm.idAlmacen = ce.folioEntradaTambor
    WHERE ce.idAlmacen = :id";
$datos = $con->prepare($query);
$datos->bindParam(':id', $id);
$datos->execute();

$arrayAlmacenCub = array();
while ($rsAlmacenDetalle = $datos->fetch()) {
$cubetaMiel = new stdClass();
$cubetaMiel->idAlmacen = $rsAlmacenDetalle[0];
$cubetaMiel->idAlmacenEncabezado = $rsAlmacenDetalle["idAlmacenEncabezado"];
$cubetaMiel->zona = $rsAlmacenDetalle["zona"];
$cubetaMiel->trazabilidad = $rsAlmacenDetalle["trazabilidad"];
$cubetaMiel->pesoLista = $rsAlmacenDetalle["pesoLista"];
$cubetaMiel->bruto = $rsAlmacenDetalle["bruto"];
$cubetaMiel->tara = $rsAlmacenDetalle["tara"];
$cubetaMiel->neto = $rsAlmacenDetalle["neto"];
$cubetaMiel->diferencia = $rsAlmacenDetalle["diferencia"];
$cubetaMiel->humedad = $rsAlmacenDetalle["humedad"];
$cubetaMiel->precio = $rsAlmacenDetalle["precio"];
$cubetaMiel->costoTotal = $rsAlmacenDetalle["costoTotal"];
$cubetaMiel->aprobado = $rsAlmacenDetalle["aprobado"];

$arrayAlmacenCub[] = $cubetaMiel;
}

# JSON-encode the response
echo $json_response = json_encode($arrayAlmacenCub);
} else if(isset($_GET['organicam'])){
    $query = "
    SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.trazabilidad, cd.pesoLista, cd.bruto, 
           cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, cd.precio, cd.costoTotal, cd.aprobado
    FROM cubetasdetalle_mantequilla cd
    LEFT JOIN cubetasencabezado_mantequilla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
    LEFT JOIN almacenencabezado_mantequilla alm ON alm.idAlmacen = ce.folioEntradaTambor
    WHERE ce.idAlmacen = :id";
$datos = $con->prepare($query);
$datos->bindParam(':id', $id);
$datos->execute();

$arrayAlmacenCub = array();
while ($rsAlmacenDetalle = $datos->fetch()) {
$cubetaMiel = new stdClass();
$cubetaMiel->idAlmacen = $rsAlmacenDetalle[0];
$cubetaMiel->idAlmacenEncabezado = $rsAlmacenDetalle["idAlmacenEncabezado"];
$cubetaMiel->zona = $rsAlmacenDetalle["zona"];
$cubetaMiel->trazabilidad = $rsAlmacenDetalle["trazabilidad"];
$cubetaMiel->pesoLista = $rsAlmacenDetalle["pesoLista"];
$cubetaMiel->bruto = $rsAlmacenDetalle["bruto"];
$cubetaMiel->tara = $rsAlmacenDetalle["tara"];
$cubetaMiel->neto = $rsAlmacenDetalle["neto"];
$cubetaMiel->diferencia = $rsAlmacenDetalle["diferencia"];
$cubetaMiel->humedad = $rsAlmacenDetalle["humedad"];
$cubetaMiel->precio = $rsAlmacenDetalle["precio"];
$cubetaMiel->costoTotal = $rsAlmacenDetalle["costoTotal"];
$cubetaMiel->aprobado = $rsAlmacenDetalle["aprobado"];

$arrayAlmacenCub[] = $cubetaMiel;
}

# JSON-encode the response
echo $json_response = json_encode($arrayAlmacenCub);
}
else if(isset($_GET['organicaa'])){
    $query = "
    SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.trazabilidad, cd.pesoLista, cd.bruto, 
           cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, cd.precio, cd.costoTotal, cd.aprobado
    FROM cubetasdetalle_altiplano cd
    LEFT JOIN cubetasencabezado_altiplano ce ON ce.idAlmacen = cd.idAlmacenEncabezado
    LEFT JOIN almacenencabezado_altiplano alm ON alm.idAlmacen = ce.folioEntradaTambor
    WHERE ce.idAlmacen = :id";
$datos = $con->prepare($query);
$datos->bindParam(':id', $id);
$datos->execute();

$arrayAlmacenCub = array();
while ($rsAlmacenDetalle = $datos->fetch()) {
$cubetaMiel = new stdClass();
$cubetaMiel->idAlmacen = $rsAlmacenDetalle[0];
$cubetaMiel->idAlmacenEncabezado = $rsAlmacenDetalle["idAlmacenEncabezado"];
$cubetaMiel->zona = $rsAlmacenDetalle["zona"];
$cubetaMiel->trazabilidad = $rsAlmacenDetalle["trazabilidad"];
$cubetaMiel->pesoLista = $rsAlmacenDetalle["pesoLista"];
$cubetaMiel->bruto = $rsAlmacenDetalle["bruto"];
$cubetaMiel->tara = $rsAlmacenDetalle["tara"];
$cubetaMiel->neto = $rsAlmacenDetalle["neto"];
$cubetaMiel->diferencia = $rsAlmacenDetalle["diferencia"];
$cubetaMiel->humedad = $rsAlmacenDetalle["humedad"];
$cubetaMiel->precio = $rsAlmacenDetalle["precio"];
$cubetaMiel->costoTotal = $rsAlmacenDetalle["costoTotal"];
$cubetaMiel->aprobado = $rsAlmacenDetalle["aprobado"];

$arrayAlmacenCub[] = $cubetaMiel;
}
# JSON-encode the response
echo $json_response = json_encode($arrayAlmacenCub);
} 

else if(isset($_GET['organicaagua'])){
    $query = "
    SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.trazabilidad, cd.pesoLista, cd.bruto, 
           cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, cd.precio, cd.costoTotal, cd.aprobado
    FROM cubetasdetalle_aguacate cd
    LEFT JOIN cubetasencabezado_aguacate ce ON ce.idAlmacen = cd.idAlmacenEncabezado
    LEFT JOIN almacenencabezado_aguacate alm ON alm.idAlmacen = ce.folioEntradaTambor
    WHERE ce.idAlmacen = :id";
$datos = $con->prepare($query);
$datos->bindParam(':id', $id);
$datos->execute();

$arrayAlmacenCub = array();
while ($rsAlmacenDetalle = $datos->fetch()) {
$cubetaMiel = new stdClass();
$cubetaMiel->idAlmacen = $rsAlmacenDetalle[0];
$cubetaMiel->idAlmacenEncabezado = $rsAlmacenDetalle["idAlmacenEncabezado"];
$cubetaMiel->zona = $rsAlmacenDetalle["zona"];
$cubetaMiel->trazabilidad = $rsAlmacenDetalle["trazabilidad"];
$cubetaMiel->pesoLista = $rsAlmacenDetalle["pesoLista"];
$cubetaMiel->bruto = $rsAlmacenDetalle["bruto"];
$cubetaMiel->tara = $rsAlmacenDetalle["tara"];
$cubetaMiel->neto = $rsAlmacenDetalle["neto"];
$cubetaMiel->diferencia = $rsAlmacenDetalle["diferencia"];
$cubetaMiel->humedad = $rsAlmacenDetalle["humedad"];
$cubetaMiel->precio = $rsAlmacenDetalle["precio"];
$cubetaMiel->costoTotal = $rsAlmacenDetalle["costoTotal"];
$cubetaMiel->aprobado = $rsAlmacenDetalle["aprobado"];

$arrayAlmacenCub[] = $cubetaMiel;
}
# JSON-encode the response
echo $json_response = json_encode($arrayAlmacenCub);
}

else if(isset($_GET['organicame'])){
    $query = "
    SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.trazabilidad, cd.pesoLista, cd.bruto, 
           cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, cd.precio, cd.costoTotal, cd.aprobado
    FROM cubetasdetalle_mezquite cd
    LEFT JOIN cubetasencabezado_mezquite ce ON ce.idAlmacen = cd.idAlmacenEncabezado
    LEFT JOIN almacenencabezado_mezquite alm ON alm.idAlmacen = ce.folioEntradaTambor
    WHERE ce.idAlmacen = :id";
$datos = $con->prepare($query);
$datos->bindParam(':id', $id);
$datos->execute();

$arrayAlmacenCub = array();
while ($rsAlmacenDetalle = $datos->fetch()) {
$cubetaMiel = new stdClass();
$cubetaMiel->idAlmacen = $rsAlmacenDetalle[0];
$cubetaMiel->idAlmacenEncabezado = $rsAlmacenDetalle["idAlmacenEncabezado"];
$cubetaMiel->zona = $rsAlmacenDetalle["zona"];
$cubetaMiel->trazabilidad = $rsAlmacenDetalle["trazabilidad"];
$cubetaMiel->pesoLista = $rsAlmacenDetalle["pesoLista"];
$cubetaMiel->bruto = $rsAlmacenDetalle["bruto"];
$cubetaMiel->tara = $rsAlmacenDetalle["tara"];
$cubetaMiel->neto = $rsAlmacenDetalle["neto"];
$cubetaMiel->diferencia = $rsAlmacenDetalle["diferencia"];
$cubetaMiel->humedad = $rsAlmacenDetalle["humedad"];
$cubetaMiel->precio = $rsAlmacenDetalle["precio"];
$cubetaMiel->costoTotal = $rsAlmacenDetalle["costoTotal"];
$cubetaMiel->aprobado = $rsAlmacenDetalle["aprobado"];

$arrayAlmacenCub[] = $cubetaMiel;
}

# JSON-encode the response
echo $json_response = json_encode($arrayAlmacenCub);
}
else if(isset($_GET['organican'])){
    $query = "
    SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.trazabilidad, cd.pesoLista, cd.bruto, 
           cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, cd.precio, cd.costoTotal, cd.aprobado
    FROM cubetasdetalle_naranjo cd
    LEFT JOIN cubetasencabezado_naranjo ce ON ce.idAlmacen = cd.idAlmacenEncabezado
    LEFT JOIN almacenencabezado_naranjo alm ON alm.idAlmacen = ce.folioEntradaTambor
    WHERE ce.idAlmacen = :id";
$datos = $con->prepare($query);
$datos->bindParam(':id', $id);
$datos->execute();

$arrayAlmacenCub = array();
while ($rsAlmacenDetalle = $datos->fetch()) {
$cubetaMiel = new stdClass();
$cubetaMiel->idAlmacen = $rsAlmacenDetalle[0];
$cubetaMiel->idAlmacenEncabezado = $rsAlmacenDetalle["idAlmacenEncabezado"];
$cubetaMiel->zona = $rsAlmacenDetalle["zona"];
$cubetaMiel->trazabilidad = $rsAlmacenDetalle["trazabilidad"];
$cubetaMiel->pesoLista = $rsAlmacenDetalle["pesoLista"];
$cubetaMiel->bruto = $rsAlmacenDetalle["bruto"];
$cubetaMiel->tara = $rsAlmacenDetalle["tara"];
$cubetaMiel->neto = $rsAlmacenDetalle["neto"];
$cubetaMiel->diferencia = $rsAlmacenDetalle["diferencia"];
$cubetaMiel->humedad = $rsAlmacenDetalle["humedad"];
$cubetaMiel->precio = $rsAlmacenDetalle["precio"];
$cubetaMiel->costoTotal = $rsAlmacenDetalle["costoTotal"];
$cubetaMiel->aprobado = $rsAlmacenDetalle["aprobado"];

$arrayAlmacenCub[] = $cubetaMiel;
}

# JSON-encode the response
echo $json_response = json_encode($arrayAlmacenCub);
} else{
    $query = "
    SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.trazabilidad, cd.pesoLista, cd.bruto, 
           cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, cd.precio, cd.costoTotal, cd.aprobado
    FROM cubetasdetalle cd
    LEFT JOIN cubetasencabezado ce ON ce.idAlmacen = cd.idAlmacenEncabezado
    LEFT JOIN almacenencabezado alm ON alm.idAlmacen = ce.folioEntradaTambor
    WHERE ce.idAlmacen = :id";
$datos = $con->prepare($query);
$datos->bindParam(':id', $id);
$datos->execute();

$arrayAlmacenCub = array();
while ($rsAlmacenDetalle = $datos->fetch()) {
$cubetaMiel = new stdClass();
$cubetaMiel->idAlmacen = $rsAlmacenDetalle[0];
$cubetaMiel->idAlmacenEncabezado = $rsAlmacenDetalle["idAlmacenEncabezado"];
$cubetaMiel->zona = $rsAlmacenDetalle["zona"];
$cubetaMiel->trazabilidad = $rsAlmacenDetalle["trazabilidad"];
$cubetaMiel->pesoLista = $rsAlmacenDetalle["pesoLista"];
$cubetaMiel->bruto = $rsAlmacenDetalle["bruto"];
$cubetaMiel->tara = $rsAlmacenDetalle["tara"];
$cubetaMiel->neto = $rsAlmacenDetalle["neto"];
$cubetaMiel->diferencia = $rsAlmacenDetalle["diferencia"];
$cubetaMiel->humedad = $rsAlmacenDetalle["humedad"];
$cubetaMiel->precio = $rsAlmacenDetalle["precio"];
$cubetaMiel->costoTotal = $rsAlmacenDetalle["costoTotal"];
$cubetaMiel->aprobado = $rsAlmacenDetalle["aprobado"];
$arrayAlmacenCub[] = $cubetaMiel;
}

# JSON-encode the response
echo $json_response = json_encode($arrayAlmacenCub);
}
?>