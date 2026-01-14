<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idEquipo = $_GET["idEquipo"];

//$sqlP = "SELECT e.nombre AS equipo, mt.idEquipo
//         FROM equipos e 
//         INNER JOIN controlmantenimiento mt ON mt.idEquipo = e.idEquipo
//WHERE mt.idEquipo = :idEquipo";
$sqlP = "SELECT a.idArea, e.nombre AS equipo, e.idEquipo, a.area, e.periodicidad, CONCAT('AF-MID-', e.idEquipo) AS codigo
        FROM equipos e
        INNER JOIN areas a ON a.idArea = e.idArea
        WHERE e.idEquipo = :idEquipo";
$dato = $con->prepare($sqlP);
$dato->bindParam(':idEquipo', $idEquipo);
$dato->execute();

if ($dato == false) {
    echo mysql_error();
} else {
    $manttos = new stdClass();
    while ($rs = $dato->fetch()) {
        $manttos->idEquipo = $rs["idEquipo"];
        $manttos->equipo = $rs["equipo"];
        $manttos->idArea = $rs["idArea"];
        $manttos->area = $rs["area"];
        $manttos->periodicidad = $rs["periodicidad"];
        $manttos->codigo = $rs["codigo"];
        switch ($manttos->periodicidad) {
            case'1':
                $manttos->periodicidad = "Mensual";
                break;
            case'2':
                $manttos->periodicidad = "Trimestral";
                break;
            case'3':
                $manttos->periodicidad = "Semestral";
                break;
            case'4':
                $manttos->periodicidad = "Anual";
                break;
        }
    }
    echo json_encode($manttos);
}
?>