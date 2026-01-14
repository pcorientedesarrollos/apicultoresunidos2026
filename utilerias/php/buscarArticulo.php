<?php

include_once '../../DAOConeccion/coneccion.php';
$cn = new Coneccion();
$cn->Conectarse();

//$id = $_GET["id"];

if (isset($_GET["id"])) {

    $id = $_GET["id"];

    $sql = "SELECT e.*, a.area, c.clasificacion,
    (CONCAT(s.subarea,' - ',s.nombre)) AS zona
    FROM equipos e 
    LEFT JOIN areas a ON a.idArea = e.idArea
    LEFT JOIN clasificaciones c ON c.idClasificacion = e.idClasificacion
    LEFT JOIN subareas s ON s.idSubarea = e.idSubarea
WHERE e.idEquipo = '$id'";
    $datos = mysql_query($sql);

    if ($datos == false) {
        echo mysql_error();
    } else {
        if (mysql_affected_rows() == 0) {
            echo 0;
        } else {
            while ($rs = mysql_fetch_array($datos)) {
                $articulo = new stdClass();
                $articulo->idEquipo = $rs["idEquipo"];
                $articulo->nombre = utf8_encode($rs["nombre"]);
                $articulo->marca = utf8_encode($rs["marca"]);
                $articulo->modelo = utf8_encode($rs["modelo"]);
                $articulo->serie = utf8_encode($rs["noSerie"]);
                $articulo->caracteristicas = utf8_encode($rs["caracteristicas"]);
                $articulo->clasificacion = utf8_encode($rs["clasificacion"]);
                $articulo->area = utf8_encode($rs["area"]);
                $articulo->zona = utf8_encode($rs["zona"]);
            }
        }
    }
}

//if (isset($_GET["nombre"])) {
//    $nombre = $_GET["nombre"];
//
//    $sql = "SELECT e.*, a.area, c.clasificacion,
//    (CONCAT(s.subarea,' - ',s.nombre)) AS zona
//    FROM equipos e 
//    LEFT JOIN areas a ON a.idArea = e.idArea
//    LEFT JOIN clasificaciones c ON c.idClasificacion = e.idClasificacion
//    LEFT JOIN subareas s ON s.idSubarea = e.idSubarea
//WHERE e.nombre like '$nombre'";
//    $datos = mysql_query($sql);
//
//    if ($datos == false) {
//        echo mysql_error();
//    } else {
//        if (mysql_affected_rows() == 0) {
//            echo 0;
//        } else {
//            while ($rs = mysql_fetch_array($datos)) {
//                $articulo = new stdClass();
//                $articulo->idEquipo = $rs["idEquipo"];
//                $articulo->nombre = utf8_encode($rs["nombre"]);
//                $articulo->marca = utf8_encode($rs["marca"]);
//                $articulo->modelo = utf8_encode($rs["modelo"]);
//                $articulo->serie = utf8_encode($rs["noSerie"]);
//                $articulo->caracteristicas = utf8_encode($rs["caracteristicas"]);
//                $articulo->clasificacion = utf8_encode($rs["clasificacion"]);
//                $articulo->area = utf8_encode($rs["area"]);
//                $articulo->zona = utf8_encode($rs["zona"]);
//            }
//        }
//    }
//}

echo json_encode($articulo);
