<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = "SELECT ca.idDisposicion, ca.idMes, m.mes
        FROM disposiciondelpersonal ca INNER JOIN meses m ON m.idMes = ca.idMes 
        GROUP BY mes
        ORDER BY idMes ASC";
$datos = $con->prepare($sql);
$datos->execute();

if ($datos == false) {
    echo mysql_error();
} else {
    $arrayCondi = array();
//    $cont = 0;
    while ($rs = $datos->fetch()) {
        $infoCondi = new stdClass();
        $infoCondi->idDisposicion = $rs["idDisposicion"];
        $infoCondi->mes = $rs["mes"];
        $infoCondi->idMes = $rs["idMes"];
        $arrayCondi[] = $infoCondi;
    }
    echo json_encode($arrayCondi);
}
?>