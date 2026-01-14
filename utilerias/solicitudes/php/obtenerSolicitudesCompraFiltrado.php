<?php

include_once '../../../DAOConeccion/conePDO.php';
include_once '../../../controlAdministrativo/php/nombreDePersona.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {

    $sql = "SELECT al.idSolicitudCompra, al.fecha, d.area AS departamento, p.nombre AS solicita, al.estado
        FROM solicitudcompra_encabezado al 
        LEFT JOIN areas d ON d.idArea = al.departamento
        LEFT JOIN personaloaxaca p ON p.idPersonalOM = al.solicita
        WHERE al.estado = '0'";
    if (isset($_GET['mes']) && isset($_GET['area'])) {
        $mes = $_GET['mes'];
        $area = $_GET['area'];
        $sql .= " AND SUBSTR(fecha FROM 6 FOR 2) = $mes AND al.departamento = $area";
    } else if (isset($_GET['mes']) && !isset($_GET['area'])) {
        $mes = $_GET['mes'];
        $sql .= " AND SUBSTR(fecha FROM 6 FOR 2) = $mes";
    } else if (isset($_GET['area']) && !isset($_GET['mes'])) {
        $area = $_GET['area'];
        $sql .= " AND al.departamento = $area";
    }

    $sql .= " GROUP BY al.idSolicitudCompra ORDER BY al.fecha DESC";

    $datos = $con->prepare($sql);
    // $datos->bindParam(':tipo', $tipoDeReportes);
    $datos->execute();
    if ($datos == FALSE) {
        throw new ErrorException($mensaje, 0, $severidad, $fichero, $línea);
    }
    $resultadoQuery = $datos->fetchAll(PDO::FETCH_ASSOC);
    $resultado = array();
    foreach ($resultadoQuery as $solicitud) {
        $sqlTotal = $con->prepare("SELECT COUNT(idDetalleCompra) as total FROM solicitudcompra_conceptos WHERE idSolicitudCompra = :idSolicitudCompra;");
        $sqlTotal->bindParam(':idSolicitudCompra', $solicitud['idSolicitudCompra']);
        $sqlTotal->execute();
        if (!$sqlTotal) {
            throw new ErrorException($mensaje, 0, $severidad, $fichero, $línea);
        }
        $resultadoTotal = $sqlTotal->fetch(PDO::FETCH_ASSOC);
        if (!$resultadoTotal) {
            $solicitud['totalDetalles'] = 0;
        } else {
            $solicitud['totalDetalles'] = intval($resultadoTotal['total']);
        }

        $sqlCobrado = $con->prepare("SELECT COUNT(idDetalleCompra) as autorizado FROM solicitudcompra_conceptos WHERE idSolicitudCompra = :idSolicitudCompra AND estado = 1;");
        $sqlCobrado->bindParam(':idSolicitudCompra', $solicitud['idSolicitudCompra']);
        $sqlCobrado->execute();
        if (!$sqlCobrado) {
            throw new ErrorException($mensaje, 0, $severidad, $fichero, $línea);
        }
        $resultadoCobrado = $sqlCobrado->fetch(PDO::FETCH_ASSOC);
        if (!$resultadoCobrado) {
            $solicitud['totalAutorizado'] = 0;
        } else {
            $solicitud['totalAutorizado'] = intval($resultadoCobrado['autorizado']);
        }

        if ($solicitud['totalDetalles'] > 0) {
            if ($solicitud['totalAutorizado'] > 0) {
                $solicitud['puedeEliminar'] = false;
            } else {
                $solicitud['puedeEliminar'] = true;
            }
        } else {
            $solicitud['puedeEliminar'] = true;
        }

        $sqlImportes = $con->prepare("SELECT SUM(costo) as importe FROM solicitudcompra_conceptos WHERE idSolicitudCompra = :idSolicitudCompra");
        $sqlImportes->bindParam(':idSolicitudCompra', $solicitud['idSolicitudCompra']);
        $sqlImportes->execute();
        if (!$sqlImportes) {
            throw new ErrorException($mensaje, 0, $severidad, $fichero, $línea);
        }
        $resultadoImporte = $sqlImportes->fetch(PDO::FETCH_ASSOC);
        if (!$resultadoImporte) {
            $solicitud['importeTotal'] = 0;
        } else {
            $solicitud['importeTotal'] = intval($resultadoImporte['importe']);
        }

        array_push($resultado, $solicitud);
    }

    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => []]);
    exit();
}
