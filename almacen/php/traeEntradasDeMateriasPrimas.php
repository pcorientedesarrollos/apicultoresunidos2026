<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

function obtenerEntradasMateriaPrima($fecha1 = false, $fecha2 = false)
{

    if (isset($_GET['tipoDeMiel'])) {
        $tipoDeMiel = $_GET['tipoDeMiel'];
    }

    switch ($tipoDeMiel) {
        case '0':
            $encabezado = 'materiaprimaencabezadoentradas';
            $detalle = 'materiaprimadetalleentradas';
            break;
        case '1':
            $encabezado = 'materiaprimaencabezadoentradas';
            $detalle = 'materiaprimadetalleentradas';
            $tipo = '1';
            break;
        case '2':
            $encabezado = 'materiaprimaencabezadoentradas_organico';
            $detalle = 'materiaprimadetalleentradas_organico';
            $tipo = '2';
            break;
        case '7':
            $encabezado = 'materiaprimaencabezadoentradas_naranjo';
            $detalle = 'materiaprimadetalleentradas_naranjo';
            $tipo = '7';
            break;
        default:
            throw new Exception('Tipo de miel no es válido');
            break;
    }

    global $con;
    $query = "SELECT mpe.*, $tipo AS tipoDeMiel FROM $encabezado mpe
            LEFT JOIN $detalle mpd ON mpd.idEntradaMateria = mpe.idEntradaMateria";
    if ($fecha1 && $fecha2) {
        $query .= " WHERE mpe.fecha BETWEEN '" . $fecha1 . "' AND '" . $fecha2 . "'";
    }

    if (isset($_GET['mes'])) {
        $mes = $_GET['mes'];
    }

    if (isset($_GET['reporte']) && $_GET['reporte'] == '1') {
        if (isset($_GET['mes'])) {
            $query .= " WHERE mpe.idEntradaMateria = mpd.idEntradaMateria AND SUBSTR(mpe.fecha FROM 6 FOR 2) = " . $mes;
        } else {
            $query .= " WHERE mpe.idEntradaMateria = mpd.idEntradaMateria";
        }
    } else if (isset($_GET['reporte']) && $_GET['reporte'] == '2') {
        if (isset($_GET['mes'])) {
            $query .= " WHERE mpe.idEntradaMateria NOT IN (SELECT mpd.idEntradaMateria FROM $detalle mpd) AND SUBSTR(mpe.fecha FROM 6 FOR 2) = " . $mes;
        } else {
            $query .= " WHERE mpe.idEntradaMateria NOT IN (SELECT mpd.idEntradaMateria FROM $detalle mpd)";
        }
    } else if (isset($_GET['mes'])) {
        $query .= " WHERE SUBSTR(fecha FROM 6 FOR 2) = " . $mes;
    }

    $query .= " GROUP BY mpe.idEntradaMateria ORDER BY  mpe.idEntradaMateria, mpe.fecha DESC";
    $datos = $con->prepare($query);
    $datos->execute();

    $arrayEntrada = array();
    while ($row = $datos->fetch()) {
        $infoEntrada = new stdClass();
        $infoEntrada->idEntradaMateria = $row["idEntradaMateria"];
        $infoEntrada->fecha = $row["fecha"];
        $infoEntrada->cantidadTotal = $row["cantidadTotal"];
        $infoEntrada->importeTotal = $row["importeTotal"];
        $infoEntrada->tipoCliente = $row["tipoCliente"];
        $infoEntrada->idProveedor = $row["idProveedor"];
        $infoEntrada->tipoDeMiel = $row["tipoDeMiel"];


        if ($infoEntrada->tipoCliente == '1') {
            $sqlNombre = "SELECT nombre AS proveedor FROM proveedor WHERE idProveedor = $infoEntrada->idProveedor";
            $datoNombre = $con->prepare($sqlNombre);
            $datoNombre->execute();
            while ($row = $datoNombre->fetch()) {
                $infoEntrada->proveedor = $row["proveedor"];
            }
        } else if ($infoEntrada->tipoCliente == '3') {
            $sqlNombre = "SELECT nombreProveedor AS proveedor FROM proveedoresmantto WHERE idProveedorMantto = $infoEntrada->idProveedor";
            $datoNombre = $con->prepare($sqlNombre);
            $datoNombre->execute();
            while ($row = $datoNombre->fetch()) {
                $infoEntrada->proveedor = $row["proveedor"];
            }
        }
        else if ($infoEntrada->tipoCliente == '6') {
            $sqlNombre = "SELECT nombre AS proveedor FROM clientes WHERE idCliente = $infoEntrada->idProveedor";
            $datoNombre = $con->prepare($sqlNombre);
            $datoNombre->execute();
            while ($row = $datoNombre->fetch()) {
                $infoEntrada->proveedor = $row["proveedor"];
            }
        } else {
            $infoEntrada->proveedor = "";
        }

        $arrayEntrada[] = $infoEntrada;
    }

    return $arrayEntrada;
}

if (!isset($_GET['auto'])) {
    $arrayEntrada = obtenerEntradasMateriaPrima();
    echo json_encode($arrayEntrada);
}
