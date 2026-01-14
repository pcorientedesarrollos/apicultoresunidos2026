<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = "
    SELECT 
                ale.fecha, al.idAlmacen, pr.nombre, pr.idSagarpa, l.localidad, al.bruto, al.tara,
                al.neto, lab.porcentaje, lab.porcentajeDescripcion, lab.sfDescripcion,
                lab.stDescripcion, lab.adulteracionDescripcion, lab.hmf, lab.procesoDescripcion,
                rs.resultado, lab.resultadoFinal
    FROM        almacen al
    INNER JOIN  laboratorio lab ON lab.idAlmacen = al.idAlmacen
    LEFT JOIN   almacenencabezado ale ON  ale.idAlmacen = al.idalmacenEncabezado
    LEFT JOIN   resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal
    LEFT JOIN   proveedor pr ON pr.idProveedor = ale.idProveedor
    LEFT JOIN   direccion dir ON dir.idDireccion = pr.idDireccion
    LEFT JOIN   localidades l ON l.idlocalidad = dir.idlocalidad
    ORDER BY al.idAlmacen ASC";
$datos = $con->prepare($sql);
$datos->execute();

if ($datos == false) {
    echo "Error al ingresar";
} else {
    $arrayTamboresAnalizados = array();
    while ($rs = $datos->fetch()) {
        $tamborAnalizado = new stdClass();
        $tamborAnalizado->fecha = $rs["fecha"];
        $tamborAnalizado->folio = $rs["idAlmacen"];
        $tamborAnalizado->nombre = $rs["nombre"];
        $tamborAnalizado->idSagarpa = $rs["idSagarpa"];
        $tamborAnalizado->localidad = $rs["localidad"];
        $tamborAnalizado->bruto = $rs["bruto"];
        $tamborAnalizado->tara = $rs["tara"];
        $tamborAnalizado->neto = $rs["neto"];
        $tamborAnalizado->porcentaje = $rs["porcentaje"];
        $tamborAnalizado->porcentajeDescripcion = $rs["porcentajeDescripcion"];
        $tamborAnalizado->sfDescripcion = $rs["sfDescripcion"];
        $tamborAnalizado->stDescripcion = $rs["stDescripcion"];
        $tamborAnalizado->adulteracionDescripcion = $rs["adulteracionDescripcion"];
        $tamborAnalizado->hmf = $rs["hmf"];
        $tamborAnalizado->procesoDescripcion = $rs["procesoDescripcion"];
        $tamborAnalizado->resultado = $rs["resultado"];
        $arrayTamboresAnalizados [] = $tamborAnalizado;
    }
    echo json_encode($arrayTamboresAnalizados);
}
?>