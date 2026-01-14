<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$json = file_get_contents("php://input");
$datosEspe = json_decode($json);
$inform = $datosEspe->valor;

if(isset($_GET["organica"])){
        foreach ( $inform as $edicion) {
        $sqlEditar = "UPDATE especificaciones_organico SET humedad = :humedad, color = :color, adulteracion = :adulteracion, sf = :sf, st = :st, tt = :tt, hmf = :hmf WHERE tipo=:tipo AND idLoteInterno=:idLoteInterno";
        $edicionEsp = $conexion->prepare($sqlEditar);
        $edicionEsp->bindParam(':humedad', $edicion->humedad);
        $edicionEsp->bindParam(':color', $edicion->color);
        $edicionEsp->bindParam(':adulteracion', $edicion->adulteracion);
        $edicionEsp->bindParam(':sf', $edicion->sf);
        $edicionEsp->bindParam(':st', $edicion->st);
        $edicionEsp->bindParam(':tt', $edicion->tt);
        $edicionEsp->bindParam(':hmf', $edicion->hmf);
        $edicionEsp->bindParam(':idLoteInterno', $edicion->idLoteInterno);
        $edicionEsp->bindParam(':tipo', $edicion->tipo);
        $edicionEsp->execute();
           }
}else{
foreach ( $inform as $edicion) {
        $sqlEditar = "UPDATE especificaciones SET humedad = :humedad, color = :color, adulteracion = :adulteracion, sf = :sf, st = :st, tt = :tt, hmf = :hmf WHERE tipo=:tipo AND idLoteInterno=:idLoteInterno";
        $edicionEsp = $conexion->prepare($sqlEditar);
        $edicionEsp->bindParam(':humedad', $edicion->humedad);
        $edicionEsp->bindParam(':color', $edicion->color);
        $edicionEsp->bindParam(':adulteracion', $edicion->adulteracion);
        $edicionEsp->bindParam(':sf', $edicion->sf);
        $edicionEsp->bindParam(':st', $edicion->st);
        $edicionEsp->bindParam(':tt', $edicion->tt);
        $edicionEsp->bindParam(':hmf', $edicion->hmf);
        $edicionEsp->bindParam(':idLoteInterno', $edicion->idLoteInterno);
         $edicionEsp->bindParam(':tipo', $edicion->tipo);
        $edicionEsp->execute();
   }
}