<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idLoteInterno = $_GET["idLoteInterno"];
//$array = array();

$sqlP = "SELECT lote FROM entradaysalida WHERE idLoteInterno = :idLoteInterno";
$dato = $con->prepare($sqlP);
$dato->bindParam(':idLoteInterno', $idLoteInterno);
$dato->execute();

if ($dato == false) {
    echo mysql_error();
} else {
    $tAnalisis = new stdClass();
    $tAnalisis->paso = 0;
    while ($rs = $dato->fetch()) {
        $tAnalisis->lote = $rs["lote"];
        $tAnalisis->paso = 1;
        $tAnalisis->detalleAnalisis = array();

        $sql = "SELECT  tex.idLoteInterno, pr.idSagarpa
                                 FROM tamboreslotes tex
                                 INNER JOIN almacen al ON al.idAlmacen = tex.folioTambor
                                 INNER JOIN almacenencabezado ale ON ale.idAlmacen = al.idAlmacenEncabezado
                                 INNER JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
                               WHERE tex.idLoteInterno = :idLoteInterno
                                 GROUP BY idSagarpa";
        $datos = $con->prepare($sql);
        $datos->bindParam(':idLoteInterno', $idLoteInterno);
        $datos->execute();
        $cont = 0;

        if ($datos == false) {
            echo mysql_error();
        } else {
            while ($rs = $datos->fetch()) {
                $detalleAnalisis = new stdClass();
                $detalleAnalisis->idSagarpa = $rs["idSagarpa"];
//                $array[] = $trazabilidadAnalisis;
                $tAnalisis->detalleAnalisis[$cont] = $detalleAnalisis;
                $cont++;
            }
        }
    }
    echo json_encode($tAnalisis);

//    if ($tAnalisis == null) {
//        echo $tAnalisis = 1;
//    } else {
//
//        echo json_encode($tAnalisis);
//    }
//    echo json_encode($array);
}
?>