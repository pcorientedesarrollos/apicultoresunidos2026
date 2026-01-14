<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$error = "";

if (!isset($_GET['idcomprador'])) {
    echo $error = "Falta el codigo";
    die;
}

$codigo = $_GET['idcomprador'];

$query = "SELECT c.idcomprador, z.zona, l.localidad, p.nombre
FROM zonas z LEFT JOIN localidades l ON z.idzona = l.idzona
LEFT JOIN compradores c ON c.idcomprador = z.idcomprador 
LEFT JOIN direccion d ON d.idlocalidad = l.idlocalidad
LEFT JOIN proveedor p ON p.idDireccion = d.idDireccion
WHERE c.idcomprador = :idcomprador ORDER BY z.zona ASC";
$data = $con->prepare($query);
$data->bindParam(':idcomprador', $codigo);
$data->execute();

$localidad = array();
while ($row = $data->fetch()) {
    $zonsComp = new stdClass();
    $zonsComp->idcomprador = $row["idcomprador"];
    $zonsComp->zona = $row["zona"];
    $zonsComp->localidad = $row["localidad"];
    $zonsComp->nombre = $row["nombre"];
    $localidad[] = $zonsComp;
}
echo json_encode($localidad);
?>