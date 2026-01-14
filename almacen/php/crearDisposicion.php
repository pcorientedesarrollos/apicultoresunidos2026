<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();


$idMes = $_GET["idMes"];

if (isset($_GET["idMes"])) {
    $idMes = addslashes($_GET["idMes"]);

    $sql = "SELECT idMes as existe FROM disposiciondelpersonal WHERE idMes = :idMes GROUP BY idMes";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idMes', $idMes);
    $datos->execute();
    while ($respuAbono = $datos->fetch()) {
        $condi = new stdClass();
        $condi->existe = $respuAbono["existe"];
    }

    if ($condi->existe == $idMes) {
        echo json_encode($arrayCondicion);
    } else {
        $sql1 = "SELECT idPersonalOM, nombre FROM personaloaxaca WHERE estado = 0 AND idArea = 4 AND idPersonalOM !=26 ORDER BY nombre ASC";
        $dao = $con->prepare($sql1);
        $dao->execute();
        $array = array();
        while ($row = $dao->fetch()) {
            $perso = new stdClass();
            $perso->idPersonalOM = $row["idPersonalOM"];
            $perso->nombre = $row["nombre"];
            $array[] = $perso;
        }

        foreach ($array as $infoDisposiciones) {
            $sql2 = "INSERT INTO disposiciondelpersonal(idMes, idPersonalOM, semana1, semana2, semana3, semana4, semana5, semana1A, semana2A, semana3A, semana4A, semana5A, semana1B, semana2B, semana3B, semana4B, semana5B, semana1C, semana2C, semana3C, semana4C, semana5C, semana1D, semana2D, semana3D, semana4D, semana5D, semana1E, semana2E, semana3E, semana4E, semana5E, semana1F, semana2F, semana3F, semana4F, semana5F, observaciones)"
                    . "VALUES(:idMes, :idPersonalOM, '0', '0', '0', '0', '0' , '0', '0', '0', '0', '0' , '0', '0', '0', '0', '0' , '0', '0', '0', '0', '0' , '0', '0', '0', '0', '0' , '0', '0', '0', '0', '0' , '0', '0', '0', '0', '0', ' ')";
            $dato = $con->prepare($sql2);
            $dato->bindParam(':idMes', $idMes);
            $dato->bindParam(':idPersonalOM', $infoDisposiciones->idPersonalOM);
            $dato->execute();
        }
    }
}
?>