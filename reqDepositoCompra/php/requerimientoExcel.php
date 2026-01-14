<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$requerimiento = array();

$sql = "SELECT re.idRequisicion, re.fechaRequisicion, re.fechaImpresion, p.nombre, rd.noTambores, rd.precio, rd.peso, rd.importe, rd.banco, rd.observaciones
FROM requisiciondetalle rd INNER JOIN requisicionencabezado re ON re.idRequisicion = rd.idRequisicion
INNER JOIN proveedor p ON p.idProveedor = rd.idProveedor";
$data = $con->prepare($sql);
$data->execute();

if ($data == false) {
    echo mysql_error();
} else {
    if (mysql_affected_rows() == 0) {
        echo 0;
    } else {

        while ($rs = $data->fetch()) {
            $req = new stdClass();
            $req->idRequisicion = $rs[0];
            $req->fechaRequisicion = $rs["fechaRequisicion"];
            $req->fechaImpresion = $rs["fechaImpresion"];
            $req->nombre = $rs["nombre"];
            $req->noTambores = $rs["noTambores"];
            $req->precio = $rs["precio"];
            $req->peso = $rs["peso"];
            $req->importe = $rs["importe"];
            $req->banco = $rs["banco"];
            $req->observaciones = $rs["observaciones"];

            $requerimiento[] = $req;
        }
        echo json_encode($requerimiento);
    }
}
?>