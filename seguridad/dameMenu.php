<?php

$post = file_get_contents('php://input');
if ($post) {
    header('Content-Type: application/json');
    include_once '../DAOConeccion/conePDO.php';
    $pdo = new conePDO;
    
    $postdata = json_decode($post);
    $idPerfil = $postdata->idPerfil;
    if (isset($postdata->selectYear)) {
        $selectYear = $postdata->selectYear;
        $con = $pdo->conectar($selectYear);
    } else {
        $con = $pdo->conectar();
    }

    $sql = "SELECT s.*, p.idPerfil
        FROM secciones s
        INNER JOIN permisos p 
        ON p.idSeccion = s.idSeccion
        WHERE idPerfil = :idPerfil";

    $datosSecciones = $con->prepare($sql);
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

            $sqlModulos = "SELECT * FROM modulos WHERE idSeccion = :idSeccion ORDER BY modulo ASC";
            $datosMdl = $con->prepare($sqlModulos);
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
                    $datosModulos->newSistema = $rsModulos["newSistema"] ?? null;
                    $seccion->lstSubMenus[] = $datosModulos;
                }
            }
            $menu [] = $seccion;
        }
    }
    echo json_encode($menu);
}




