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
        case '5':
            $configuracionlaboratorio_tabla = 'configuracionlaboratorio_mantequilla';
            $laboratorio_tabla = 'laboratorio_mantequilla';
            break;
        case '6':
            $configuracionlaboratorio_tabla = 'configuracionlaboratorio_altiplano';
            $laboratorio_tabla = 'laboratorio_altiplano';
            break;
        case '7':
            $configuracionlaboratorio_tabla = 'configuracionlaboratorio_naranjo';
            $laboratorio_tabla = 'laboratorio_naranjo';
            break;
        case '8':
            $configuracionlaboratorio_tabla = 'configuracionlaboratorio_aguacate';
            $laboratorio_tabla = 'laboratorio_aguacate';
            break;
        case '9':
            $configuracionlaboratorio_tabla = 'configuracionlaboratorio_mezquite';
            $laboratorio_tabla = 'laboratorio_mezquite';
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
            $sql = $con->prepare("INSERT INTO $laboratorio_tabla (entradaNo, idAlmacen, porcentaje, estado, porcentajeDescripcion)
            VALUES (:idAlmacenEncabezado, :idAlmacen, :porcentaje, 1, :caracteristicaPorcentaje)");
            $sql->bindParam(':idAlmacenEncabezado',  $laboratorio->idAlmacenEncabezado);
            $sql->bindParam(':idAlmacen',  $laboratorio->idAlmacen);
            $sql->bindParam(':porcentaje',  $laboratorio->porcentaje);
            $sql->bindParam(':caracteristicaPorcentaje',  $caracteristicaPorcentaje);
        } else {
            $sql = $con->prepare("UPDATE $laboratorio_tabla SET porcentaje =:porcentaje, porcentajeDescripcion = :caracteristicaPorcentaje WHERE idLaboratorio = :idLaboratorio");
            $sql->bindParam(':porcentaje', $laboratorio->porcentaje);
            $sql->bindParam(':caracteristicaPorcentaje', $caracteristicaPorcentaje);
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
