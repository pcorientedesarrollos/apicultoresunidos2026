<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$json = file_get_contents("php://input");
$datos = json_decode($json);

$info = $datos->valor;
    
    foreach ($info as $i) {    
            $sql = "UPDATE proveedor set cantidad = :cantidad, idEstado = :idEstado WHERE proveedor.idProveedor = :idProveedor";
            $datosUp1 = $con->prepare($sql);
            $datosUp1->bindParam(':cantidad', $i->cantidad);
            $datosUp1->bindParam(':idEstado', $i->idEstado);
            $datosUp1->bindParam(':idProveedor', $i->idProveedor);
            $datosUp1->execute();        
    }

?>