<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();


$idMes = $_GET["idMes"];

if (isset($_GET["idMes"])) {
    $idMes = addslashes($_GET["idMes"]);

    $sql = "SELECT idMes as existe FROM presupuestos_cuentas WHERE idMes = :idMes GROUP BY idMes";
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
        $sql1 = "SELECT c.idCuentaConcepto, s.idSubcuenta FROM cuentas c LEFT JOIN subcuentas s ON c.idCuentaConcepto = s.idCuentaConcepto";
        $dao = $con->prepare($sql1);
        $dao->execute();
        $array = array();
        while ($row = $dao->fetch()) {
            $perso = new stdClass();
            $perso->idCuenta = $row["idCuentaConcepto"];
            $perso->idSubcuenta = $row["idSubcuenta"];
            $array[] = $perso;
        }

        foreach ($array as $infoDisposiciones) {
            $sql2 = "INSERT INTO presupuestos_cuentas(idCuenta, idSubcuenta, idMes, cantidad)"
                    . "VALUES(:idCuenta, :idSubcuenta, :idMes, 0)";
            $dato = $con->prepare($sql2);
            $dato->bindParam(':idCuenta', $infoDisposiciones->idCuenta);
            $dato->bindParam(':idSubcuenta', $infoDisposiciones->idSubcuenta);
            $dato->bindParam(':idMes', $idMes);
            $dato->execute();
        }
    }
}
?>