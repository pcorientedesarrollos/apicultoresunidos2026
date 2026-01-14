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
            $res = mysql_query("SELECT MAX(idRequisicion) AS id FROM requisicionencabezado");
            if ($rs = mysql_fetch_row($res)) {
                $id = trim($rs[0]);
                $sqlTotales = "SELECT SUM(totalTambores) as sumaTambores,
        (SELECT SUM(totalKilos)FROM requisicionencabezado) as sumaKilos,
        (SELECT SUM(importeTotal) FROM requisicionencabezado) as sumaTotal
        FROM requisicionencabezado";
                $datosTotales = $con->prepare($sqlTotales);
                $datosTotales0->execute();
                if ($datosTotales == false) {
                    echo mysql_error();
                } else {
                    $req = new stdClass();
                    while ($resTotal = $datosTotales->fetch()) {
                        $reqTotales = new stdClass();
                        $reqTotales->idRequisicion = $rs[0];
                        $reqTotales->fechaRequisicion = $rs[0];
                        $reqTotales->fechaImpresion = $rs[0];
                        $reqTotales->nombre = $rs[0];
                        $reqTotales->sumaTambores = $resTotal["sumaTambores"];
                        $reqTotales->precio = $rs[0];
                        $reqTotales->sumaKilos = $resTotal["sumaKilos"];
                        $reqTotales->sumaTotal = $resTotal["sumaTotal"];
                        $reqTotales->importe = $rs[0];
                        $reqTotales->banco = $rs[0];
                        $reqTotales->observaciones = $rs[0];

                        $requerimiento[] = $reqTotales;
                    }
                }
            }
        }
    }
}
echo json_encode($requerimiento);
?>