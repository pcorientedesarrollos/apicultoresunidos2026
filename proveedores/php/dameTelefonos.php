<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();


$id = $_GET["id"];
$array = array();
$sql = "SELECT * FROM telefonos WHERE idContacto = :id";
$datos = $conexion->prepare($sql);
$datos->bindParam(':id', $id);
$datos->execute();

if ($datos == false) {
    echo mysql_error();
} else {
    $cont = 0;
//    while ($rs = mysql_fetch_array($datos)) {
    while($rs = $datos->fetch()){
        $telefonos = new stdClass();
        $telefonos->id = $rs["idTelefono"];
        $telefonos->telefono = $rs["telefono"];
        $array[$cont] = $telefonos;
        $cont++;
    }
    echo json_encode($array);
}