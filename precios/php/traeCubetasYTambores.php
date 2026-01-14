<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$id = 0;
$id = $_GET["idProveedor"];

$sql = "SELECT DISTINCT (al.idAlmacen), al.fecha, pr.nombre, lo.localidad, al.totalCompra
FROM almacenencabezado al 
LEFT JOIN proveedor pr 
ON pr.idProveedor = al.idProveedor
LEFT JOIN almacen alm
on al.idAlmacen = alm.idAlmacenEncabezado
LEFT JOIN direccion dr 
on pr.idDireccion = dr.idDireccion
LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
UNION
SELECT ce.idAlmacen, ce.fecha, rp.nombre, ol.localidad, ce.totalCompra
FROM cubetasencabezado ce 
LEFT JOIN proveedor rp 
ON rp.idProveedor = ce.idProveedor
LEFT JOIN cubetasdetalle cd 
ON ce.idAlmacen = cd.idAlmacenEncabezado
LEFT JOIN direccion rd 
ON rp.idDireccion = rd.idDireccion
LEFT JOIN localidades ol ON ol.idlocalidad = rd.idlocalidad
WHERE ce.folioEntradaTambor = 99999
ORDER BY fecha DESC";
$datos = $con->prepare($sql);
$datos->execute();
if ($datos == false) {
    throw new Exception('No se recibieron parámetros');
} else {
    $array = array();
    $cont = 0;
    while ($rs = $datos->fetch()) {
        $cubetaTambor = new stdClass();
        $cubetaTambor->fecha = $rs["fecha"];
        $cubetaTambor->id = $rs[0];
        $cubetaTambor->proveedor = $rs["nombre"];
        $cubetaTambor->totalCompra = $rs["totalCompra"];
        $cubetaTambor->localidad = $rs["localidad"];
        $array[$cont] = $cubetaTambor;
        // $array = $cubetaTambor;
        $cont++;
    }
    echo json_encode($array);
}
?>