<?php

include_once '../../DAOConeccion/conePDO.php';
include_once '../../mensajes/Mensajes.php';
$pdo = new conePDO();
$msg = new Mensajes();
$con = $pdo->conectar();
$json = file_get_contents("php://input");
$datos = json_decode($json);
$info = $datos->valor;
$id = $_GET["id"];

if(isset($_GET["organica"])){
        $sql = "INSERT INTO cubetasdetalle_organico(idAlmacenEncabezado, zona,  trazabilidad, pesoLista, bruto, tara, neto, diferencia, humedad, autorizado, precio, costoTotal) "
        . "VALUES(:id, :zona, :trazabilidad, :pesoLista, :bruto, :tara, :neto, :diferencia, :humedad, '0', '0', '0')";
    $dato = $con->prepare($sql);
    $dato->bindParam(':id', $id);
    $dato->bindParam(':zona', $info->zona);
    $dato->bindParam(':trazabilidad', $info->trazabilidad);
    $dato->bindParam(':pesoLista', $info->pesoLista);
    $dato->bindParam(':bruto', $info->bruto);
    $dato->bindParam(':tara', $info->tara);
    $dato->bindParam(':neto', $info->neto);
    $dato->bindParam(':diferencia', $info->diferencia);
    $dato->bindParam(':humedad', $info->humedad);
    $dato->execute();
    if ($dato == false) {
        echo json_encode($msg->error(mysql_error()));
    } else {
        echo json_encode($msg->succes("Nuevo almacen disponible"));
    }
}else{
    $sql = "INSERT INTO cubetasdetalle(idAlmacenEncabezado, zona,  trazabilidad, pesoLista, bruto, tara, neto, diferencia, humedad, autorizado, precio, costoTotal) "
    . "VALUES(:id, :zona, :trazabilidad, :pesoLista, :bruto, :tara, :neto, :diferencia, :humedad, '0', '0', '0')";
$dato = $con->prepare($sql);
$dato->bindParam(':id', $id);
$dato->bindParam(':zona', $info->zona);
$dato->bindParam(':trazabilidad', $info->trazabilidad);
$dato->bindParam(':pesoLista', $info->pesoLista);
$dato->bindParam(':bruto', $info->bruto);
$dato->bindParam(':tara', $info->tara);
$dato->bindParam(':neto', $info->neto);
$dato->bindParam(':diferencia', $info->diferencia);
$dato->bindParam(':humedad', $info->humedad);
$dato->execute();
if ($dato == false) {
echo json_encode($msg->error(mysql_error()));
} else {
echo json_encode($msg->succes("Nuevo almacen disponible"));
}
}

