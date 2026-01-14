<?php

include '../DAOConeccion/coneccion.php';
$cn = new Coneccion();
$cn->Conectarse();
$json = file_get_contents("php://input");
$datos = json_decode($json);
$info = $datos->valor;
$idUsuario = $_GET["idUsuario"];
$sqlEliminarPermisos = "DELETE  FROM permisos WHERE idUsuario = '$idUsuario'";
$datos = mysql_query($sqlEliminarPermisos);
if ($datos == false) {
    echo mysql_error();
} else {
    foreach ($info as $permisos) {
        if ($permisos->autorizo == 1) {
            $sql = "INSERT INTO permisos (idAcceso, idUsuario) VALUES ('" . $permisos->idModulo . "','$idUsuario')";
            $datosPermisos = mysql_query($sql);
            if ($datosPermisos == false) {
                echo mysql_error();
                break;
            }
        }
    }
}