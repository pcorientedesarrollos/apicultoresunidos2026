<?php

include_once '../../../DAOConeccion/coneccion.php';
$cn = new Coneccion();
$cn->Conectarse();

$error = "";

if (!isset($_GET['idcomprador'])) {
    echo $error = "Falta el codigo";
    die;
}

$codigo = $_GET['idcomprador'];

$query = "SELECT p.nombre FROM proveedor p 
LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
LEFT JOIN zonas z ON z.idzona = l.idzona
LEFT JOIN compradores c ON c.idcomprador = z.idcomprador
WHERE c.idcomprador = :idcomprador ORDER BY p.nombre ASC";

$data = $con->prepare($query);
$data->bindParam(':idcomprador', $codigo);
$data->execute();

$nombreProvee = array();
while ($row = $data->fetch()) {
    $nombreP = new stdClass();
    $nombreP->nombre = $row["nombre"];
    $nombreProvee[] = $nombreP;
}

echo json_encode($nombreProvee);
?>