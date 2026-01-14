<?php
include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

function obtenerEntradasEnvasesFrascos($fecha1 = false, $fecha2 = false)
{

    global $con;
    $query = "SELECT e.* FROM envasesfrascosencabezadoentradas e
            LEFT JOIN envasesfrascosdetalleentradas d ON d.idEntradaEnvases = e.idEntradaEnvases";
    if ($fecha1 && $fecha2) {
        $query .= " WHERE e.fecha BETWEEN '" . $fecha1 . "' AND '" . $fecha2 . "'";
    }

    if (isset($_GET['mes'])) {
        $mes = $_GET['mes'];
    }

    if (isset($_GET['reporte']) && $_GET['reporte'] == '1') {
        if (isset($_GET['mes'])) {
            $query .= " WHERE e.idEntradaEnvases = d.idEntradaEnvases AND SUBSTR(e.fecha FROM 6 FOR 2) = " . $mes;
        } else {
            $query .= " WHERE e.idEntradaEnvases = d.idEntradaEnvases";
        }
    } else if (isset($_GET['reporte']) && $_GET['reporte'] == '2') {
        if (isset($_GET['mes'])) {
            $query .= " WHERE e.idEntradaEnvases NOT IN (SELECT d.idEntradaEnvases FROM envasesfrascosdetalleentradas d) AND SUBSTR(e.fecha FROM 6 FOR 2) = " . $mes;
        } else {
            $query .= " WHERE e.idEntradaEnvases NOT IN (SELECT d.idEntradaEnvases FROM envasesfrascosdetalleentradas d)";
        }
    } else if (isset($_GET['mes'])) {
        $query .= " WHERE SUBSTR(fecha FROM 6 FOR 2) = " . $mes;
    }

    $query .= " GROUP BY e.idEntradaEnvases ORDER BY  e.idEntradaEnvases, e.fecha DESC";
    $datos = $con->prepare($query);
    $datos->execute();

    $arrayEntrada = array();
    while ($row = $datos->fetch()) {
        $infoEntrada = new stdClass();
        $infoEntrada->idEntradaEnvases = $row["idEntradaEnvases"];
        $infoEntrada->fecha = $row["fecha"];
        $infoEntrada->cantidadTotal = $row["cantidadTotal"];
        $infoEntrada->importeTotal = $row["importeTotal"];
        $infoEntrada->tipoCliente = $row["tipoCliente"];
        $infoEntrada->idProveedor = $row["idProveedor"];

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
        } else {
            $infoEntrada->proveedor = "";
        }

        $arrayEntrada[] = $infoEntrada;
    }

    return $arrayEntrada;
}

if (!isset($_GET['auto'])) {
    $arrayEntrada = obtenerEntradasEnvasesFrascos();
    echo json_encode($arrayEntrada);
}
