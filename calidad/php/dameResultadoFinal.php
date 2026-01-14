<?php

//include_once '../../DAOConeccion/coneccion.php';
//$cn = new Coneccion();
//$cn->Conectarse();

include_once '../../DAOConeccion/conePDO.php';
$pdo= new conePDO();
$con= $pdo->conectar();

$sql = "SELECT * FROM resultadofinal";
//$datos = mysql_query($sql);

$datos = $con->prepare($sql);
$datos->execute();

$resultadoFinal = array();
if ($sql == false) {
    echo mysql_error();
}
while ($rs = $datos->fetch()) {
    $result = new stdClass();
    $result->id = $rs["idresultadoFinal"];
    $result->descrip = $rs["resultado"];
    $resultadoFinal[] = $result;
}
echo json_encode($resultadoFinal);
