<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");

$datos = json_decode($miInfo);
$datos = (array) ($datos);

$idzona = $datos['idzona'];
$zona = $datos['zona'];
$idcomprador = $datos['idcomprador'];

if (isset($datos['idzona'])) {

    $sql = "UPDATE zonas SET zona = :zona, idcomprador = :idcomprador WHERE zonas.idzona = :idzona";
    $data = $con->prepare($sql);
    $data->bindParam(':zona', $zona);
    $data->bindParam(':idcomprador', $idcomprador);
    $data->bindParam(':idzona', $idzona);
    $data->execute();

    if ($data == false) {
        echo 'Error al ingresar localidad';
    } else {
        echo 'La zona se agregó exitosamente';
    }
}
?>