<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idArea = $_GET['idArea'];

$sql = "SELECT area FROM areas WHERE idArea = :idArea";
$dato = $con->prepare($sql);
$dato->bindParam(':idArea', $idArea);
$dato->execute();

while ($row = $dato->fetch()) {
    $equipoA = new stdClass();
    $equipoA->area = $row["area"];
    $equipoA->equiposPorArea = array();

//    $query = "SELECT p.idProgramacion, a.area, p.idEquipo, e.nombre, e.periodicidad
//FROM programaciondefechas p 
//LEFT JOIN areas a ON a.idArea = p.idArea
//INNER JOIN equipos e ON e.idEquipo = p.idEquipo
//WHERE a.idArea = :idArea
//GROUP BY p.idEquipo
//ORDER BY a.area ASC";
    $query = "SELECT e.nombre, e.idEquipo, e.programacion FROM equipos e INNER JOIN areas a ON a.idArea = e.idArea WHERE e.idArea = :idArea";
    $datos = $con->prepare($query);
    $datos->bindParam(':idArea', $idArea);
    $datos->execute();

    $arrayR = array();
    while ($row = $datos->fetch()) {
        $menuProgram = new stdClass();
//        $menuProgram->idProgramacion = $row["idProgramacion"];
//        $menuProgram->area = $row["area"];
        $menuProgram->idEquipo = $row["idEquipo"];
        $menuProgram->nombre = $row["nombre"];
        $menuProgram->programacion = $row["programacion"];



//        $menuProgram->periodicidad = $row["periodicidad"];
//
//        switch ($menuProgram->periodicidad) {
//            case '1':
//                $menuProgram->periodicidad = "Mensual";
//                break;
//            case '2':
//                $menuProgram->periodicidad = "Trimestral";
//                break;
//            case '3':
//                $menuProgram->periodicidad = "Semestral";
//                break;
//            case'4':
//                $menuProgram->periodicidad = "Anual";
//                break;
//        }

        $equipoA->equiposPorArea[] = $menuProgram;
    }
}

echo json_encode($equipoA);
?>