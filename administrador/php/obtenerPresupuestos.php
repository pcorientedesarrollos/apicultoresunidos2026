<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = array();
try {

    $sql = "SELECT p.idPresupuesto, p.idMes, m.mes
        FROM presupuestos_cuentas p 
        INNER JOIN meses m ON m.idMes = p.idMes 
        GROUP BY mes
        ORDER BY idMes ASC";
    $datos = $con->prepare($sql);
    $datos->execute();

    if($datos == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado = $datos->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['error'=>false, 'message'=>'Consulta realizada', 'data'=>$resultado]);

} catch (Exception $e){
    echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'data'=>$resultado]);
    exit();
}
