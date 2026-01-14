<?php
include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

function obtenerSalidasEnvasesFrascos($fecha1 = false, $fecha2 = false)
{

    global $con;

    $query = "SELECT e.* FROM envasesfrascosencabezadosalidas e
    LEFT JOIN envasesfrascosdetallesalidas d ON d.idSalidaEnvases = e.idSalidaEnvases";
    if ($fecha1 && $fecha2) {
        $query .= " WHERE e.fecha BETWEEN '" . $fecha1 . "' AND '" . $fecha2 . "'";
    }
    if (isset($_GET['mes'])) {
        $mes = $_GET['mes'];
    }

    if (isset($_GET['reporte']) && $_GET['reporte'] == '1') {
        if (isset($_GET['mes'])) {
            $query .= " WHERE e.idSalidaEnvases = d.idSalidaEnvases AND SUBSTR(e.fecha FROM 6 FOR 2) = " . $mes;
        } else {
            $query .= " WHERE e.idSalidaEnvases = d.idSalidaEnvases";
        }
    } else if (isset($_GET['reporte']) && $_GET['reporte'] == '2') {
        if (isset($_GET['mes'])) {
            $query .= " WHERE e.idSalidaEnvases NOT IN (SELECT d.idSalidaEnvases FROM envasesfrascosdetallesalidas d) AND SUBSTR(e.fecha FROM 6 FOR 2) = " . $mes;
        } else {
            $query .= " WHERE e.idSalidaEnvases NOT IN (SELECT d.idSalidaEnvases FROM envasesfrascosdetallesalidas d)";
        }
    } else if (isset($_GET['mes'])) {
        $query .= " WHERE SUBSTR(fecha FROM 6 FOR 2) = " . $mes;
    }

    $query .= " GROUP BY e.idSalidaEnvases ORDER BY e.idSalidaEnvases, e.fecha DESC";
    $datos = $con->prepare($query);
    $datos->execute();

    $arraySalida = array();
    while ($row = $datos->fetch()) {
        $infoSalida = new stdClass();
        $infoSalida->idSalidaEnvases = $row["idSalidaEnvases"];
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
        } else {
            $infoSalida->proveedor = "";
        }

        $arraySalida[] = $infoSalida;
    }

    return $arraySalida;
}


if (!isset($_GET['auto'])) {
    $arraySalida = obtenerSalidasEnvasesFrascos();
    echo json_encode($arraySalida);
}
