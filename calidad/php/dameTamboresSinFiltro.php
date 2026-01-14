<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = "SELECT ale.fecha, al.idAlmacen, al.estado, pr.nombre, pr.idSagarpa, l.localidad, al.bruto, al.tara,
                al.neto, lab.porcentaje, lab.st, lab.porcentajeDescripcion, lab.sfDescripcion,
                lab.stDescripcion, lab.adulteracionDescripcion, lab.hmf, lab.procesoDescripcion, lab.resultadoFinal,
                rs.resultado, rs.idResultadoFinal
    FROM        almacen al
    INNER JOIN  laboratorio lab ON lab.idAlmacen = al.idAlmacen
    LEFT JOIN   almacenencabezado ale ON  ale.idAlmacen = al.idalmacenEncabezado
    LEFT JOIN   resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal
    LEFT JOIN   proveedor pr ON pr.idProveedor = ale.idProveedor
    LEFT JOIN   direccion dir ON dir.idDireccion = pr.idDireccion
    LEFT JOIN   localidades l ON l.idlocalidad = dir.idlocalidad
    WHERE al.estado = 0";
$datos = $con->prepare($sql);
$datos->execute();

$tamboresSinFiltro = array();
if ($datos == false) {
    throw new Exception($cn->errorInfo());
}
while ($rs = $datos->fetch()) {
    $tambor = new stdClass();
    $tambor->fecha = $rs["fecha"];
    $tambor->idAlmacen = $rs["idAlmacen"];
    $tambor->proveedor = utf8_encode($rs["nombre"]);
    $tambor->idSagarpa = $rs["idSagarpa"];
    $tambor->localidad = $rs["localidad"];
    $tambor->bruto = $rs["bruto"];
    $tambor->tara = $rs["tara"];
    $tambor->neto = $rs["neto"];
    $tambor->porcentaje = $rs["porcentaje"];
    $tambor->sf = $rs["sfDescripcion"];
    $tambor->st = $rs["stDescripcion"];
    $tambor->adulteracionDescripcion = $rs["adulteracionDescripcion"];
    $tambor->hmf = $rs["hmf"];
    $tambor->resultado = $rs["resultado"];
    $tambor->idResultadoFinal = $rs["idResultadoFinal"];
    $tamboresSinFiltro[] = $tambor;
}
echo json_encode($tamboresSinFiltro);
