<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = "SELECT * FROM meses";
$datos = $con->prepare($sql);
$datos->execute();

if ($datos == true) {
    $arrayMeses = array();
    while ($row = $datos->fetch()) {
        $mes = new stdClass();
        $mes->idMes = $row["idMes"];
        $mes->mes = $row["mes"];
        $arrayMeses[] = $mes;
    }
    echo $json_response = json_encode($arrayMeses);
} else {
    echo mysql_error();
}
?>