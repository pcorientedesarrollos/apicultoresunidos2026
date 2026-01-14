<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if(isset($_GET["organica"])){
    $idLoteInterno = $_GET["idLoteInterno"];
    $sql = "SELECT idLoteInterno, fechaProceso, fechaEnvasado FROM calidad_organico 
    WHERE idLoteInterno = :idLoteInterno ";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idLoteInterno', $idLoteInterno);
    $datos->execute();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $fechaInterno = new stdClass();
            $fechaInterno->idLoteInterno = $rs["idLoteInterno"];
            $fechaInterno->fechaProceso = $rs["fechaProceso"];
            $fechaInterno->fechaEnvasado = $rs["fechaEnvasado"];
    
            if ($fechaInterno->fechaProceso == "1969-12-31") {
                $fechaInterno->fechaProceso = " ";
            } else {
                $fechaInterno->fechaProceso = $rs["fechaProceso"];
            }
    
            if ($fechaInterno->fechaEnvasado == "1969-12-31") {
                $fechaInterno->fechaEnvasado = " ";
            } else {
                $fechaInterno->fechaEnvasado = $rs["fechaEnvasado"];
            }
        }
        echo json_encode($fechaInterno);
    }
}else{
    $idLoteInterno = $_GET["idLoteInterno"];
    $sql = "SELECT idLoteInterno, fechaProceso, fechaEnvasado FROM calidad 
    WHERE idLoteInterno = :idLoteInterno ";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idLoteInterno', $idLoteInterno);
    $datos->execute();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $fechaInterno = new stdClass();
            $fechaInterno->idLoteInterno = $rs["idLoteInterno"];
            $fechaInterno->fechaProceso = $rs["fechaProceso"];
            $fechaInterno->fechaEnvasado = $rs["fechaEnvasado"];
    
            if ($fechaInterno->fechaProceso == "1969-12-31") {
                $fechaInterno->fechaProceso = " ";
            } else {
                $fechaInterno->fechaProceso = $rs["fechaProceso"];
            }
    
            if ($fechaInterno->fechaEnvasado == "1969-12-31") {
                $fechaInterno->fechaEnvasado = " ";
            } else {
                $fechaInterno->fechaEnvasado = $rs["fechaEnvasado"];
            }
        }
        echo json_encode($fechaInterno);
    }
}
?>