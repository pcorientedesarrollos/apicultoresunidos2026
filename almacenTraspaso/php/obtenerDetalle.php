<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$miel = $_GET['miel'];

try {
    if (!$miel) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $id = $_GET['id'];
    }

    if (isset($miel)) {
        switch ($miel) {
            case '1':
                $encabezado = 'almacenencabezadotraspaso';
                $detalle = 'almacentraspaso';
                $laboratorio = 'laboratorio';
                break;
            case '2':
                $encabezado = 'almacenencabezadotraspaso_organico';
                $detalle = 'almacentraspaso_organico';
                $laboratorio = 'laboratorio_organico';
            default:
                throw new Exception('Tipo de miel inválido');
                break;
        }
    }

    $con->beginTransaction();

    $sql = "SELECT * FROM $encabezado al 
        LEFT JOIN proveedor pr 
        ON pr.idProveedor = al.idProveedor 
        LEFT JOIN direccion dr 
        ON dr.idDireccion = pr.idDireccion
        LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
        WHERE idAlmacen = :id";
    $datos = $con->prepare($sql);
    $datos->bindParam(':id', $id);
    $datos->execute();
    if ($datos == false) {
        throw new Exception($con->errorInfo());
    } else {
        $almacen = new stdClass();
        while ($rs = $datos->fetch()) {
            $almacen->idAlmacen = $rs["idAlmacen"];
            $almacen->idProveedor = $rs["idProveedor"];
            $almacen->proveedor = $rs["nombre"];
            $almacen->idlocalidad = $rs["idlocalidad"];
            $almacen->localidad = $rs["localidad"];
            $almacen->idSagarpa = $rs["idSagarpa"];
            $almacen->fecha = $rs["fecha"];
            $almacen->folio = $rs["folio"];
            $almacen->totalCompra = $rs["totalCompra"];
            $almacen->almacenDetalle = array();

            $sqlAlmacenDetalle = "SELECT * 
                FROM $detalle al
                left JOIN $laboratorio lab 
                on lab.idAlmacen = al.idAlmacen
                WHERE idAlmacenEncabezado = :id";
            $dato = $con->prepare($sqlAlmacenDetalle);
            $dato->bindParam(':id', $id);
            $dato->execute();
            $cont = 0;
            while ($rsAlmacenDetalle = $dato->fetch()) {
                $almacenDetalle = new stdClass();
                $almacenDetalle->idAlmacen = $rsAlmacenDetalle[0];
                $almacenDetalle->idAlmacenEncabezado = $rsAlmacenDetalle["idAlmacenEncabezado"];
                $almacenDetalle->zona = $rsAlmacenDetalle["zona"];
                $almacenDetalle->trazabilidad = $rsAlmacenDetalle["trazabilidad"];
                $almacenDetalle->pesoLista = $rsAlmacenDetalle["pesoLista"];
                $almacenDetalle->bruto = $rsAlmacenDetalle["bruto"];
                $almacenDetalle->tara = $rsAlmacenDetalle["tara"];
                $almacenDetalle->neto = $rsAlmacenDetalle["neto"];
                $almacenDetalle->diferencia = $rsAlmacenDetalle["diferencia"];
                $almacenDetalle->humedad = $rsAlmacenDetalle["humedad"];
                $almacenDetalle->estado = $rsAlmacenDetalle["autorizado"];
                $almacenDetalle->precio = $rsAlmacenDetalle["precio"];
                $almacenDetalle->costoTotal = $rsAlmacenDetalle["costoTotal"];
                $almacenDetalle->referencia = $rsAlmacenDetalle["referencia"];
                if ($rsAlmacenDetalle["idLaboratorio"] == null) {
                    $almacenDetalle->idLaboratorio = 0;
                } else {
                    $almacenDetalle->idLaboratorio = $rsAlmacenDetalle["idLaboratorio"];
                }
                $almacenDetalle->porcentaje = $rsAlmacenDetalle["porcentaje"];
                $almacenDetalle->sf = $rsAlmacenDetalle["sf"];
                $almacenDetalle->st = $rsAlmacenDetalle["st"];
                $almacenDetalle->c13 = $rsAlmacenDetalle["c13"];
                $almacenDetalle->hmf = $rsAlmacenDetalle["hmf"];
                $almacenDetalle->resultadoFinal = $rsAlmacenDetalle["resultadoFinal"];
                $almacenDetalle->marcaInterna = $rsAlmacenDetalle["marcaInterna"];
                $almacenDetalle->estadoLab = $rsAlmacenDetalle["estado"];
                $almacen->almacenDetalle[$cont] = $almacenDetalle;
                $cont++;
            }
        }
        echo json_encode($almacen);
    }

    $mensaje = 'Hecho';

    // $con->commit();
    // echo json_encode(['error' => false, 'message' => $mensaje]);

} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine()]);
}
