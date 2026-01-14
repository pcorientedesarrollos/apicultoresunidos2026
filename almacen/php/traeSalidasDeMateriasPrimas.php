<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

function obtenerSalidasMateriaPrima($fecha1 = false, $fecha2 = false)
{
    if (isset($_GET['tipoDeMiel'])) {
        $tipoDeMiel = $_GET['tipoDeMiel'];
    }

    switch ($tipoDeMiel) {
        case '0':
            $encabezado = 'materiaprimaencabezadosalidas';
            break;
        case '1':
            $encabezado = 'materiaprimaencabezadosalidas';
            break;
        case '2':
            $encabezado = 'materiaprimaencabezadosalidas_organico';
            break;
        case '7':
            $encabezado = 'materiaprimaencabezadosalidas_naranjo';
            break;
        default:
            throw new Exception('Tipo de miel no es válido');
            break;
    }

    global $con;
    $query = "SELECT * FROM $encabezado";
    if ($fecha1 && $fecha2) {
        $query .= " WHERE fecha BETWEEN '" . $fecha1 . "' AND '" . $fecha2 . "'";
    }
    $query .= " ORDER BY fecha DESC";
    $datos = $con->prepare($query);
    $datos->execute();

    $arraySalida = array();
    while ($row = $datos->fetch()) {
        $infoSalida = new stdClass();
        $infoSalida->idSalidaMateria = $row["idSalidaMateria"];
        $infoSalida->fecha = $row["fecha"];
        $infoSalida->cantidadTotal = $row["cantidadTotal"];
        $infoSalida->importeTotal = $row["importeTotal"];
        $infoSalida->tipoCliente = $row["tipoCliente"];
        $infoSalida->idProveedor = $row["idProveedor"];

        if ($infoSalida->tipoCliente == '1') {
            $sqlNombre = "SELECT nombre AS proveedor FROM proveedor WHERE idProveedor = $infoSalida->idProveedor";
            $datoNombre = $con->prepare($sqlNombre);
            $datoNombre->execute();
            while ($row = $datoNombre->fetch()) {
                $infoSalida->proveedor = $row["proveedor"];
            }
        } else if ($infoSalida->tipoCliente == '3') {
            $sqlNombre = "SELECT nombreProveedor AS proveedor FROM proveedoresmantto WHERE idProveedorMantto = $infoSalida->idProveedor";
            $datoNombre = $con->prepare($sqlNombre);
            $datoNombre->execute();
            while ($row = $datoNombre->fetch()) {
                $infoSalida->proveedor = $row["proveedor"];
            }
        }  else if ($infoSalida->tipoCliente == '6') {
            $sqlNombre = "SELECT nombre AS proveedor FROM clientes WHERE idCliente = $infoSalida->idProveedor";
            $datoNombre = $con->prepare($sqlNombre);
            $datoNombre->execute();
            while ($row = $datoNombre->fetch()) {
                $infoSalida->proveedor = $row["proveedor"];
            }
        }else {
            $infoSalida->proveedor = "";
        }

        $arraySalida[] = $infoSalida;
    }

    return $arraySalida;
}


if (!isset($_GET['auto'])) {
    $arraySalida = obtenerSalidasMateriaPrima();
    echo json_encode($arraySalida);
}
