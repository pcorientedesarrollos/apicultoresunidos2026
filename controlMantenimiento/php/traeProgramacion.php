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

//$sql = "SELECT pf.idArea, pf.idEquipo, a.area, e.nombre,e.periodicidad, e.programacion
//        FROM programaciondefechas pf
//        INNER JOIN areas a ON a.idArea = pf.idArea
//        INNER JOIN equipos e ON e.idEquipo = pf.idEquipo
//        WHERE pf.idEquipo = :idEquipo";
$sql = "SELECT a.idArea, e.idEquipo, a.area, e.nombre,e.periodicidad, e.programacion,CONCAT('AF-MID-', e.idEquipo) AS codigo
        FROM equipos e
        INNER JOIN areas a ON a.idArea = e.idArea
        WHERE e.idEquipo = :idEquipo";
$dato = $con->prepare($sql);
$dato->bindParam(':idEquipo', $idEquipo);
$dato->execute();

while ($row = $dato->fetch()) {
    $programacion = new stdClass();
    $programacion->idEquipo = $row["idEquipo"];
    $programacion->nombre = $row["nombre"];
    $programacion->idArea = $row["idArea"];
    $programacion->area = $row["area"];
    $programacion->programacion = $row["programacion"];
    $programacion->codigo = $row["codigo"];
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

    if ($programacion->programacion > "0") {
        $programacion->arregloDeFechas = array();
        $query = "SELECT pf.idProgramacion, pf.fechaProgramada, pf.idMes, m.mes
        FROM programaciondefechas pf
        INNER JOIN meses m ON m.idMes = pf.idMes
        WHERE pf.idEquipo = :idEquipo";
        $datos = $con->prepare($query);
        $datos->bindParam(':idEquipo', $idEquipo);
        $datos->execute();

        while ($row = $datos->fetch()) {
            $arrayFechas = new stdClass();
            $arrayFechas->idProgramacion = $row["idProgramacion"];
            $arrayFechas->fechaProgramada = $row["fechaProgramada"];
            $arrayFechas->idMes = $row["idMes"];
            $arrayFechas->mes = $row["mes"];
            $programacion->arregloDeFechas[] = $arrayFechas;
        }
    }
}

echo $json_response = json_encode($programacion);
?>