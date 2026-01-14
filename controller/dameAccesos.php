<?php

include_once '../DAOConeccion/coneccion.php';
$cn = new Coneccion();
$cn->Conectarse();
$idUsuario = $_GET["idUsuario"];
$sql = "SELECT  * from accesos ac 
LEFT JOIN permisos p 
on ac.idAcceso = p.idAcceso
LEFT JOIN usuario u 
ON p.idUsuario = u.idusuario
GROUP BY ac.idAcceso
ORDER BY ac.idAcceso
";
//WHERE u.idUsuario = $idUsuario";
$datos = mysql_query($sql);
if ($datos == false) {
    echo mysql_error();
} else {
    $modulosAutorizados = array();
    while ($rs = mysql_fetch_array($datos)) {


        $modulo = new stdClass();
        $modulo->idModulo = $rs[0];
        $modulo->modulo = $rs["modulo"];
        $sqlAutorizado = "SELECT * "
                . "FROM permisos WHERE idAcceso = '" . $rs["idAcceso"] . "' and  idUsuario = '$idUsuario'";
        $datosPermiso = mysql_query($sqlAutorizado);
        if ($datosPermiso == false) {
            echo mysql_error();
        } else {
            $modulo->autorizo = "0";
            while ($rsAcceso = mysql_fetch_array($datosPermiso)) {
                $modulo->autorizo = "1";
            }
        }
//        $autorizado = $rs["ac.idAcceso"];
//        if ($autorizado == "null") {
//            $modulo->autorizado = 0;
//        } else {
//            $modulo->autorizado = 1;
//        }
        $modulosAutorizados[] = $modulo;
    }
    echo json_encode($modulosAutorizados);
}