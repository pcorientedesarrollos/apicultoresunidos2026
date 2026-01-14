<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if (isset($_GET["articulo1"]) && isset($_GET["articulo2"])) {

    $articulo1 = $_GET["articulo1"];
    $articulo2 = $_GET["articulo2"];

    $sql = "SELECT e.*, a.area, c.clasificacion,
    (CONCAT(s.subarea,' - ',s.nombre)) AS zona
    FROM equipos e 
    LEFT JOIN areas a ON a.idArea = e.idArea
    LEFT JOIN clasificaciones c ON c.idClasificacion = e.idClasificacion
    LEFT JOIN subareas s ON s.idSubarea = e.idSubarea
WHERE e.idEquipo BETWEEN :articulo1 AND :articulo2
ORDER BY e.idEquipo ASC";
    $datos = $con->prepare($sql);
    $datos->bindParam(':articulo1', $articulo1);
    $datos->bindParam('articulo2', $articulo2);
    $datos->execute();
    if ($datos == false) {
        echo 'ERROR';
    } else {

        $informacionArticulo = array();
        while ($rs = $datos->fetch()) {
            $articulo = new stdClass();
            $articulo->idEquipo = $rs["idEquipo"];
            $articulo->nombre = $rs["nombre"];
            $articulo->marca = $rs["marca"];
            $articulo->modelo = $rs["modelo"];
            $articulo->serie = $rs["noSerie"];
            $articulo->caracteristicas = $rs["caracteristicas"];
            $articulo->clasificacion = $rs["clasificacion"];
            $articulo->area = $rs["area"];
            $articulo->zona = $rs["zona"];
            $informacionArticulo [] = $articulo;
        }
    }
}


if (isset($_GET["nombre"])) {

    $nombre = $_GET["nombre"];

    $sql = "SELECT e.*, a.area, c.clasificacion,
    (CONCAT(s.subarea,' - ',s.nombre)) AS zona
    FROM equipos e 
    LEFT JOIN areas a ON a.idArea = e.idArea
    LEFT JOIN clasificaciones c ON c.idClasificacion = e.idClasificacion
    LEFT JOIN subareas s ON s.idSubarea = e.idSubarea
        WHERE e.nombre regexp :nombre;";
//    WHERE e.nombre like '%$nombre%'";
    $datos = $con->prepare($sql);
    $datos->bindParam(':nombre', $nombre);
    $datos->execute();
    if ($datos == false) {
        echo 'ERROR';
    } else {

        $informacionArticulo = array();
        while ($rs = $datos->fetch()) {
            $articulo = new stdClass();
            $articulo->idEquipo = $rs["idEquipo"];
            $articulo->nombre = $rs["nombre"];
            $articulo->marca = $rs["marca"];
            $articulo->modelo = $rs["modelo"];
            $articulo->serie = $rs["noSerie"];
            $articulo->caracteristicas = $rs["caracteristicas"];
            $articulo->clasificacion = $rs["clasificacion"];
            $articulo->area = $rs["area"];
            $articulo->zona = $rs["zona"];
            $informacionArticulo [] = $articulo;
        }
    }
}


echo json_encode($informacionArticulo);
