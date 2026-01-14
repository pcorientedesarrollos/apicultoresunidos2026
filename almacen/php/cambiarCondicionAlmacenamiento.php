<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$valor = $_GET["valor"];
$id = $_GET["id"];
$semana = $_GET["semana"];

switch ($semana) {
    case 1:
        $sql = "UPDATE condicionesalmacenamiento set semana1 = :valor WHERE idCondicionAlmacenamiento = :id";
        break;
    case 2:
        $sql = "UPDATE condicionesalmacenamiento set semana2 = :valor WHERE idCondicionAlmacenamiento = :id";
        break;
    case 3:
        $sql = "UPDATE condicionesalmacenamiento set semana3 = :valor WHERE idCondicionAlmacenamiento = :id";
        break;
    case 4:
        $sql = "UPDATE condicionesalmacenamiento set semana4 = :valor WHERE idCondicionAlmacenamiento = :id";
        break;
    case 5:
        $sql = "UPDATE condicionesalmacenamiento set semana5 = :valor WHERE idCondicionAlmacenamiento = :id";
        break;
}
$datos = $con->prepare($sql);
$datos->bindParam(':valor', $valor);
$datos->bindParam(':id', $id);
$datos->execute();
if ($datos == false) {
    echo mysql_error();
} else {
    echo "Estado cambiado satisfactoriamente";
}
?>