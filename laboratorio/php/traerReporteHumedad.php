<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $conexion = $pdo->conectar();
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$data = file_get_contents('php://input');
$response = array();
try {
    if(!$data) {
        throw new Exception('No se han recibido parámetros');
    } else {
        $datos = json_decode($data);
        if(!isset($datos->folioInicial) || !isset($datos->folioFinal) || !isset($datos->tipoDeMiel)){
            throw new Exception('No se han recibido parámetros');
        }
        $folioInicial = $datos->folioInicial;
        $folioFinal = $datos->folioFinal;
        $tipoDeMiel = $datos->tipoDeMiel;
    }
    switch($tipoDeMiel){
        case '1':
            $almacen_tabla = 'almacen';
            $almacenencabezado_tabla = 'almacenencabezado';
        break;
        case '2':
            $almacen_tabla = 'almacen_organico';
            $almacenencabezado_tabla = 'almacenencabezado_organico';
        break;
        default: 
            throw new Exception('Tipo de miel no válido');
        break;
    }
    $sqlHumedadRep = "SELECT alen.fecha, al.idAlmacen, pr.nombre, lo.localidad
    FROM $almacenencabezado_tabla alen
    LEFT JOIN $almacen_tabla al     ON al.idAlmacenEncabezado = alen.idAlmacen
    LEFT JOIN proveedor pr   ON pr.idProveedor = alen.idProveedor
    LEFT JOIN direccion dr   ON dr.idDireccion = pr.idDireccion
    LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
    WHERE al.idAlmacen BETWEEN :idAlmacenInicial AND :idAlmacenFinal";

    $datosHumedadRep = $conexion->prepare($sqlHumedadRep);
    $datosHumedadRep->bindParam(':idAlmacenInicial', $folioInicial);
    $datosHumedadRep->bindParam(':idAlmacenFinal', $folioFinal);
    $datosHumedadRep->execute();
    if($datosHumedadRep == FALSE) {
        throw new Exception($conexion->errorInfo());
    }
    $response = $datosHumedadRep->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['error'=>false, 'data'=>$response]);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'data'=>$response]);
    exit();
}
