<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

if(isset($_GET["organica"])){
    $query="SELECT idEspecificacion,idLoteInterno FROM especificaciones_organico
    GROUP BY idLoteInterno";
    $datosResum = $conexion->prepare($query);
    $datosResum->execute();
    
    while ($especificacion = $datosResum->fetch()){
        $result = new stdClass();
        $result->idEspecificacion = $especificacion["idEspecificacion"];
        $result->idLoteInterno = $especificacion["idLoteInterno"];
        $arrayEspecificacion[] = $result;
    }
    echo json_encode($arrayEspecificacion);
}else{
    $query="SELECT  idEspecificacion,idLoteInterno FROM especificaciones
    GROUP BY idLoteInterno";
    $datosResum = $conexion->prepare($query);
    $datosResum->execute();
    
    while ($especificacion = $datosResum->fetch()){
        $result = new stdClass();
        $result->idEspecificacion = $especificacion["idEspecificacion"];
        $result->idLoteInterno = $especificacion["idLoteInterno"];
        $arrayEspecificacion[] = $result;
    }
    echo json_encode($arrayEspecificacion);
}