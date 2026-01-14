<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$info = json_decode($miInfo);

if (isset($_GET['idEvaluacion'])) {
    $sqlUp = "UPDATE evaluaciones SET idTipoProveedor = :idTipoProveedor, idProveedorMantto = :idProveedorMantto, sistemaCalidad = :sistemaCalidad, idCertificacion = :idCertificacion, certificacion = :certificacion, nombre = :nombre, cargo = :cargo, fecha = :fecha, cuestionario = :cuestionario WHERE idEvaluacion = :idEvaluacion";
    $datos = $con->prepare($sqlUp);
    $datos->bindParam(':idTipoProveedor', $info->idTipoProveedor);
    $datos->bindParam(':idProveedorMantto', $info->idProveedorMantto);
    $datos->bindParam(':sistemaCalidad', $info->sistemaCalidad);
    $datos->bindParam(':idCertificacion', $info->idCertificacion);
    $datos->bindParam(':certificacion', $info->certificacion);
    $datos->bindParam(':nombre', $info->nombre);
    $datos->bindParam(':cargo', $info->cargo);
    $datos->bindParam(':fecha', $info->fecha);
    $datos->bindParam(':cuestionario', $info->cuestionario);
    $datos->bindParam(':idEvaluacion', $info->idEvaluacion);
    $datos->execute();
} else {
    $sql = "INSERT INTO evaluaciones (idTipoProveedor, idProveedorMantto, sistemaCalidad, idCertificacion, certificacion, nombre, cargo, fecha, cuestionario) VALUES (:idTipoProveedor, :idProveedorMantto, :sistemaCalidad, :idCertificacion, :certificacion, :nombre, :cargo, :fecha, :cuestionario)";
    $dato = $con->prepare($sql);
    $dato->bindParam(':idTipoProveedor', $info->idTipoProveedor);
    $dato->bindParam(':idProveedorMantto', $info->idProveedorMantto);
    $dato->bindParam(':sistemaCalidad', $info->sistemaCalidad);
    $dato->bindParam(':idCertificacion', $info->idCertificacion);
    $dato->bindParam(':certificacion', $info->certificacion);
    $dato->bindParam(':nombre', $info->nombre);
    $dato->bindParam(':cargo', $info->cargo);
    $dato->bindParam(':fecha', $info->fecha);
    $dato->bindParam(':cuestionario', $info->cuestionario);
    $dato->execute();
}
?>