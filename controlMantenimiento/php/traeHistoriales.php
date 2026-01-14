<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idEquipo = $_GET["idEquipo"];
$sqlP = "SELECT *
        FROM historiales
        WHERE idEquipo = :idEquipo
        ORDER BY fecha DESC";
$dato = $con->prepare($sqlP);
$dato->bindParam(':idEquipo', $idEquipo);
$dato->execute();
$historiales = array();
if ($dato == false) {
    echo mysql_error();
} else {
    while ($rs = $dato->fetch()) {
        $histo = new stdClass();
        $histo->idHistorial = $rs["idHistorial"];
        $histo->fecha = $rs["fecha"];
        $histo->areaAntesCambio = $rs["areaAntesCambio"];
        $histo->areaDespuesCambio = $rs["areaDespuesCambio"];
        $histo->autoriza = $rs["autoriza"];
        $histo->entrega = $rs["entrega"];
        $histo->recibe = $rs["recibe"];
        $historiales[] = $histo;

        $areaAntes = "SELECT area FROM areas WHERE idArea = :idArea";
        $area = $con->prepare($areaAntes);
        $area->bindParam(':idArea', $histo->areaAntesCambio);
        $area->execute();
        while ($rs = $area->fetch()) {
            $histo->antes = $rs["area"];
        }

        $areaDespues = "SELECT area FROM areas WHERE idArea = :idArea";
        $despues = $con->prepare($areaDespues);
        $despues->bindParam(':idArea', $histo->areaDespuesCambio);
        $despues->execute();
        while ($rs = $despues->fetch()) {
            $histo->despues = $rs["area"];
        }

        $autoriza = "SELECT nombre FROM personaloaxaca WHERE idPersonalOM = :idPersonalOM";
        $autorizo = $con->prepare($autoriza);
        $autorizo->bindParam(':idPersonalOM', $histo->autoriza);
        $autorizo->execute();
        while ($rs = $autorizo->fetch()) {
            $histo->autorizo = $rs["nombre"];
        }

        $entrega = "SELECT nombre FROM personaloaxaca WHERE idPersonalOM = :idPersonalOM";
        $entrego = $con->prepare($entrega);
        $entrego->bindParam(':idPersonalOM', $histo->entrega);
        $entrego->execute();
        while ($rs = $entrego->fetch()) {
            $histo->entrego = $rs["nombre"];
        }

        $recibe = "SELECT nombre FROM personaloaxaca WHERE idPersonalOM = :idPersonalOM";
        $recibio = $con->prepare($recibe);
        $recibio->bindParam(':idPersonalOM', $histo->recibe);
        $recibio->execute();
        while ($rs = $recibio->fetch()) {
            $histo->recibio = $rs["nombre"];
        }
    }
}
echo json_encode($historiales);
?>