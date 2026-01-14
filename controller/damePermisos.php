<?php

include_once '../DAOConeccion/coneccion.php';
$cn = new Coneccion();
$cn->Conectarse();
$idPerfil = $_GET["idPerfil"];
//$sql = "SELECT * FROM permisos p
//INNER JOIN accesos ac
//on p.idAcceso = ac.idAcceso
//WHERE idUsuario= $idUsuario";
//$sql="SELECT * FROM permisos p 
//        INNER JOIN accesos ac on p.idAcceso = ac.idAcceso
//        LEFT JOIN submenus sm ON p.idAcceso = sm.idAcceso 
//        WHERE idUsuario= '$idUsuario' ";

$sql = "SELECT * FROM permisos p 
        INNER JOIN secciones ac on p.idSeccion = ac.idSeccion
        WHERE idPerfil= '$idPerfil' ";

$datos = mysql_query($sql);
if ($datos == false) {
    echo mysql_error();
} else {
    $listaPermisos = array();
    while ($rs = mysql_fetch_array($datos)) {
        $rutas = new stdClass();
        $rutas->vista = $rs["modulo"];
        $rutas->acceso = $rs["ruta"];
//        $rutas->subMenu = $rs["subMenu"];
        $listaPermisos [] = $rutas;
    }
    echo json_encode($listaPermisos);
}
