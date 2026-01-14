<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$id = $_GET["idAlmacen"];

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
    WHERE al.idAlmacen = :idAlmacen";
$datos = $con->prepare($sql);
$datos->bindParam(':idAlmacen', $idAlmacen);
$datos->execute();

$seleccionTambores = array();
if ($datos == false) {
    throw new Exception($cn->errorInfo());
}
while ($rs = $datos->fetch()) {
    $select = new stdClass();
    $select->fecha = $rs["fecha"];
    $select->idAlmacen = $rs["idAlmacen"];
    $select->proveedor = utf8_encode($rs["nombre"]);
    $select->idSagarpa = $rs["idSagarpa"];
    $select->localidad = $rs["localidad"];
    $select->bruto = $rs["bruto"];
    $select->tara = $rs["tara"];
    $select->neto = $rs["neto"];
    $select->porcentaje = $rs["porcentaje"];
    $select->sf = $rs["sfDescripcion"];
    $select->st = $rs["stDescripcion"];
    $select->adulteracionDescripcion = $rs["adulteracionDescripcion"];
    $select->hmf = $rs["hmf"];
    $select->resultado = $rs["resultado"];
    $select->idResultadoFinal = $rs["idResultadoFinal"];
    $seleccionTambores[] = $select;
}
echo json_encode($seleccionTambores);
