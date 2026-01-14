<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();


$id = $_GET['id'];

if (isset($_GET['pdf'])) {
    $query = " SELECT idCarta, idProveedor, archivoCarta FROM archivoscartas WHERE idProveedor =:id AND tipo = 2";
    $result = $conexion->prepare($query);
    $result->bindParam(':id', $id);
    $result->execute();

    $arrayCartas = array();

    while ($row = $result->fetch()) {
        $docCarta = new stdClass();
        $docCarta->idCarta = $row["idCarta"];
        $docCarta->idProveedor = $row["idProveedor"];
        $docCarta->archivoCarta = $row["archivoCarta"];
        $arrayCartas[] = $docCarta;
    }
} else {
    $query = " SELECT idCarta, idProveedor, archivoCarta FROM archivoscartas WHERE idProveedor =:id AND tipo = 1";
    $result = $conexion->prepare($query);
    $result->bindParam(':id', $id);
    $result->execute();

    $arrayCartas = array();

    while ($row = $result->fetch()) {
        $docCarta = new stdClass();
        $docCarta->idCarta = $row["idCarta"];
        $docCarta->idProveedor = $row["idProveedor"];
        $docCarta->archivoCarta = $row["archivoCarta"];
        $arrayCartas[] = $docCarta;
    }
}


//# JSON-encode the response
echo $json_response = json_encode($arrayCartas);
?>