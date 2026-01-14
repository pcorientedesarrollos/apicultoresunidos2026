<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");

$datos = json_decode($miInfo);
$datos = (array) ($datos);

$nombre_completo = join(' ', [$datos['nombres'], $datos['apellido_paterno'], $datos['apellido_materno']]);
$nombres = $datos['nombres'];
$paterno = $datos['apellido_paterno'];
$materno = $datos['apellido_materno'];
$idArea = $datos['idArea'];
$clave = $datos['clave'];
$correo = $datos['correo'];
// $nombre = $datos['nombre'];
$idPuesto = $datos['idPuesto'];
$idPersonalOM = $datos['idPersonalOM'];



if (isset($datos['idPersonalOM'])) {

    $sql = "UPDATE personaloaxaca SET idArea = :idArea, clave = :clave, nombre = :nombre, nombres = :nombres,
        apellido_paterno = :paterno, apellido_materno = :materno, idPuesto = :idPuesto, correo = :correo
        WHERE personaloaxaca.idPersonalOM = :idPersonalOM";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idArea', $idArea);
    $datos->bindParam(':clave', $clave);
    $datos->bindParam(':nombre', $nombre_completo);
    $datos->bindParam(':nombres', $nombres);
    $datos->bindParam(':paterno', $paterno);
    $datos->bindParam(':materno', $materno);
    $datos->bindParam(':idPuesto', $idPuesto);
    $datos->bindParam(':correo', $correo);
    $datos->bindParam(':idPersonalOM', $idPersonalOM);
    $datos->execute();

    if ($datos == false) {
        echo 'Error al ingresar localidad';
    } else {
        echo 'La localidad se agrego exitosamente';
    }
}
?>