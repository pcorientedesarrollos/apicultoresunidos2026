<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$resultado = array();
try {
    if(isset($_GET['proveedor'])){
        $query = $con->prepare("SELECT * FROM tiposdemiel ORDER BY idTipoDeMiel ASC LIMIT 9");
    }else if(isset($_GET['sobrante'])){
        $query = $con->prepare("SELECT * FROM tiposdemiel ORDER BY idTipoDeMiel ASC LIMIT 1");
    }else{
        $query = $con->prepare("SELECT * FROM tiposdemiel ORDER BY idTipoDeMiel ASC LIMIT 9");
    }
    $query->execute();

    if ($query == FALSE) {throw new Exception($con->errorInfo());}
    $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($resultado);

} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}