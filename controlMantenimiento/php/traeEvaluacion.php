<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idEvaluacion = $_GET['idEvaluacion'];

$query = "SELECT e.*, p.*
         FROM evaluaciones e 
         INNER JOIN proveedoresmantto p ON p.idProveedorMantto = e.idProveedorMantto
         WHERE idEvaluacion = :idEvaluacion";
$datos = $con->prepare($query);
$datos->bindParam(':idEvaluacion', $idEvaluacion);
$datos->execute();

while ($row = $datos->fetch()) {
    $infoMantto = new stdClass();
    $infoMantto->idEvaluacion = $row["idEvaluacion"];
    $infoMantto->idTipoProveedor = $row["idTipoProveedor"];
    $infoMantto->idProveedorMantto = $row["idProveedorMantto"];
    $infoMantto->sistemaCalidad = $row["sistemaCalidad"];
    $infoMantto->idCertificacion = $row["idCertificacion"];
    $infoMantto->certificacion = $row["certificacion"];
    $infoMantto->nombre = $row["nombre"];
    $infoMantto->cargo = $row["cargo"];
    $infoMantto->fecha = $row["fecha"];
    $infoMantto->cuestionario = $row["cuestionario"];
    $infoMantto->nombreProveedor = $row["nombreProveedor"];
    $infoMantto->domicilio = $row["domicilio"];
    $infoMantto->telefono = $row["telefono"];
    $infoMantto->correo = $row["correo"];
    $infoMantto->web = $row["web"];
    $infoMantto->nombreContacto = $row["nombreContacto"];
}
echo json_encode($infoMantto);
?>