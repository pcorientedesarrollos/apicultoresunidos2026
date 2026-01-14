<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();

$laboratorio = array();

$sql = "SELECT * , lab.estado, rs.resultado AS estadoLaboratorio
FROM almacen al
LEFT JOIN laboratorio lab ON lab.idAlmacen = al.idAlmacen
INNER JOIN almacenencabezado ale ON ale.idAlmacen = al.idAlmacenEncabezado
INNER JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
INNER JOIN direccion dr ON pr.idDireccion = dr.idDireccion
LEFT JOIN resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal";
$datos = $con->prepare($sql);
$datos->execute();

if ($datos == false) {
    echo mysql_error();
} else {
    if (mysql_affected_rows() == 0) {
        echo 0;
    } else {

        while ($rs = $datos->fetch()) {
            $almacen = new stdClass();
            $almacen->idAlmacen = $rs[0];
            $almacen->proveedor = $rs["nombre"];
            $almacen->porcentaje = $rs["porcentajeDescripcion"];
            $almacen->sf = $rs["sfDescripcion"];
            $almacen->st = $rs["stDescripcion"];
            $almacen->c13 = $rs["adulteracionDescripcion"];
            $almacen->hmf = $rs["procesoDescripcion"];
            $almacen->resultadoFinal = $rs["estadoLaboratorio"];
            $almacen->marcaInterna = $rs["marcaInterna"];
            $almacen->fecha = $rs["fecha"];
            $almacen->bruto = $rs["bruto"];
            $almacen->tara = $rs["tara"];
            $almacen->neto = $rs["neto"];
            if ($rs["estadoLaboratorio"] == null) {
                $almacen->estado = 0;
            } else {
                $almacen->estado = $rs["estadoLaboratorio"];
            }
            $laboratorio[] = $almacen;
        }
        echo json_encode($laboratorio);
    }
}