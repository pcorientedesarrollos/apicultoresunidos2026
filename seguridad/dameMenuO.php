<?php

//include_once '../DAOConeccion/coneccion.php';
//$cn = new Coneccion();

include_once '../DAOConeccion/conePDO.php';
$pdo= new conePDO;
$con=$pdo->conectar();
        
$idPerfil = $_GET["idPerfil"];
$sql = "SELECT s.*, p.idPerfil FROM secciones s
INNER JOIN permisos p ON p.idSeccion = s.idSeccion
WHERE idPerfil = :idPerfil";
//$cn->Conectarse();
//$datosSecciones = mysql_query($sql);

$datosSecciones=$con->prepare($sql);
$datosSecciones->bindParam(':idPerfil', $idPerfil);
$datosSecciones->execute();

$menu = array();
if ($datosSecciones == false) {
    echo mysql_error();
} else {
    while ($rs = $datosSecciones->fetch()) {
        $seccion = new stdClass();
        $seccion->idSeccion = $rs["idSeccion"];
        $seccion->seccion = $rs["seccion"];
//        $seccion->lstSubmenus = array();
        //'" . $seccion->idSeccion . "'
        $sqlModulos = "SELECT * FROM modulos WHERE idSeccion = :idSeccion ORDER BY modulo ASC";
       // $datosMdl = mysql_query($sqlModulos);
        $datosMdl=$con->prepare($sqlModulos);
        $datosMdl->bindParam(':idSeccion', $seccion->idSeccion);
        $datosMdl->execute();
        
        if ($datosMdl == false) {
            echo mysql_error();
        } else {
            while ($rsModulos = $datosMdl->fetch()) {
                $datosModulos = new stdClass();
                $datosModulos->idModulo = $rsModulos["idModulo"];
                $datosModulos->modulo = $rsModulos["modulo"];
                $datosModulos->ruta = $rsModulos["ruta"];
                $datosModulos->newSistema = $rsModulos["newSistema"];
                $seccion->lstSubMenus[] = $datosModulos;
            }
        }
        $menu [] = $seccion;
    }
}
echo json_encode($menu);


