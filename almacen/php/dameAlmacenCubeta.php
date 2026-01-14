<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$miel = $_GET['miel'];

try {
    if (!$miel) {
        throw new Exception('No se recibieron parámetros');
    }else{
        $id = $_GET['id'];
    }

    if(isset($miel)){
        switch ($miel) {
                    case '1':
                        $encabezado = 'cubetasencabezado';
                        $detalle = 'cubetasdetalle';
                        break;
                    case '2':
                        $encabezado = 'cubetasencabezado_organico';
                        $detalle = 'cubetasdetalle_organico';
                        break;
                    case '5':
                        $encabezado = 'cubetasencabezado_mantequilla';
                        $detalle = 'cubetasdetalle_mantequilla';
                        break;
                    case '6':
                        $encabezado = 'cubetasencabezado_altiplano';
                        $detalle = 'cubetasdetalle_altiplano';
                        break;
                    case '7':
                        $encabezado = 'cubetasencabezado_naranjo';
                        $detalle = 'cubetasdetalle_naranjo';
                        break;
                    case '8':
                        $encabezado = 'cubetasencabezado_aguacate';
                        $detalle = 'cubetasdetalle_aguacate';
                        break;
                    case '9':
                        $encabezado = 'cubetasencabezado_mezquite';
                        $detalle = 'cubetasdetalle_mezquite';
                        break;
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
        if ($datos == false){
            throw new Exception($con->errorInfo());
        }else {
        
            $almacenCubeta = new stdClass();
            while ($rs = $datos->fetch()) {
                $almacenCubeta->idAlmacen = $rs["idAlmacen"];
                $almacenCubeta->idProveedor = $rs["idProveedor"];
                $almacenCubeta->proveedor = $rs["nombre"];
                $almacenCubeta->idlocalidad = $rs["idlocalidad"];
                $almacenCubeta->localidad = $rs["localidad"];
                $almacenCubeta->idSagarpa = $rs["idSagarpa"];
                $almacenCubeta->fecha = $rs["fecha"];
                $almacenCubeta->folioEntradaTambor = $rs["folioEntradaTambor"];
                $almacenCubeta->almacenDetalleCubeta = array();
                $sqlAlmacenDetalle = "SELECT * 
                FROM $detalle al
                left JOIN laboratorio_organico lab 
                on lab.idAlmacen = al.idAlmacen
                WHERE idAlmacenEncabezado = :id";
                $dato = $con->prepare($sqlAlmacenDetalle);
                $dato->bindParam(':id', $id);
                $dato->execute();
                $cont = 0;
                while ($rsAlmacenDetalle = $dato->fetch()) {
                    $almacenDetalleCubeta = new stdClass();
                    $almacenDetalleCubeta->idAlmacen = $rsAlmacenDetalle[0];
                    $almacenDetalleCubeta->idAlmacenEncabezado = $rsAlmacenDetalle["idAlmacenEncabezado"];
                    $almacenDetalleCubeta->zona = $rsAlmacenDetalle["zona"];
                    $almacenDetalleCubeta->trazabilidad = $rsAlmacenDetalle["trazabilidad"];
                    $almacenDetalleCubeta->pesoLista = $rsAlmacenDetalle["pesoLista"];
                    $almacenDetalleCubeta->bruto = $rsAlmacenDetalle["bruto"];
                    $almacenDetalleCubeta->tara = $rsAlmacenDetalle["tara"];
                    $almacenDetalleCubeta->neto = $rsAlmacenDetalle["neto"];
                    $almacenDetalleCubeta->diferencia = $rsAlmacenDetalle["diferencia"];
                    $almacenDetalleCubeta->humedad = $rsAlmacenDetalle["humedad"];
                    $almacenDetalleCubeta->tamborAsignado = $rsAlmacenDetalle["tamborAsignado"];
                    $almacenDetalleCubeta->referencia = $rsAlmacenDetalle["referencia"];
                    $almacenCubeta->almacenDetalleCubeta[$cont] = $almacenDetalleCubeta;
                    $cont++;
                }
            }
            echo json_encode($almacenCubeta);

        }

        $mensaje = 'Hecho';

} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine()]);
}
