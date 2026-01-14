<?php

$json = file_get_contents("php://input");
$infos = json_decode($json);

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

if(isset($_GET["organica"])){
    foreach ($infos as $arregloEspecificaciones) {
        $sqlEspe = "INSERT INTO especificaciones_organico(humedad,color,adulteracion,sf,st,tt,hmf,idLoteInterno,tipo) VALUES (:humedad,:color,:adulteracion,:sf,:st,:tt,:hmf,:idLoteInterno,:tipo)";
        $datsEsp = $conexion->prepare($sqlEspe);
        $datsEsp->bindParam(':humedad', $arregloEspecificaciones->humedad);
        $datsEsp->bindParam(':color', $arregloEspecificaciones->color);
        $datsEsp->bindParam(':adulteracion', $arregloEspecificaciones->adulteracion);
        $datsEsp->bindParam(':sf', $arregloEspecificaciones->sf);
        $datsEsp->bindParam(':st', $arregloEspecificaciones->st);
        $datsEsp->bindParam(':tt', $arregloEspecificaciones->tt);
        $datsEsp->bindParam(':hmf', $arregloEspecificaciones->hmf);
        $datsEsp->bindParam(':idLoteInterno', $arregloEspecificaciones->idLoteInterno);
        $datsEsp->bindParam(':tipo', $arregloEspecificaciones->tipo);
        $datsEsp->execute();
    }
}else{
    foreach ($infos as $arregloEspecificaciones) {
        $sqlEspe = "INSERT INTO especificaciones(humedad,color,adulteracion,sf,st,tt,hmf,idLoteInterno,tipo) VALUES (:humedad,:color,:adulteracion,:sf,:st,:tt,:hmf,:idLoteInterno,:tipo)";
        $datsEsp = $conexion->prepare($sqlEspe);
        $datsEsp->bindParam(':humedad', $arregloEspecificaciones->humedad);
        $datsEsp->bindParam(':color', $arregloEspecificaciones->color);
        $datsEsp->bindParam(':adulteracion', $arregloEspecificaciones->adulteracion);
        $datsEsp->bindParam(':sf', $arregloEspecificaciones->sf);
        $datsEsp->bindParam(':st', $arregloEspecificaciones->st);
        $datsEsp->bindParam(':tt', $arregloEspecificaciones->tt);
        $datsEsp->bindParam(':hmf', $arregloEspecificaciones->hmf);
        $datsEsp->bindParam(':idLoteInterno', $arregloEspecificaciones->idLoteInterno);
        $datsEsp->bindParam(':tipo', $arregloEspecificaciones->tipo);
        $datsEsp->execute();
    }
}