<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();

if(isset($_GET['ver'])){
    $ver = $_GET['ver'];
    $query = $con->prepare('SELECT * FROM clasificacionesmiel WHERE tipo = :tipo');
    $query->bindParam(':tipo', $ver);
    $query->execute();
    echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));
} else {
    echo json_encode([]);
}
