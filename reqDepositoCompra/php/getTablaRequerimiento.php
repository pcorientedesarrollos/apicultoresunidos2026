<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    $query = "SELECT re.idRequisicion, re.fechaRequisicion, re.fechaImpresion, re.totalTambores, re.totalKilos, re.importeTotal
    FROM requisicionencabezado re
    GROUP BY re.idRequisicion ORDER BY re.fechaRequisicion DESC";
    $data = $con->prepare($query);
    $data->execute();
    if (!$data) {
        throw new Exception($con->errorInfo());
    }
    $resultadoQuery = $data->fetchAll(PDO::FETCH_ASSOC);
    $resultado = array();
    foreach ($resultadoQuery as $requerimiento) {

        // Revisar cuantos req tiene registrados y cuantos están cobrados

        $sqlTotal = $con->prepare("SELECT COUNT(idDetalle) as total FROM requisiciondetalle WHERE idRequisicion = :idRequisicion;");
        $sqlTotal->bindParam(':idRequisicion', $requerimiento['idRequisicion']);
        $sqlTotal->execute();
        if (!$sqlTotal) {
            throw new Exception($con->errorInfo());
        }
        $resultadoTotal = $sqlTotal->fetch(PDO::FETCH_ASSOC);
        if (!$resultadoTotal) {
            $requerimiento['totalDetalles'] = 0;
        } else {
            $requerimiento['totalDetalles'] = intval($resultadoTotal['total']);
        }

        $sqlCobrado = $con->prepare("SELECT COUNT(idDetalle) as cobrado FROM requisiciondetalle WHERE idRequisicion = :idRequisicion AND cobrado = 1;");
        $sqlCobrado->bindParam(':idRequisicion', $requerimiento['idRequisicion']);
        $sqlCobrado->execute();
        if (!$sqlCobrado) {
            throw new Exception($con->errorInfo());
        }
        $resultadoCobrado = $sqlCobrado->fetch(PDO::FETCH_ASSOC);
        if (!$resultadoCobrado) {
            $requerimiento['totalCobrado'] = 0;
        } else {
            $requerimiento['totalCobrado'] = intval($resultadoCobrado['cobrado']);
        }

        if ($requerimiento['totalDetalles'] > 0) {
            if ($requerimiento['totalCobrado'] > 0) {
                $requerimiento['puedeEliminar'] = false;
            } else {
                $requerimiento['puedeEliminar'] = true;
            }
        } else {
            $requerimiento['puedeEliminar'] = true;
        }

        array_push($resultado, $requerimiento);
    }

    echo json_encode(['error' => false, 'resultado' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
