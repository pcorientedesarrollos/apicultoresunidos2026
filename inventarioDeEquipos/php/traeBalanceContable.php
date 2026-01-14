<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if (isset($_GET['idArea'])) {

    $sqlArea = "SELECT eq.idEquipo, eq.nombre AS equipo, eq.caracteristicas, 
                eq.marca, eq.modelo, eq.noSerie, cl.clasificacion, eq.costo,
                (CONCAT(s.subarea,' - ',s.nombre)) AS zona
                FROM equipos eq 
                LEFT JOIN clasificaciones cl
                ON eq.idClasificacion = cl.idClasificacion
                LEFT JOIN subareas s ON s.idSubarea = eq.idSubarea
                WHERE eq.idArea = :idArea
                ORDER BY eq.idEquipo ASC";
    $dato = $con->prepare($sqlArea);
    $dato->bindParam(':idArea', $_GET['idArea']);
    $dato->execute();
    $inventarios = array();
    if ($dato == false) {
        echo 'Error';
    } else {
        while ($rs = $dato->fetch()) {
            $inventario = new stdClass();
            $inventario->idEquipo = $rs["idEquipo"];
            $inventario->equipo = $rs["equipo"];
            $inventario->caracteristicas = $rs["caracteristicas"];
            $inventario->clasificacion = $rs["clasificacion"];
            $inventario->marca = $rs["marca"];
            $inventario->modelo = $rs["modelo"];
            $inventario->serie = $rs["noSerie"];
            $inventario->zona = $rs["zona"];
            $inventario->costo = $rs["costo"];
            $inventarios[] = $inventario;
        }
    }
} elseif (isset($_GET['idClasificacion'])) {

    $sqlClas = "SELECT eq.idEquipo, eq.nombre AS equipo, eq.caracteristicas, 
                eq.marca, eq.modelo, eq.noSerie, cl.clasificacion, eq.costo,
                (CONCAT(s.subarea,' - ',s.nombre)) AS zona
                FROM equipos eq 
                LEFT JOIN clasificaciones cl
                ON eq.idClasificacion = cl.idClasificacion
                LEFT JOIN subareas s ON s.idSubarea = eq.idSubarea
                WHERE cl.idClasificacion = :idClasificacion
                ORDER BY eq.idEquipo ASC";
    $dato = $con->prepare($sqlClas);
    $dato->bindParam(':idClasificacion', $_GET['idClasificacion']);
    $dato->execute();
    $inventarios = array();
    if ($dato == false) {
        echo 'Error';
    } else {
        while ($rs = $dato->fetch()) {
            $inventario = new stdClass();
            $inventario->idEquipo = $rs["idEquipo"];
            $inventario->equipo = $rs["equipo"];
            $inventario->caracteristicas = $rs["caracteristicas"];
            $inventario->clasificacion = $rs["clasificacion"];
            $inventario->marca = $rs["marca"];
            $inventario->modelo = $rs["modelo"];
            $inventario->serie = $rs["noSerie"];
            $inventario->zona = $rs["zona"];
            $inventario->costo = $rs["costo"];
            $inventarios[] = $inventario;
        }
    }
} else {
    $sqlClas = "SELECT c.clasificacion, SUM(e.costo) AS costoTotal
                FROM equipos e
                LEFT JOIN clasificaciones c ON c.idClasificacion = e.idClasificacion 
                GROUP BY e.idClasificacion
                ORDER BY c.clasificacion ASC";
    $dato = $con->prepare($sqlClas);
    $dato->execute();
    $inventarios = array();
    if ($dato == false) {
        echo 'Error';
    } else {
        while ($rs = $dato->fetch()) {
            $inventario = new stdClass();
            $inventario->clasificacion = $rs["clasificacion"];
            $inventario->costoTotal = $rs["costoTotal"];
            $inventarios[] = $inventario;
        }
    }
}
echo json_encode($inventarios);
?>