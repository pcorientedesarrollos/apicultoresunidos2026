<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();

$sql = "SELECT idlocalidad, localidad FROM localidades WHERE estado = 1";
if(isset($_GET['zona'])) {
    $zona = $_GET['zona'];
    $sql .= " AND idzona = '0' OR idzona = '$zona'";
}else if(isset($_GET['creando'])){
    $sql .= " AND idzona = '0'";
}
$sql .= " ORDER BY localidad ASC";
$datos = $con->prepare($sql);
$datos->execute();

$arrayLocalidades = array();
while ($row = $datos->fetch()) {
    $localidadC = new stdClass();
    $localidadC->idlocalidad = $row["idlocalidad"];
    $localidadC->localidad = $row["localidad"];
    $arrayLocalidades[] = $localidadC;
}

echo $json_response = json_encode($arrayLocalidades);
?>