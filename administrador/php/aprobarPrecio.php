<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    if (!isset($_GET['miel'])) {
        throw new Exception('No se recibió parámetro');
    } else {
        if (isset($_GET['tambo'])) {
            switch ($_GET['miel']) {
                case 1:
                    $tabla = 'almacen';
                    break;
                case 2:
                    $tabla = 'almacen_organico';
                    break;
                case 5:
                    $tabla = 'almacen_mantequilla';
                    break;
                case 6:
                    $tabla = 'almacen_altiplano';
                    break;
                case 7:
                    $tabla = 'almacen_naranjo';
                    break;
                case 8:
                    $tabla = 'almacen_aguacate';
                    break;
                case 9:
                    $tabla = 'almacen_mezquite';
                    break;
            }
        } else if (isset($_GET['cubeta'])) {
            switch ($_GET['miel']) {
                case 1:
                    $tabla = 'cubetasdetalle';
                    break;
                case 2:
                    $tabla = 'cubetasdetalle_organico';
                    break;
                case 5:
                    $tabla = 'cubetasdetalle_mantequilla';
                    break;
                case 6:
                    $tabla = 'cubetasdetalle_altiplano';
                    break;
                case 7:
                    $tabla = 'cubetasdetalle_naranjo';
                    break;
                case 8:
                    $tabla = 'cubetasdetalle_aguacate';
                    break;
                case 9:
                    $tabla = 'cubetasdetalle_mezquite';
                    break;
            }
        }
        $id = $_GET['id'];
        $valor = $_GET["valor"];
    }


    if (isset($_GET['entrada'])) {
        $entrada = $_GET['entrada'];
        $sql = "UPDATE $tabla SET aprobado = :valor WHERE idAlmacenEncabezado = :id";
        $data = $con->prepare($sql);
        $data->bindParam(':valor', $valor);
        $data->bindParam(':id', $entrada);
        $data->execute();
        if (!$data) {
            throw new Exception($con->errorInfo());
        }
    } else {
        // $sql = "UPDATE $tabla SET aprobado = '1' WHERE idAlmacen = :id";
        $sql = "UPDATE $tabla SET aprobado = :valor WHERE idAlmacen = :id";
        $data = $con->prepare($sql);
        $data->bindParam(':valor', $valor);
        $data->bindParam(':id', $id);
        $data->execute();
        if (!$data) {
            throw new Exception($con->errorInfo());
        }
    }

    echo json_encode(['error' => false, 'message' => 'Cambio guardado']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
