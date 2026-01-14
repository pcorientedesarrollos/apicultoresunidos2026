<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$error = "";

if (!isset($_GET['idProveedorMantto'])) {
    echo $error = "Falta el codigo";
    die;
}

$idProveedorMantto = $_GET['idProveedorMantto'];

$query = "SELECT * FROM proveedoresmantto WHERE idProveedorMantto = :idProveedorMantto";
$datos = $con->prepare($query);
$datos->bindParam(':idProveedorMantto', $idProveedorMantto);
$datos->execute();

while ($row = $datos->fetch()) {
    $tecnico = new stdClass();
    $tecnico->idProveedorMantto = $row["idProveedorMantto"];
    $tecnico->idArea = $row["idArea"];
    $tecnico->fechaRegistro = $row["fechaRegistro"];
    $tecnico->nombreProveedor = $row["nombreProveedor"];
    $tecnico->nombreContacto = $row["nombreContacto"];
    $tecnico->idTipoProveedor = $row["idTipoProveedor"];
    $tecnico->insumo = $row["insumo"];
    $tecnico->domicilio = $row["domicilio"];
    $tecnico->telefono = $row["telefono"];
    $tecnico->correo = $row["correo"];
    $tecnico->web = $row["web"];
    $tecnico->estatus = $row["estatus"];
    $tecnico->credito = $row["credito"];
}
echo $json_response = json_encode($tecnico);
?>