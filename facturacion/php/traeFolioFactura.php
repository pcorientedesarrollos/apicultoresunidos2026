<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$id = $_GET['id'];

    $query = "SELECT folioFactura FROM listadepesos WHERE idTamborPeso = :id";
    $datos = $con->prepare($query);
    $datos->bindParam(':id', $id);
    $datos->execute();

    while ($row = $datos->fetch()) {
        $infoCliente = new stdClass();
        $infoCliente->folioFactura = $row["folioFactura"];
    }
    echo json_encode($infoCliente);



?>