<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$tipo = $_GET['tipo'];

$sql = "SELECT * FROM zonascera WHERE tipo = :tipo";
$datos = $con->prepare($sql);
$datos->bindParam(':tipo', $tipo);
$datos->execute();

if ($datos == true) {
    $zonasCera = array();
    while ($row = $datos->fetch()) {
        $zona = new stdClass();
        $zona->idZonaCera = $row["idZonaCera"];
        $zona->nombre = $row["nombre"];
        $zonasCera[] = $zona;
    }
    echo $json_response = json_encode($zonasCera);
} else {
    echo mysql_error();
}
?>