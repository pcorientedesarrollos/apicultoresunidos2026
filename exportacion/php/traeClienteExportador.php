<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if (isset($_GET['idClienteExportador'])) {

    $idClienteExportador = $_GET['idClienteExportador'];

    $query = "SELECT *
             FROM clientesexportadores
             WHERE idClienteExportador = :idClienteExportador";
    $datos = $con->prepare($query);
    $datos->bindParam(':idClienteExportador', $idClienteExportador);
    $datos->execute();

    $infoCliente = $datos->fetch(PDO::FETCH_ASSOC);
    echo json_encode($infoCliente);

} else {
    $query = "SELECT * FROM clientesexportadores";
    $datos = $con->prepare($query);
    $datos->execute();

    $array = array();
    $array = $datos->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($array);
}
?>