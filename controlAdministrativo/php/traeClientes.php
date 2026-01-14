<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$dbh = $pdo->conectar();

if(isset($_GET['clientes'])){
    $consulta = $dbh->prepare("SELECT nombre FROM proveedor WHERE 1");
    $consulta->execute();
    if($consulta->rowCount() >= 1){
        echo json_encode($consulta->fetchAll(PDO::FETCH_ASSOC));
    } else {
        echo json_encode([]);
    }
} else {
    exit();
}


?>