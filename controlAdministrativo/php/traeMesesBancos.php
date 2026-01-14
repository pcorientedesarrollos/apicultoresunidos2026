<?php
if(isset($_GET['meses'])) {
    include_once '../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();
    $meses = $con->prepare("SELECT m.mes, m.idMes FROM auxiliardebancos ab LEFT JOIN meses m ON SUBSTR(ab.fecha FROM 6 FOR 2) = m.idMes GROUP BY m.idMes ORDER BY m.idMes");
    $meses->execute();
    $arrayMeses = array();
   foreach($meses->fetchAll(PDO::FETCH_ASSOC) as $dato){
                $sql = $con->prepare("SELECT COALESCE(SUM(cantidad),0) AS ingresosMes, 
                (SELECT COALESCE(SUM(cantidad),0) FROM auxiliardebancos WHERE ingresoEgreso = 1 AND 
                SUBSTR(fecha FROM 6 FOR 2) = CONVERT(:idMes, UNSIGNED) ) AS egresosMes
                FROM auxiliardebancos WHERE ingresoEgreso = 0 AND 
                SUBSTR(fecha FROM 6 FOR 2) = CONVERT(:idMes, UNSIGNED)");
                     $sql->bindParam(':idMes', $dato['idMes']);
                     $sql->execute();
                     $totalesMes = $sql->fetch(PDO::FETCH_ASSOC);
                     $ingresosMes = $totalesMes['ingresosMes'];
                     $egresosMes = $totalesMes['egresosMes'];
                     $dato['ingresosMes'] = $ingresosMes;
                     $dato['egresosMes'] = $egresosMes;
                     array_push($arrayMeses, $dato);
        }
      echo json_encode($arrayMeses);
} else {
    exit();
}