<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

//$idArea = $_GET['idArea'];

if (isset($_GET["idArea"])) {
    $query = "SELECT pf.idArea, a.area, e.nombre AS equipo, e.idEquipo, e.periodicidad
FROM programaciondefechas pf 
LEFT JOIN areas a ON a.idArea = pf.idArea
LEFT JOIN equipos e ON e.idEquipo = pf.idEquipo
WHERE pf.idArea = :idArea
GROUP BY equipo 
ORDER BY a.area ASC
";
    $datos = $con->prepare($query);
    $datos->bindParam(':idArea', $_GET["idArea"]);
    $datos->execute();

    $arrayCrono = array();
    while ($row = $datos->fetch()) {
        $cronograma = new stdClass();
        $cronograma->periodicidad = $row["periodicidad"];
        $cronograma->area = $row["area"];
        $cronograma->idArea = $row["idArea"];
        $cronograma->equipo = $row["equipo"];
        $cronograma->idEquipo = $row["idEquipo"];

        switch ($cronograma->periodicidad) {
            case '0':
                $cronograma->periodicidad = "Aún no asignada";
                break;
            case '1':
                $cronograma->periodicidad = "Mensual";
                break;
            case '2':
                $cronograma->periodicidad = "Trimestral";
                break;
            case '3':
                $cronograma->periodicidad = "Semestral";
                break;
            case '4':
                $cronograma->periodicidad = "Anual";
                break;
        }

//        $sqlFechas = "SELECT pf.fechaProgramada, pf.idMes, cm.fechaReal
//FROM programaciondefechas pf 
//LEFT JOIN controlmantenimiento cm ON cm.idMes = pf.idMes AND cm.idEquipo = pf.idEquipo
//WHERE pf.idEquipo = :idEquipo 
//ORDER BY pf.idMes ASC";
        $sqlFechas = "SELECT pf.fechaProgramada, pf.idMes
FROM programaciondefechas pf 
WHERE pf.idEquipo = :idEquipo 
ORDER BY pf.idMes ASC";
        $datosMdl = $con->prepare($sqlFechas);
        $datosMdl->bindParam(':idEquipo', $cronograma->idEquipo);
        $datosMdl->execute();

        if ($datosMdl == false) {
            echo mysql_error();
        } else {
            while ($rsModulos = $datosMdl->fetch()) {
                $datosFechas = new stdClass();
                $datosFechas->fechaProgramada = $rsModulos["fechaProgramada"];
//                $datosFechas->fechaReal = $rsModulos["fechaReal"];
                $datosFechas->idMes = $rsModulos["idMes"];

                $sqlReal = "SELECT fechaReal FROM controlmantenimiento WHERE idEquipo = :idEquipo AND idMes = :idMes";
                $rel = $con->prepare($sqlReal);
                $rel->bindParam(':idEquipo', $cronograma->idEquipo);
                $rel->bindParam(':idMes', $datosFechas->idMes);
                $rel->execute();
                while ($rsModulos = $rel->fetch()) {
                    $datosFechas->fechaReal = $rsModulos["fechaReal"];
                }
                $cronograma->listaFechas[] = $datosFechas;
            }

            $sqlFechasE = "SELECT cm.fechaReal AS extraordinaria, cm.idMes AS mes
                               FROM controlmantenimiento cm 
                               WHERE cm.idEquipo = :idEquipo AND cm.tipo = '1'
                               ORDER BY cm.idMes ASC";
            $ext = $con->prepare($sqlFechasE);
            $ext->bindParam(':idEquipo', $cronograma->idEquipo);
            $ext->execute();
            while ($rsModulos = $ext->fetch()) {
                $extra = new stdClass();
                $extra->extraordinaria = $rsModulos["extraordinaria"];
                $extra->mes = $rsModulos["mes"];

                $cronograma->extraordinarias[] = $extra;
            }

            $arrayCrono[] = $cronograma;
        }
    }
} else {
    $query = "SELECT pf.idArea, a.area, e.nombre AS equipo, e.idEquipo, e.periodicidad
FROM programaciondefechas pf 
LEFT JOIN areas a ON a.idArea = pf.idArea
LEFT JOIN equipos e ON e.idEquipo = pf.idEquipo
GROUP BY equipo 
ORDER BY a.area ASC";
    $datos = $con->prepare($query);
    $datos->execute();

    $arrayCrono = array();
    while ($row = $datos->fetch()) {
        $cronograma = new stdClass();
        $cronograma->periodicidad = $row["periodicidad"];
        $cronograma->area = $row["area"];
        $cronograma->idArea = $row["idArea"];
        $cronograma->equipo = $row["equipo"];
        $cronograma->idEquipo = $row["idEquipo"];

        switch ($cronograma->periodicidad) {
            case '0':
                $cronograma->periodicidad = "Aún no asignada";
                break;
            case '1':
                $cronograma->periodicidad = "Mensual";
                break;
            case '2':
                $cronograma->periodicidad = "Trimestral";
                break;
            case '3':
                $cronograma->periodicidad = "Semestral";
                break;
            case '4':
                $cronograma->periodicidad = "Anual";
                break;
        }

//        $sqlFechas = "SELECT pf.fechaProgramada, pf.idMes, cm.fechaReal 
//FROM programaciondefechas pf 
//LEFT JOIN controlmantenimiento cm ON cm.idMes = pf.idMes AND cm.idEquipo = pf.idEquipo
//WHERE pf.idEquipo = :idEquipo AND cm.tipo = '0'
//ORDER BY pf.idMes ASC";
        $sqlFechas = "SELECT pf.fechaProgramada, pf.idMes
FROM programaciondefechas pf 
WHERE pf.idEquipo = :idEquipo
ORDER BY pf.idMes ASC";
        $datosMdl = $con->prepare($sqlFechas);
        $datosMdl->bindParam(':idEquipo', $cronograma->idEquipo);
        $datosMdl->execute();

        if ($datosMdl == false) {
            echo mysql_error();
        } else {
            while ($rsModulos = $datosMdl->fetch()) {
                $datosFechas = new stdClass();
                $datosFechas->fechaProgramada = $rsModulos["fechaProgramada"];
//                $datosFechas->fechaReal = $rsModulos["fechaReal"];
                $datosFechas->idMes = $rsModulos["idMes"];

                $sqlReal = "SELECT fechaReal FROM controlmantenimiento WHERE idEquipo = :idEquipo AND idMes = :idMes";
                $rel = $con->prepare($sqlReal);
                $rel->bindParam(':idEquipo', $cronograma->idEquipo);
                $rel->bindParam(':idMes', $datosFechas->idMes);
                $rel->execute();
                while ($rsModulos = $rel->fetch()) {
                    $datosFechas->fechaReal = $rsModulos["fechaReal"];
                }
                $cronograma->listaFechas[] = $datosFechas;
            }

            $sqlFechasE = "SELECT cm.fechaReal AS extraordinaria, cm.idMes AS mes
                               FROM controlmantenimiento cm 
                               WHERE cm.idEquipo = :idEquipo AND cm.tipo = '1'
                               ORDER BY cm.idMes ASC";
            $ext = $con->prepare($sqlFechasE);
            $ext->bindParam(':idEquipo', $cronograma->idEquipo);
            $ext->execute();
            while ($rsModulos = $ext->fetch()) {
                $extra = new stdClass();
                $extra->extraordinaria = $rsModulos["extraordinaria"];
                $extra->mes = $rsModulos["mes"];

                $cronograma->extraordinarias[] = $extra;
            }


            $arrayCrono[] = $cronograma;
        }
    }
}

echo json_encode($arrayCrono);
