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
                        $encabezado = 'almacenencabezado';
                        $detalle = 'almacen';
                        break;
                    case '2':
                        $encabezado = 'almacenencabezado_organico';
                        $detalle = 'almacen_organico';
                        break;
                    case '5':
                        $encabezado = 'almacenencabezado_mantequilla';
                        $detalle = 'almacen_mantequilla';
                        break;
                    case '6':
                        $encabezado = 'almacenencabezado_altiplano';
                        $detalle = 'almacen_altiplano';
                        break;
                    case '7':
                        $encabezado = 'almacenencabezado_naranjo';
                        $detalle = 'almacen_naranjo';
                        break;
                    case '8':
                        $encabezado = 'almacenencabezado_aguacate';
                        $detalle = 'almacen_aguacate';
                        break;
                    case '9':
                        $encabezado = 'almacenencabezado_mezquite';
                        $detalle = 'almacen_mezquite';
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
        WHERE idAlmacen = :id
        ORDER BY idAlmacen";
        $datos = $con->prepare($sql);
        $datos->bindParam(':id', $id);
        $datos->execute();
        if ($datos == false){
            throw new Exception($con->errorInfo());
        }else {
            $almacen = new stdClass();
            while ($rs = $datos->fetch()) {
                $almacen->idAlmacen = $rs["idAlmacen"];
                $almacen->clasificacionMiel = $rs["clasificacionMiel"];
                $almacen->idProveedor = $rs["idProveedor"];
                $almacen->proveedor = $rs["nombre"];
                $almacen->idlocalidad = $rs["idlocalidad"];
                $almacen->localidad = $rs["localidad"];
                $almacen->idSagarpa = $rs["idSagarpa"];
                $almacen->fecha = $rs["fecha"];
                $almacen->folio = $rs["folio"];
                $almacen->totalCompra = $rs["totalCompra"];
                $almacen->almacenDetalle = array();
        
                $sqlAlmacenDetalle = "SELECT * FROM $detalle al
                LEFT JOIN laboratorio_organico lab ON lab.idAlmacen = al.idAlmacen
                WHERE idAlmacenEncabezado = :id
                GROUP BY al.idAlmacen
                oRDER BY al.idAlmacen ASC";
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
        
                $sqlTotales = "SELECT SUM(costoTotal) as costosTotales
                      FROM $detalle WHERE idAlmacenEncabezado = :id";
                $datosT = $con->prepare($sqlTotales);
                $datosT->bindParam(':id', $id);
                $datosT->execute();
                if ($datosT == false) {
                    echo mysql_error();
                } else {
                    while ($resTotal = $datosT->fetch()) {
                        $almacen->costosTotales = $resTotal["costosTotales"];
                    }
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
