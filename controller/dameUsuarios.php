<?php
include_once '../DAOConeccion/coneccion.php';
$cn  = new Coneccion();
$cn->Conectarse();
$sql ="SELECT * FROM usuario";
$datos = mysql_query($sql);
if($datos == false){
    echo mysql_error();
}
else{
    $usuarios =  array();
    while($rs = mysql_fetch_array($datos)){
        $usuario = new stdClass();
        $usuario->idUsuario = $rs["idusuario"];
        $usuario->nombre = $rs["nombre"];
        $usuarios[] = $usuario;
    }
    echo json_encode($usuarios);
}