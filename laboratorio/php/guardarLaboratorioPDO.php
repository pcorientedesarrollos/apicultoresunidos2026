<?php

include_once '../../DAOConeccion/conePDO.php';
include_once '../../controller/DameCaracteristica.php';
$caracteristicas = new DameCaracteristica();
$pdo = new conePDO;
$con = $pdo->conectar();

$json = file_get_contents("php://input");
$datos = json_decode($json);
$info = $datos->valor;

$listaSf = $caracteristicas->obtenerValorSf();
$listaSt = $caracteristicas->obtenerValorSt();
$listaPorcentaje = $caracteristicas->obtenerValoresPorcentaje();
$litaAdulteracion = $caracteristicas->obtenerValoresAdulteracion();
$listaProceso = $caracteristicas->obtenerValoresHmf();
$listaColor = $caracteristicas->obtenerValorColor();
$listaTetra = $caracteristicas->obtenerValorTt();
$caracteristicaSf = "";
$caracteristicaSt = "";
$caracteristicaPorcentaje = "";
$caracteristicaAdulteracion = "";
$caracteristicaProceso = "";
$caracteristicasColor = "";
$caracteristicasTetra = "";
foreach ($info as $laboratorio) {
    foreach ($listaSf as $value) {
        switch ($value->signo) {
            case 1:
                if ($laboratorio->sf < $value->rango1) {
                    $caracteristicaSf = $value->descripcion;
                }
                break;
            case 2:
                if ($laboratorio->sf > $value->rango1) {
                    $caracteristicaSf = $value->descripcion;
                }
                break;
            case 3:
                if ($laboratorio->sf <= $value->rango1) {
                    $caracteristicaSf = $value->descripcion;
                }
                break;
            case 4:
                if ($laboratorio->sf >= $value->rango1) {
                    $caracteristicaSf = $value->descripcion;
                }
                break;
            case 5:
                if ($laboratorio->sf >= $value->rango1 && $laboratorio->sf <= $value->rango2) {
                    $caracteristicaSf = $value->descripcion;
                }
                break;
        }
    }

    foreach ($listaSt as $value) {
        switch ($value->signo) {
            case 1:
                if ($laboratorio->st < $value->rango1) {
                    $caracteristicaSt = $value->descripcion;
                }
                break;
            case 2:
                if ($laboratorio->st > $value->rango1) {
                    $caracteristicaSt = $value->descripcion;
                }
                break;
            case 3:
                if ($laboratorio->st <= $value->rango1) {
                    $caracteristicaSt = $value->descripcion;
                }
                break;
            case 4:
                if ($laboratorio->st >= $value->rango1) {
                    $caracteristicaSt = $value->descripcion;
                }
                break;
            case 5:
                if ($laboratorio->st >= $value->rango1 && $laboratorio->st <= $value->rango2) {
                    $caracteristicaSt = $value->descripcion;
                }
                break;
        }
    }

    foreach ($listaPorcentaje as $value) {
        switch ($value->signo) {
            case 1:
                if ($laboratorio->porcentaje < $value->rango1) {
                    $caracteristicaPorcentaje = $value->descripcion;
                }
                break;
            case 2:
                if ($laboratorio->porcentaje > $value->rango1) {
                    $caracteristicaPorcentaje = $value->descripcion;
                }
                break;
            case 3:
                if ($laboratorio->porcentaje <= $value->rango1) {
                    $caracteristicaPorcentaje = $value->descripcion;
                }
                break;
            case 4:
                if ($laboratorio->porcentaje >= $value->rango1) {
                    $caracteristicaPorcentaje = $value->descripcion;
                }
                break;
            case 5:
                if ($laboratorio->porcentaje >= $value->rango1 && $laboratorio->porcentaje <= $value->rango2) {
                    $caracteristicaPorcentaje = $value->descripcion;
                }
                break;
        }
    }
    foreach ($litaAdulteracion as $value) {
        switch ($value->signo) {
            case 1:
                if ($laboratorio->c13 < $value->rango1) {
                    $caracteristicaAdulteracion = $value->descripcion;
                }
                break;
            case 2:
                if ($laboratorio->c13 > $value->rango1) {
                    $caracteristicaAdulteracion = $value->descripcion;
                }
                break;
            case 3:
                if ($laboratorio->c13 <= $value->rango1) {
                    $caracteristicaAdulteracion = $value->descripcion;
                }
                break;
            case 4:
                if ($laboratorio->c13 >= $value->rango1) {
                    $caracteristicaAdulteracion = $value->descripcion;
                }
                break;
            case 5:
                if ($laboratorio->c13 >= $value->rango1 && $laboratorio->c13 <= $value->rango2) {
                    $caracteristicaAdulteracion = $value->descripcion;
                }
                break;
        }
    }

    foreach ($listaProceso as $value) {
        switch ($value->signo) {
            case 1:
                if ($laboratorio->hmf < $value->rango1) {
                    $caracteristicaProceso = $value->descripcion;
                }
                break;
            case 2:
                if ($laboratorio->hmf > $value->rango1) {
                    $caracteristicaProceso = $value->descripcion;
                }
                break;
            case 3:
                if ($laboratorio->hmf <= $value->rango1) {
                    $caracteristicaProceso = $value->descripcion;
                }
                break;
            case 4:
                if ($laboratorio->hmf >= $value->rango1) {
                    $caracteristicaProceso = $value->descripcion;
                }
                break;
            case 5:
                if ($laboratorio->hmf >= $value->rango1 && $laboratorio->hmf <= $value->rango2) {
                    $caracteristicaProceso = $value->descripcion;
                }
                break;
        }
    }

    foreach ($listaColor as $value) {
        switch ($value->signo) {
            case 1:
                if ($laboratorio->color < $value->rango1) {
                    $caracteristicasColor = $value->descripcion;
                }
                break;
            case 2:
                if ($laboratorio->color > $value->rango1) {
                    $caracteristicasColor = $value->descripcion;
                }
                break;
            case 3:
                if ($laboratorio->color <= $value->rango1) {
                    $caracteristicasColor = $value->descripcion;
                }
                break;
            case 4:
                if ($laboratorio->color >= $value->rango1) {
                    $caracteristicasColor = $value->descripcion;
                }
                break;
            case 5:
                if ($laboratorio->color >= $value->rango1 && $laboratorio->color <= $value->rango2) {
                    $caracteristicasColor = $value->descripcion;
                }
                break;
        }
    }

    foreach ($listaTetra as $value) {
        switch ($value->signo) {
            case 1:
                if ($laboratorio->tt < $value->rango1) {
                    $caracteristicasTetra = $value->descripcion;
                }
                break;
            case 2:
                if ($laboratorio->tt > $value->rango1) {
                    $caracteristicasTetra = $value->descripcion;
                }
                break;
            case 3:
                if ($laboratorio->tt <= $value->rango1) {
                    $caracteristicasTetra = $value->descripcion;
                }
                break;
            case 4:
                if ($laboratorio->tt >= $value->rango1) {
                    $caracteristicasTetra = $value->descripcion;
                }
                break;
            case 5:
                if ($laboratorio->tt >= $value->rango1 && $laboratorio->tt <= $value->rango2) {
                    $caracteristicasTetra = $value->descripcion;
                }
                break;
        }
    }


    if ($laboratorio->idLaboratorio == 0 && $laboratorio->porcentaje !== null) {
        $sql = "INSERT INTO laboratorio (entradaNo, idAlmacen, porcentaje, sf, st, c13, hmf, color, tt, micro, idFloracion, resultadoFinal, marcaInterna, estado, sfDescripcion, stDescripcion, porcentajeDescripcion, adulteracionDescripcion, procesoDescripcion, colorDescripcion, ttDescripcion) "
                . "VALUES (:idAlmacenEncabezado, :idAlmacen, :porcentaje, :sf, :st, :c13, :hmf, :color, :tt, :micro, :idFloracion, :resultadoFinal, :marcaInterna, 1, '$caracteristicaSf', '$caracteristicaSt', '$caracteristicaPorcentaje', '$caracteristicaAdulteracion','$caracteristicaProceso', '$caracteristicasColor', '$caracteristicasTetra')";
    } else {
        $sql = "UPDATE laboratorio SET"
                . " porcentaje = :porcentaje, sf = :sf, st= :st, c13= :c13, hmf= :hmf, color= :color, tt= :tt, micro = :micro,"
                . "idFloracion = :idFloracion , resultadoFinal= :resultadoFinal, marcaInterna= :marcaInterna, sfDescripcion='$caracteristicaSf', stDescripcion='$caracteristicaSt', porcentajeDescripcion = '$caracteristicaPorcentaje', adulteracionDescripcion = '$caracteristicaAdulteracion', procesoDescripcion ='$caracteristicaProceso', colorDescripcion = '$caracteristicasColor', ttDescripcion = '$caracteristicasTetra' WHERE idLaboratorio= :idLaboratorio";
    }
    $datosLaboratorio = $con->prepare($sql);
    $datosLaboratorio->bindParam(':idAlmacenEncabezado', $laboratorio->idAlmacenEncabezado);
    $datosLaboratorio->bindParam(':idAlmacen', $laboratorio->idAlmacen);
    $datosLaboratorio->bindParam(':porcentaje', $laboratorio->porcentaje);
    $datosLaboratorio->bindParam(':sf', $laboratorio->sf);
    $datosLaboratorio->bindParam(':st', $laboratorio->st);
    $datosLaboratorio->bindParam(':c13', $laboratorio->c13);
    $datosLaboratorio->bindParam(':hmf', $laboratorio->hmf);
    $datosLaboratorio->bindParam(':color', $laboratorio->color);
    $datosLaboratorio->bindParam(':tt', $laboratorio->tt);
    $datosLaboratorio->bindParam(':micro', $laboratorio->micro);
    $datosLaboratorio->bindParam(':idFloracion', $laboratorio->idFloracion);
    $datosLaboratorio->bindParam(':resultadoFinal', $laboratorio->resultadoFinal);
    $datosLaboratorio->bindParam(':marcaInterna', $laboratorio->marcaInterna);
    $datosLaboratorio->bindParam(':idLaboratorio', $laboratorio->idLaboratorio);
    $datosLaboratorio->execute();
}
if ($datosLaboratorio == false) {
    echo 'Error al ingresar';
} else {
    echo 'Agregado exitosamente';
}
?>