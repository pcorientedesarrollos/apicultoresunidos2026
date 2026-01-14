<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$json = file_get_contents("php://input");
$datos = json_decode($json);

$info = $datos->valor;
    
    foreach ($info as $i) {    
            $sql = "UPDATE presupuestos_cuentas set cantidad = :cantidad WHERE idPresupuesto = :idPresupuesto";
            $datosUp1 = $con->prepare($sql);
            $datosUp1->bindParam(':cantidad', $i->cantidad);
            $datosUp1->bindParam(':idPresupuesto', $i->idPresupuesto);
            $datosUp1->execute();        
    }

?>