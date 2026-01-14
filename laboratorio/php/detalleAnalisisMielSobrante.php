<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$postdata = file_get_contents('php://input');
try {
    if (!$postdata) {
        throw new Exception('No se recibieron los parámetros');
    } else {
        $postdata = json_decode($postdata);
    }
    $id = $postdata->idAlmacen;
    $idTipoDeMiel = $postdata->idTipoDeMiel;

    $sql = "SELECT al.idEncabezadoSobrante, al.sobrante, s.nombre, tdm.tipoDeMiel,
    CASE WHEN al.tipo = 2 THEN 'Exportación' WHEN al.tipo = 1 THEN 'Nacional' END AS tipo
    FROM almacensobrantesencabezado al
    LEFT JOIN sobrantes s ON s.idSobrante = al.sobrante
    LEFT JOIN tiposdemiel tdm ON tdm.idTipoDeMiel = :tipoDeMiel
    WHERE al.idEncabezadoSobrante = :id ";
    $data = $con->prepare($sql);
    $data->bindParam(':id', $id);
    $data->bindParam(':tipoDeMiel', $idTipoDeMiel);
    $data->execute();
    if ($data == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $detalleLab = new stdClass();

    while ($rs = $data->fetch()) {
        $detalleLab->entrada = $rs["idEncabezadoSobrante"];
        $detalleLab->sobrante = $rs["sobrante"];
        $detalleLab->nombre = $rs["nombre"];
        $detalleLab->tipoDeMiel = $rs["tipoDeMiel"];
        $detalleLab->tipo = $rs["tipo"];
        $detalleLab->informacionLab = array();

        $sqlDetalle = "SELECT al.consecutivoEntrada, CONCAT(s.codigo, '-', al.consecutivo) AS idAlmacen, al.consecutivo AS consecutivo, al.neto, al.sobrante, al.tipoDeMiel,
        lab.idLaboratorio, lab.porcentaje, lab.sf, lab.st,
        lab.c13, lab.hmf, lab.color, lab.tt, lab.micro, lab.marcaInterna,
        lab.idFloracion, lab.resultadoFinal
        FROM almacensobrantes al
        LEFT JOIN laboratorio_sobrantes lab ON lab.idAlmacen = al.consecutivo AND lab.sobrante = :sobrante AND lab.tipoDeMiel = :miel
        AND al.sobrante = :sobrante AND al.tipoDeMiel = :miel
        LEFT JOIN floraciones fl 
        ON fl.idFloracion = lab.idFloracion
        LEFT JOIN almacensobrantesencabezado ale
        on ale.idEncabezadoSobrante = al.consecutivoEntrada
        LEFT JOIN sobrantes s ON s.idSobrante = al.sobrante
        WHERE al.consecutivoEntrada = :id";
        $datos = $con->prepare($sqlDetalle);
        $datos->bindParam(':id', $detalleLab->entrada);
        $datos->bindParam(':sobrante', $detalleLab->sobrante);
        $datos->bindParam(':miel', $idTipoDeMiel);
        $datos->execute();

        if ($datos == FALSE) {
            throw new Exception($con->errorInfo());
        }

        while ($rs = $datos->fetch()) {
            $informacionLab = new stdClass();
            $informacionLab->idAlmacen = $rs['idAlmacen'];
            $informacionLab->consecutivo = $rs['consecutivo'];
            $informacionLab->neto = $rs["neto"];
            // $informacionLab->humedad = $rs["humedad"];
            $informacionLab->idAlmacenEncabezado = $rs["consecutivoEntrada"];
            $informacionLab->sobrante = $rs["sobrante"];
            $informacionLab->tipoDeMiel = $rs["tipoDeMiel"];
            $informacionLab->idLaboratorio = $rs["idLaboratorio"];
            $informacionLab->porcentaje = $rs["porcentaje"];
            $informacionLab->sf = $rs["sf"];
            $informacionLab->st = $rs["st"];
            $informacionLab->c13 = $rs["c13"];
            $informacionLab->hmf = $rs["hmf"];
            $informacionLab->marcaInterna = $rs["marcaInterna"];
            $informacionLab->color = $rs["color"];
            $informacionLab->tt = $rs["tt"];
            $informacionLab->micro = $rs["micro"];
            $informacionLab->idFloracion = $rs["idFloracion"];
            $informacionLab->resultadoFinal = $rs["resultadoFinal"];
            if ($rs["idLaboratorio"] == null) {
                $informacionLab->idLaboratorio = 0;
            } else {
                $informacionLab->idLaboratorio = $rs["idLaboratorio"];
            }
            $detalleLab->informacionLab[] = $informacionLab;
        }
    }
    echo json_encode(['error' => false, 'message' => '', 'data' => $detalleLab]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Line :' . $e->getLine(), 'data' => []]);
}
