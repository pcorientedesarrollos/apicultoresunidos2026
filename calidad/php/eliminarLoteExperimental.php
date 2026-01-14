<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

//$datos = json_decode($json);
//$info = $datos->valor;

$idLoteExperimental = $_GET["idLoteExperimental"];

if(isset($_GET["organica"])){
    $sqlTblExp = "DELETE FROM experimental_organico WHERE idLoteExperimental = :idLoteExperimental";
    $datEx = $con->prepare($sqlTblExp);
    $datEx->bindParam(':idLoteExperimental', $idLoteExperimental);
    $datEx->execute();
    
    $sql = "DELETE FROM tamboresexperimentales_organico WHERE idLoteExperimental = :idLoteExperimental";
    $datTX = $con->prepare($sql);
    $datTX->bindParam(':idLoteExperimental', $idLoteExperimental);
    $datTX->execute();
}else{
    $sqlTblExp = "DELETE FROM experimental WHERE idLoteExperimental = :idLoteExperimental";
    $datEx = $con->prepare($sqlTblExp);
    $datEx->bindParam(':idLoteExperimental', $idLoteExperimental);
    $datEx->execute();
    
    $sql = "DELETE FROM tamboresexperimentales WHERE idLoteExperimental = :idLoteExperimental";
    $datTX = $con->prepare($sql);
    $datTX->bindParam(':idLoteExperimental', $idLoteExperimental);
    $datTX->execute();
}

//
//foreach ($info as $i) {
//    $sqlAl = "UPDATE almacen set estado = '0' WHERE idAlmacen = :idAlmacen";
//    $datTA = $con->prepare($sqlAl);
//    $datTA->bindParam(':idAlmacen', $i->idAlmacen);
//    $datTA->execute();
//}
