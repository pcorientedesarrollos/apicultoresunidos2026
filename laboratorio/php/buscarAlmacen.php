<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$id = $_GET["id"];

$sql = "SELECT * 
        FROM almacen al
        left JOIN laboratorio lab 
        on lab.idAlmacen = al.idAlmacen
        INNER JOIN almacenencabezado ale
        on ale.idAlmacen = al.idAlmacenEncabezado
        INNER JOIN proveedor pr 
        on pr.idProveedor = ale.idProveedor
        INNER JOIN direccion dr
        on pr.idDireccion = dr.idDireccion
        WHERE al.idAlmacen = :id";
$data = $con->prepare($sql);
$data->bindParam(':id', $id);
$data->execute();

if ($data == false) {
    echo mysql_error();
} else {
    if (mysql_affected_rows() == 0) {
        echo 0;
    } else {
        $almacen = new stdClass();
        while ($rs = $data->fetch()) {
            $almacen->porcentaje = $rs["porcentaje"];
            $almacen->sf = $rs["sf"];
            $almacen->st = $rs["st"];
            $almacen->c13 = $rs["c13"];
            $almacen->hmf = $rs["hmf"];
            $almacen->resultadoFinal = $rs["resultadoFinal"];
            $almacen->marcaInterna = $rs["marcaInterna"];
            $almacen->estadoLab = $rs["estado"];
            $almacen->idAlmacen = $rs["idAlmacen"];
            $almacen->idProveedor = $rs["idProveedor"];
            $almacen->proveedor = $rs["nombre"];
            $almacen->idSagarpa = $rs["idSagarpa"];
            $almacen->fecha = $rs["fecha"];
            $almacen->almacenDetalle = array();
            $almacen->idAlmacen = $rs[0];
            $almacen->idAlmacenEncabezado = $rs["idAlmacenEncabezado"];
            $almacen->zona = $rs["zona"];
            $almacen->trazabilidad = $rs["trazabilidad"];
            $almacen->pesoLista = $rs["pesoLista"];
            $almacen->bruto = $rs["bruto"];
            $almacen->tara = $rs["tara"];
            $almacen->neto = $rs["neto"];
            $almacen->diferencia = $rs["diferencia"];
            $almacen->estado = $rs["autorizado"];
            if ($rs["idLaboratorio"] == null) {
                $almacen->idLaboratorio = 0;
            } else {
                $almacen->idLaboratorio = $rs["idLaboratorio"];
            }
        }
        echo json_encode($almacen);
    }
}