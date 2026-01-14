<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idLoteInterno = $_GET['idLoteInterno'];

if(isset($_GET["organica"])){
    $query = "SELECT * FROM especificaciones_organico
    WHERE idLoteInterno = :idLoteInterno";
    $datsEspeci = $conexion->prepare($query);
    $datsEspeci->bindParam(':idLoteInterno', $idLoteInterno);
    $datsEspeci->execute();
    
    while ($especificacionesn = $datsEspeci->fetch()) {
        if ($especificacionesn["tipo"] == 1) {
            $clientesEspe = new stdClass();
            $clientesEspe->idEspecificacion = $especificacionesn["idEspecificacion"];
            $clientesEspe->humedad = $especificacionesn["humedad"];
            $clientesEspe->color = $especificacionesn["color"];
            $clientesEspe->adulteracion = $especificacionesn["adulteracion"];
            $clientesEspe->sf = $especificacionesn["sf"];
            $clientesEspe->st = $especificacionesn["st"];
            $clientesEspe->tt = $especificacionesn["tt"];
            $clientesEspe->hmf = $especificacionesn["hmf"];
            $clientesEspe->idLoteInterno = $especificacionesn["idLoteInterno"];
            $clientesEspe->tipo = $especificacionesn["tipo"];
            $especificacion[] = $clientesEspe;
        } else {
            $laboratorioEspe = new stdClass();
            $laboratorioEspe->idEspecificacion = $especificacionesn["idEspecificacion"];
            $laboratorioEspe->humedad = $especificacionesn["humedad"];
            $laboratorioEspe->color = $especificacionesn["color"];
            $laboratorioEspe->adulteracion = $especificacionesn["adulteracion"];
            $laboratorioEspe->sf = $especificacionesn["sf"];
            $laboratorioEspe->st = $especificacionesn["st"];
            $laboratorioEspe->tt = $especificacionesn["tt"];
            $laboratorioEspe->hmf = $especificacionesn["hmf"];
            $laboratorioEspe->idLoteInterno = $especificacionesn["idLoteInterno"];
            $laboratorioEspe->tipo = $especificacionesn["tipo"];
            $especificacion[] = $laboratorioEspe;
        }
    }
    
    echo json_encode($especificacion);
    
}else{
    $query = "SELECT * FROM especificaciones
    WHERE idLoteInterno = :idLoteInterno";
    $datsEspeci = $conexion->prepare($query);
    $datsEspeci->bindParam(':idLoteInterno', $idLoteInterno);
    $datsEspeci->execute();
    
    while ($especificacionesn = $datsEspeci->fetch()) {
        if ($especificacionesn["tipo"] == 1) {
            $clientesEspe = new stdClass();
            $clientesEspe->idEspecificacion = $especificacionesn["idEspecificacion"];
            $clientesEspe->humedad = $especificacionesn["humedad"];
            $clientesEspe->color = $especificacionesn["color"];
            $clientesEspe->adulteracion = $especificacionesn["adulteracion"];
            $clientesEspe->sf = $especificacionesn["sf"];
            $clientesEspe->st = $especificacionesn["st"];
            $clientesEspe->tt = $especificacionesn["tt"];
            $clientesEspe->hmf = $especificacionesn["hmf"];
            $clientesEspe->idLoteInterno = $especificacionesn["idLoteInterno"];
            $clientesEspe->tipo = $especificacionesn["tipo"];
            $especificacion[] = $clientesEspe;
        } else {
            $laboratorioEspe = new stdClass();
            $laboratorioEspe->idEspecificacion = $especificacionesn["idEspecificacion"];
            $laboratorioEspe->humedad = $especificacionesn["humedad"];
            $laboratorioEspe->color = $especificacionesn["color"];
            $laboratorioEspe->adulteracion = $especificacionesn["adulteracion"];
            $laboratorioEspe->sf = $especificacionesn["sf"];
            $laboratorioEspe->st = $especificacionesn["st"];
            $laboratorioEspe->tt = $especificacionesn["tt"];
            $laboratorioEspe->hmf = $especificacionesn["hmf"];
            $laboratorioEspe->idLoteInterno = $especificacionesn["idLoteInterno"];
            $laboratorioEspe->tipo = $especificacionesn["tipo"];
            $especificacion[] = $laboratorioEspe;
        }
    }  
    echo json_encode($especificacion);    
}