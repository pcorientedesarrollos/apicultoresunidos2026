<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$error = "";

if (!isset($_GET['idEquipo'])) {
    echo $error = "Falta el codigo";
    die;
}

$idEquipo = $_GET['idEquipo'];

$query = "SELECT e.*, a.area,
    (CONCAT(s.subarea,' - ',s.nombre)) AS zona
        FROM equipos e LEFT JOIN areas a ON a.idArea = e.idArea 
        LEFT JOIN subareas s ON s.idSubarea = e.idSubarea
        WHERE e.idEquipo = :idEquipo";
$datos = $con->prepare($query);
$datos->bindParam(':idEquipo', $idEquipo);
$datos->execute();

while ($row = $datos->fetch()) {
    $equipo = new stdClass();
    $equipo->idEquipo = $row["idEquipo"];
    $equipo->idArea = $row["idArea"];
    $equipo->area = $row["area"];
    $equipo->idSubarea = $row["idSubarea"];
    $equipo->zona = $row["zona"];
    $equipo->nombre = $row["nombre"];
    $equipo->codigo = $row["codigo"];
    $equipo->marca = $row["marca"];
    $equipo->modelo = $row["modelo"];
    $equipo->noSerie = $row["noSerie"];
    $equipo->costo = $row["costo"];
    $equipo->caracteristicas = $row["caracteristicas"];
    $equipo->idClasificacion = $row["idClasificacion"];
    $equipo->mantto = $row["mantto"];
    $equipo->periodicidad = $row["periodicidad"];
}
echo $json_response = json_encode($equipo);
?>