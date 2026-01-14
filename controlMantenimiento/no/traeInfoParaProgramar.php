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

$sql = "SELECT pf.idArea, pf.idEquipo, a.area, e.nombre,e.periodicidad
        FROM programaciondefechas pf
        INNER JOIN areas a ON a.idArea = pf.idArea
        INNER JOIN equipos e ON e.idEquipo = pf.idEquipo
        WHERE pf.idEquipo = :idEquipo";
$dato = $con->prepare($sql);
$dato->bindParam(':idEquipo', $idEquipo);
$dato->execute();

while ($row = $dato->fetch()) {
    $programacion = new stdClass();
    $programacion->idEquipo = $row["idEquipo"];
    $programacion->nombre = $row["nombre"];
    $programacion->idArea = $row["idArea"];
    $programacion->area = $row["area"];
    $programacion->periodicidad = $row["periodicidad"];

    switch ($programacion->periodicidad) {
        case'1':
            $programacion->periodicidad = "Mensual";
            break;
        case'2':
            $programacion->periodicidad = "Trimestral";
            break;
        case'3':
            $programacion->periodicidad = "Semestral";
            break;
        case'4':
            $programacion->periodicidad = "Anual";
            break;
    }
}

echo $json_response = json_encode($programacion);
?>