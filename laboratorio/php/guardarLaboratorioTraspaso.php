<?php

include_once '../../DAOConeccion/conePDO.php';
include_once '../../mensajes/Mensajes.php';
include_once '../../controller/DameCaracteristica.php';
$pdo = new conePDO; $con = $pdo->conectar();
$msg = new Mensajes();


$json = file_get_contents("php://input");
try {
    if(!$json) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($json);
        $info = $datos->valor;
        $tipoDeMiel = $datos->tipoDeMiel;
    }

    switch($tipoDeMiel){
        case '1':
        $configuracionlaboratorio_tabla = 'configuracionlaboratorio';
        // $laboratorio_tabla = 'laboratorio';
        break;
        case '2':
        $configuracionlaboratorio_tabla = 'configuracionlaboratorio_organico';
        // $laboratorio_tabla = 'laboratorio_organico';
        break;
        default:
            throw new Exception('Parámetro de tipo de miel inválido');
        break;
    }
    $caracteristicas = new DameCaracteristica($con, $configuracionlaboratorio_tabla);
    
    // LLamamos a las caracteristicas

    $listaSf = $caracteristicas->obtenerValorSf();
    $listaSt = $caracteristicas->obtenerValorSt();
    $listaPorcentaje = $caracteristicas->obtenerValoresPorcentaje();
    $litaAdulteracion = $caracteristicas->obtenerValoresAdulteracion();
    $listaProceso = $caracteristicas->obtenerValoresHmf();
    $listaColor = $caracteristicas->obtenerValorColor();
    $listaTetra = $caracteristicas->obtenerValorTt();

    $caracteristicaAdulteracion = "";
    $caracteristicaPorcentaje = "";
    $caracteristicaProceso = "";
    $caracteristicasColor = "";
    $caracteristicasTetra = "";
    $caracteristicaSf = "";
    $caracteristicaSt = "";

    foreach ($info as $laboratorio) {
        foreach ($listaSf as $value) {
            switch ($value['signo']) {
                case 1:
                    if ($laboratorio->sf < $value['rango1']) {
                        $caracteristicaSf = $value['descripcion'];
                    }
                    break;
                case 2:
                    if ($laboratorio->sf > $value['rango1']) {
                        $caracteristicaSf = $value['descripcion'];
                    }
                    break;
                case 3:
                    if ($laboratorio->sf <= $value['rango1']) {
                        $caracteristicaSf = $value['descripcion'];
                    }
                    break;
                case 4:
                    if ($laboratorio->sf >= $value['rango1']) {
                        $caracteristicaSf = $value['descripcion'];
                    }
                    break;
                case 5:
                    if ($laboratorio->sf >= $value['rango1'] && $laboratorio->sf <= $value['rango2']) {
                        $caracteristicaSf = $value['descripcion'];
                    }
                    break;
            }
        }
    
        foreach ($listaSt as $value) {
            switch ($value['signo']) {
                case 1:
                    if ($laboratorio->st < $value['rango1']) {
                        $caracteristicaSt = $value['descripcion'];
                    }
                    break;
                case 2:
                    if ($laboratorio->st > $value['rango1']) {
                        $caracteristicaSt = $value['descripcion'];
                    }
                    break;
                case 3:
                    if ($laboratorio->st <= $value['rango1']) {
                        $caracteristicaSt = $value['descripcion'];
                    }
                    break;
                case 4:
                    if ($laboratorio->st >= $value['rango1']) {
                        $caracteristicaSt = $value['descripcion'];
                    }
                    break;
                case 5:
                    if ($laboratorio->st >= $value['rango1'] && $laboratorio->st <= $value['rango2']) {
                        $caracteristicaSt = $value['descripcion'];
                    }
                    break;
            }
        }
    
        foreach ($listaPorcentaje as $value) {
            switch ($value['signo']) {
                case 1:
                    if ($laboratorio->porcentaje < $value['rango1']) {
                        $caracteristicaPorcentaje = $value['descripcion'];
                    }
                    break;
                case 2:
                    if ($laboratorio->porcentaje > $value['rango1']) {
                        $caracteristicaPorcentaje = $value['descripcion'];
                    }
                    break;
                case 3:
                    if ($laboratorio->porcentaje <= $value['rango1']) {
                        $caracteristicaPorcentaje = $value['descripcion'];
                    }
                    break;
                case 4:
                    if ($laboratorio->porcentaje >= $value['rango1']) {
                        $caracteristicaPorcentaje = $value['descripcion'];
                    }
                    break;
                case 5:
                    if ($laboratorio->porcentaje >= $value['rango1'] && $laboratorio->porcentaje <= $value['rango2']) {
                        $caracteristicaPorcentaje = $value['descripcion'];
                    }
                    break;
            }
        }
        foreach ($litaAdulteracion as $value) {
            switch ($value['signo']) {
                case 1:
                    if ($laboratorio->c13 < $value['rango1']) {
                        $caracteristicaAdulteracion = $value['descripcion'];
                    }
                    break;
                case 2:
                    if ($laboratorio->c13 > $value['rango1']) {
                        $caracteristicaAdulteracion = $value['descripcion'];
                    }
                    break;
                case 3:
                    if ($laboratorio->c13 <= $value['rango1']) {
                        $caracteristicaAdulteracion = $value['descripcion'];
                    }
                    break;
                case 4:
                    if ($laboratorio->c13 >= $value['rango1']) {
                        $caracteristicaAdulteracion = $value['descripcion'];
                    }
                    break;
                case 5:
                    if ($laboratorio->c13 >= $value['rango1'] && $laboratorio->c13 <= $value['rango2']) {
                        $caracteristicaAdulteracion = $value['descripcion'];
                    }
                    break;
            }
        }
    
        foreach ($listaProceso as $value) {
            switch ($value['signo']) {
                case 1:
                    if ($laboratorio->hmf < $value['rango1']) {
                        $caracteristicaProceso = $value['descripcion'];
                    }
                    break;
                case 2:
                    if ($laboratorio->hmf > $value['rango1']) {
                        $caracteristicaProceso = $value['descripcion'];
                    }
                    break;
                case 3:
                    if ($laboratorio->hmf <= $value['rango1']) {
                        $caracteristicaProceso = $value['descripcion'];
                    }
                    break;
                case 4:
                    if ($laboratorio->hmf >= $value['rango1']) {
                        $caracteristicaProceso = $value['descripcion'];
                    }
                    break;
                case 5:
                    if ($laboratorio->hmf >= $value['rango1'] && $laboratorio->hmf <= $value['rango2']) {
                        $caracteristicaProceso = $value['descripcion'];
                    }
                    break;
            }
        }
    
        foreach ($listaColor as $value) {
            switch ($value['signo']) {
                case 1:
                    if ($laboratorio->color < $value['rango1']) {
                        $caracteristicasColor = $value['descripcion'];
                    }
                    break;
                case 2:
                    if ($laboratorio->color > $value['rango1']) {
                        $caracteristicasColor = $value['descripcion'];
                    }
                    break;
                case 3:
                    if ($laboratorio->color <= $value['rango1']) {
                        $caracteristicasColor = $value['descripcion'];
                    }
                    break;
                case 4:
                    if ($laboratorio->color >= $value['rango1']) {
                        $caracteristicasColor = $value['descripcion'];
                    }
                    break;
                case 5:
                    if ($laboratorio->color >= $value['rango1'] && $laboratorio->color <= $value['rango2']) {
                        $caracteristicasColor = $value['descripcion'];
                    }
                    break;
            }
        }
    
        foreach ($listaTetra as $value) {
            switch ($value['signo']) {
                case 1:
                    if ($laboratorio->tt < $value['rango1']) {
                        $caracteristicasTetra = $value['descripcion'];
                    }
                    break;
                case 2:
                    if ($laboratorio->tt > $value['rango1']) {
                        $caracteristicasTetra = $value['descripcion'];
                    }
                    break;
                case 3:
                    if ($laboratorio->tt <= $value['rango1']) {
                        $caracteristicasTetra = $value['descripcion'];
                    }
                    break;
                case 4:
                    if ($laboratorio->tt >= $value['rango1']) {
                        $caracteristicasTetra = $value['descripcion'];
                    }
                    break;
                case 5:
                    if ($laboratorio->tt >= $value['rango1'] && $laboratorio->tt <= $value['rango2']) {
                        $caracteristicasTetra = $value['descripcion'];
                    }
                    break;
            }
        }
    
    
        if ($laboratorio->idLaboratorio == 0 && $laboratorio->porcentaje !== null) {
            $sql = $con->prepare("INSERT INTO laboratorio_traspaso (entradaNo, idAlmacen, porcentaje, sf, st, c13, hmf, color, tt, micro,
            idFloracion, resultadoFinal, marcaInterna, estado, sfDescripcion, stDescripcion, porcentajeDescripcion,
            adulteracionDescripcion, procesoDescripcion, colorDescripcion, ttDescripcion, tipoDeMiel)
            VALUES (:idAlmacenEncabezado,:idAlmacen,:porcentaje, :sf, :st, :c13, :hmf, :color, :tt, :micro, :idFloracion,
            :resultadoFinal,:marcaInterna,1, :caracteristicaSf, :caracteristicaSt, :caracteristicaPorcentaje,
            :caracteristicaAdulteracion,:caracteristicaProceso, :caracteristicasColor, :caracteristicasTetra, :tipoDeMiel)");
            $sql->bindParam(':idAlmacenEncabezado',  $laboratorio->idAlmacenEncabezado);
            $sql->bindParam(':idAlmacen',  $laboratorio->idAlmacen);
            $sql->bindParam(':porcentaje',  $laboratorio->porcentaje);
            $sql->bindParam(':sf',  $laboratorio->sf);
            $sql->bindParam(':st',  $laboratorio->st);
            $sql->bindParam(':c13',  $laboratorio->c13);
            $sql->bindParam(':hmf',  $laboratorio->hmf);
            $sql->bindParam(':color',  $laboratorio->color);
            $sql->bindParam(':tt',  $laboratorio->tt);
            $sql->bindParam(':micro',  $laboratorio->micro);
            $sql->bindParam(':idFloracion',  $laboratorio->idFloracion);
            $sql->bindParam(':resultadoFinal',  $laboratorio->resultadoFinal);
            $sql->bindParam(':marcaInterna',  $laboratorio->marcaInterna);
            $sql->bindParam(':caracteristicaSf',  $caracteristicaSf);
            $sql->bindParam(':caracteristicaSt',  $caracteristicaSt);
            $sql->bindParam(':caracteristicaPorcentaje',  $caracteristicaPorcentaje);
            $sql->bindParam(':caracteristicaAdulteracion',  $caracteristicaAdulteracion);
            $sql->bindParam(':caracteristicaProceso',  $caracteristicaProceso);
            $sql->bindParam(':caracteristicasColor',  $caracteristicasColor);
            $sql->bindParam(':caracteristicasTetra',  $caracteristicasTetra);
            $sql->bindParam(':tipoDeMiel',  $tipoDeMiel);
        } else {
            $sql = $con->prepare("UPDATE laboratorio_traspaso SET porcentaje =:porcentaje, sf = :sf, st= :st, c13=:c13, hmf=:hmf,
            color=:color, tt=:tt, micro = :micro,idFloracion = :idFloracion, resultadoFinal=:resultadoFinal,
            marcaInterna=:marcaInterna, sfDescripcion=:caracteristicaSf, stDescripcion=:caracteristicaSt,
            porcentajeDescripcion = :caracteristicaPorcentaje, adulteracionDescripcion = :caracteristicaAdulteracion,
            procesoDescripcion =:caracteristicaProceso, colorDescripcion = :caracteristicasColor,
            ttDescripcion = :caracteristicasTetra, tipoDeMiel = :tipoDeMiel WHERE idLaboratorio=:idLaboratorio");
            $sql->bindParam(':porcentaje', $laboratorio->porcentaje);
            $sql->bindParam(':sf', $laboratorio->sf);
            $sql->bindParam(':st', $laboratorio->st);
            $sql->bindParam(':c13', $laboratorio->c13);
            $sql->bindParam(':hmf', $laboratorio->hmf);
            $sql->bindParam(':color', $laboratorio->color);
            $sql->bindParam(':tt', $laboratorio->tt);
            $sql->bindParam(':micro', $laboratorio->micro);
            $sql->bindParam(':idFloracion', $laboratorio->idFloracion);
            $sql->bindParam(':resultadoFinal', $laboratorio->resultadoFinal);
            $sql->bindParam(':marcaInterna', $laboratorio->marcaInterna);
            $sql->bindParam(':caracteristicaSf', $caracteristicaSf);
            $sql->bindParam(':caracteristicaSt', $caracteristicaSt);
            $sql->bindParam(':caracteristicaPorcentaje', $caracteristicaPorcentaje);
            $sql->bindParam(':caracteristicaAdulteracion', $caracteristicaAdulteracion);
            $sql->bindParam(':caracteristicaProceso', $caracteristicaProceso);
            $sql->bindParam(':caracteristicasColor', $caracteristicasColor);
            $sql->bindParam(':caracteristicasTetra', $caracteristicasTetra);
            $sql->bindParam(':tipoDeMiel',  $tipoDeMiel);
            $sql->bindParam(':idLaboratorio', $laboratorio->idLaboratorio);
        }
        $sql->execute();
        if($sql == FALSE) {
            throw new Exception('No se ha registrado alguna configuración');
        }
    }

    echo json_encode(['error'=>false, 'message'=>'Datos guardados', 'swal'=>'success']);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage() . '. Line: ' . $e->getLine(), 'swal'=>'error']);
    exit();
}