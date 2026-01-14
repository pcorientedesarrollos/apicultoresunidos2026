<?php

include_once '../../DAOConeccion/conePDO.php';
include_once '../../mensajes/Mensajes.php';
include_once '../../controller/DameCaracteristica.php';
$pdo = new conePDO;
$con = $pdo->conectar();
$msg = new Mensajes();


$json = file_get_contents("php://input");
try {
    if (!$json) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($json);
        $info = $datos->valor;
        $tipoDeMiel = $datos->tipoDeMiel;
    }

    switch ($tipoDeMiel) {
        case '1':
            $configuracionlaboratorio_tabla = 'configuracionlaboratorio';
            $laboratorio_tabla = 'laboratorio';
            break;
        case '2':
            $configuracionlaboratorio_tabla = 'configuracionlaboratorio_organico';
            $laboratorio_tabla = 'laboratorio_organico';
            break;
        default:
            throw new Exception('Parámetro de tipo de miel inválido');
            break;
    }
    $caracteristicas = new DameCaracteristica($con, $configuracionlaboratorio_tabla);

    // LLamamos a las caracteristicas


    $listaPorcentaje = $caracteristicas->obtenerValoresPorcentaje();

    $caracteristicaPorcentaje = "";

    foreach ($info as $laboratorio) {

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


        if ($laboratorio->idLaboratorio == 0 && $laboratorio->porcentaje !== null) {
            $sql = $con->prepare("INSERT INTO $laboratorio_tabla (entradaNo, idAlmacen, porcentaje, sf, st, c13, hmf, color, tt, micro,
            idFloracion, resultadoFinal, marcaInterna, estado, sfDescripcion, stDescripcion, porcentajeDescripcion,
            adulteracionDescripcion, procesoDescripcion, colorDescripcion, ttDescripcion)
            VALUES (:idAlmacenEncabezado,:idAlmacen,:porcentaje, :sf, :st, :c13, :hmf, :color, :tt, :micro, :idFloracion,
            :resultadoFinal,:marcaInterna,1, :caracteristicaSf, :caracteristicaSt, :caracteristicaPorcentaje,
            :caracteristicaAdulteracion,:caracteristicaProceso, :caracteristicasColor, :caracteristicasTetra)");
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
        } else {
            $sql = $con->prepare("UPDATE $laboratorio_tabla SET porcentaje =:porcentaje, sf = :sf, st= :st, c13=:c13, hmf=:hmf,
            color=:color, tt=:tt, micro = :micro,idFloracion = :idFloracion, resultadoFinal=:resultadoFinal,
            marcaInterna=:marcaInterna, sfDescripcion=:caracteristicaSf, stDescripcion=:caracteristicaSt,
            porcentajeDescripcion = :caracteristicaPorcentaje, adulteracionDescripcion = :caracteristicaAdulteracion,
            procesoDescripcion =:caracteristicaProceso, colorDescripcion = :caracteristicasColor,
            ttDescripcion = :caracteristicasTetra WHERE idLaboratorio=:idLaboratorio");
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
            $sql->bindParam(':idLaboratorio', $laboratorio->idLaboratorio);
        }
        $sql->execute();
        if ($sql == FALSE) {
            throw new Exception('No se ha registrado alguna configuración');
        }
    }

    echo json_encode(['error' => false, 'message' => 'Se ha guardado la configuración', 'swal' => 'success']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line: ' . $e->getLine(), 'swal' => 'error']);
    exit();
}
