<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$postdata = file_get_contents('php://input');
try {
    if(!$postdata){
        throw new Exception('No se recibieron los parámetros');
    } else {
        $postdata = json_decode($postdata);
    }
    $id = $postdata->idAlmacen;
    $idTipoDeMiel = $postdata->idTipoDeMiel;
    
    switch ($idTipoDeMiel):
        case '1':
            $almacen_tabla = 'almacen';
            $almacenencabezado_tabla = 'almacenencabezado';
            $laboratorio_tabla = 'laboratorio';
        break;
        case '2':
            $almacen_tabla = 'almacen_organico';
            $almacenencabezado_tabla = 'almacenencabezado_organico';
            $laboratorio_tabla = 'laboratorio_organico';
        break;
        case '5':
            $almacen_tabla = 'almacen_mantequilla';
            $almacenencabezado_tabla = 'almacenencabezado_mantequilla';
            $laboratorio_tabla = 'laboratorio_mantequilla';
        break;
        case '6':
            $almacen_tabla = 'almacen_altiplano';
            $almacenencabezado_tabla = 'almacenencabezado_altiplano';
            $laboratorio_tabla = 'laboratorio_altiplano';
        break;
        case '7':
            $almacen_tabla = 'almacen_naranjo';
            $almacenencabezado_tabla = 'almacenencabezado_naranjo';
            $laboratorio_tabla = 'laboratorio_naranjo';
        break;
        case '8':
            $almacen_tabla = 'almacen_aguacate';
            $almacenencabezado_tabla = 'almacenencabezado_aguacate';
            $laboratorio_tabla = 'laboratorio_aguacate';
        break;
        case '9':
            $almacen_tabla = 'almacen_mezquite';
            $almacenencabezado_tabla = 'almacenencabezado_mezquite';
            $laboratorio_tabla = 'laboratorio_mezquite';
        break;
        default:
            throw new Exception('Parametro inválido');
        break;
    endswitch;

    $sql = "SELECT al.idAlmacen, pr.nombre, lo.localidad, pr.idSagarpa, tdm.tipoDeMiel
    FROM $almacenencabezado_tabla al
    LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor
    LEFT JOIN direccion dr ON pr.idDireccion = dr.idDireccion
    LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
    LEFT JOIN tiposdemiel tdm ON tdm.idTipoDeMiel = :tipoDeMiel
    WHERE al.idAlmacen = :id ORDER BY al.idAlmacen ASC";
    $data = $con->prepare($sql);
    $data->bindParam(':id', $id);
    $data->bindParam(':tipoDeMiel', $idTipoDeMiel);
    $data->execute();
    if($data == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $detalleLab = new stdClass();

    while ($rs = $data->fetch()) {
        $detalleLab->entrada = $rs["idAlmacen"];
        $detalleLab->proveedor = $rs["nombre"];
        $detalleLab->localidad = $rs["localidad"];
        $detalleLab->idSagarpa = $rs["idSagarpa"];
        $detalleLab->tipoDeMiel = $rs["tipoDeMiel"];
        $detalleLab->informacionLab = array();

        $sqlDetalle = "SELECT al.idAlmacenEncabezado, al.idAlmacen, al.neto, al.humedad,
        lab.idLaboratorio, lab.porcentaje, lab.sf, lab.st,
        lab.c13, lab.hmf, lab.color, lab.tt, lab.micro, lab.marcaInterna,
        lab.idFloracion, lab.resultadoFinal,
        CASE WHEN al.referencia > 0 THEN al.referencia ELSE '---' END AS referencia
        FROM $almacen_tabla al
        LEFT JOIN $laboratorio_tabla lab 
        on lab.idAlmacen = al.idAlmacen
        LEFT JOIN floraciones fl 
        ON fl.idFloracion = lab.idFloracion
        LEFT JOIN $almacenencabezado_tabla ale
        on ale.idAlmacen = al.idAlmacenEncabezado
        WHERE al.idAlmacenEncabezado = :id ORDER BY al.idAlmacen ASC";
        $datos = $con->prepare($sqlDetalle);
        $datos->bindParam(':id', $id);
        $datos->execute();

        if($datos == FALSE) {
            throw new Exception($con->errorInfo());
        }

        while ($rs = $datos->fetch()) {
            $informacionLab = new stdClass();
            $informacionLab->idAlmacen = $rs['idAlmacen'];
            $informacionLab->neto = $rs["neto"];
            $informacionLab->humedad = $rs["humedad"];
            $informacionLab->idAlmacenEncabezado = $rs["idAlmacenEncabezado"];
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
            $informacionLab->referencia = $rs["referencia"];
            if ($rs["idLaboratorio"] == null) {
                $informacionLab->idLaboratorio = 0;
            } else {
                $informacionLab->idLaboratorio = $rs["idLaboratorio"];
            }
            $detalleLab->informacionLab[] = $informacionLab;
        }
    }
    echo json_encode(['error'=>false, 'message'=>'', 'data' => $detalleLab]);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage() . ' Line :' . $e->getLine(), 'data' => []]);
}