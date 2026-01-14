<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();


if (isset($_GET['idlocalidad'])) {
    $sql = 'SELECT l.idEstado, e.estado
    FROM localidades l
    LEFT JOIN estados e ON e.idEstado = l.idEstado
    WHERE l.idlocalidad = :idlocalidad';
    $result = $conexion->prepare($sql);
    $result->bindParam(':idlocalidad', $_GET['idlocalidad']);
    $result->execute();

    $arrayEstados = array();

    while ($row = $result->fetch()) {
        $estadosMexico = new stdClass();
        $estadosMexico->idEstado = $row["idEstado"];
        $estadosMexico->estado = $row["estado"];
        $arrayEstados[] = $estadosMexico;
    }
} else {
    $sql = 'SELECT idEstado, estado FROM estados ORDER BY estado ASC';

    $result = $conexion->prepare($sql);
    $result->execute();

    $arrayEstados = array();

    while ($row = $result->fetch()) {
        $estadosMexico = new stdClass();
        $estadosMexico->idEstado = $row["idEstado"];
        $estadosMexico->estado = $row["estado"];
        $arrayEstados[] = $estadosMexico;
    }
}

echo $json_response = json_encode($arrayEstados);

