<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idLoteInterno = $_GET["idLoteInterno"];
$tipo = $_GET['tipoMiel'];

if($tipo == '1'){
$tamboreslotes = 'tamboreslotes';
$almacen = 'almacen';
$almacenencabezado = 'almacenencabezado';
$calidad = 'calidad';
}else if($tipo == '2'){
    $tamboreslotes = 'tamboreslotes_organico';
    $almacen = 'almacen_organico';
    $almacenencabezado = 'almacenencabezado_organico';
$calidad = 'calidad_organico';

}else if($tipo == '5'){
    $tamboreslotes = 'tamboreslotes_mantequilla';
    $almacen = 'almacen_mantequilla';
    $almacenencabezado = 'almacenencabezado_mantequilla';
$calidad = 'calidad_mantequilla';

}else if($tipo == '6'){
    $tamboreslotes = 'tamboreslotes_altiplano';
    $almacen = 'almacen_altiplano';
    $almacenencabezado = 'almacenencabezado_altiplano';
$calidad = 'calidad_altiplano';

}else if($tipo == '7'){
    $tamboreslotes = 'tamboreslotes_naranjo';
    $almacen = 'almacen_naranjo';
    $almacenencabezado = 'almacenencabezado_naranjo';
$calidad = 'calidad_naranjo';

}else if($tipo == '8'){
    $tamboreslotes = 'tamboreslotes_aguacate';
    $almacen = 'almacen_aguacate';
    $almacenencabezado = 'almacenencabezado_aguacate';
$calidad = 'calidad_aguacate';

}else if($tipo == '9'){
    $tamboreslotes = 'tamboreslotes_mezquite';
    $almacen = 'almacen_mezquite';
    $almacenencabezado = 'almacenencabezado_mezquite';
$calidad = 'calidad_mezquite';

}

$sql = "SELECT tl.nombreLaboratorio, tl.fechaProtocolo, tl.folioProtocolo, tl.tipoMiel
                                 FROM trazabilidadlaboratorio tl 
                                WHERE tl.idLoteInterno = :idLoteInterno AND tl.tipoMiel = :tipo";
$datos = $con->prepare($sql);
$datos->bindParam(':idLoteInterno', $idLoteInterno);
$datos->bindParam(':tipo', $tipo);
$datos->execute();

if ($datos == false) {
        throw new Exception($con->errorInfo());
} else {
    while ($rs = $datos->fetch()) {
        $tAnalisis = new stdClass();
        $tAnalisis->nombreLaboratorio = $rs["nombreLaboratorio"];
        $tAnalisis->fechaProtocolo = $rs["fechaProtocolo"];
        $tAnalisis->folioProtocolo = $rs["folioProtocolo"];
        $tAnalisis->tipoMiel = $rs["tipoMiel"];
        $tAnalisis->detalleAnalisis = array();

        $sqlD = "SELECT ale.idAlmacen AS id, tex.idLoteInterno, pr.idSagarpa 
                   FROM $tamboreslotes tex
                   INNER JOIN $almacen al ON al.idAlmacen = tex.folioTambor
                   INNER JOIN $almacenencabezado ale ON ale.idAlmacen = al.idAlmacenEncabezado
                   INNER JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
                   WHERE tex.idLoteInterno = :idLoteInterno AND tex.tipo = '0'
                   GROUP BY idSagarpa
                UNION
                SELECT al.consecutivo AS id, tex.idLoteInterno, pr.idSagarpa
                    FROM $tamboreslotes tex
                    INNER JOIN almacensobrantes al ON al.consecutivo = tex.folioTambor AND al.sobrante = tex.clasificacion
                    INNER JOIN proveedor pr
                    WHERE tex.idLoteInterno = :idLoteInterno AND tex.tipo = '1' AND al.tipoDeMiel = :tipo AND pr.idProveedor = '83'
                    GROUP BY idSagarpa";
        $data = $con->prepare($sqlD);
        $data->bindParam(':idLoteInterno', $idLoteInterno);
        $data->bindParam(':tipo', $tipo);
        $data->execute();
        $cont = 0;
        if ($data == false) {
        throw new Exception($con->errorInfo());
    
        } else {
            while ($rs = $data->fetch()) {
                $detalleAnalisis = new stdClass();
                $detalleAnalisis->idSagarpa = $rs["idSagarpa"];
                $tAnalisis->detalleAnalisis[$cont] = $detalleAnalisis;
                $cont++;
            }
        }

//        $sqlMC = "SELECT lote 
//                                 FROM entradaysalida 
//                                WHERE idLoteInterno = :idLoteInterno";
        $sqlMC = "SELECT marcaFinalCliente 
                                 FROM $calidad 
                                WHERE idLoteInterno = :idLoteInterno";
        $respuesta = $con->prepare($sqlMC);
        $respuesta->bindParam(':idLoteInterno', $idLoteInterno);
        $respuesta->execute();
        if ($respuesta == false) {
        throw new Exception($con->errorInfo());
    
        } else {
            while ($rs = $respuesta->fetch()) {
//                $tAnalisis->lote = $rs["lote"];
                $tAnalisis->marcaFinalCliente = $rs["marcaFinalCliente"];
            }
        }
    }
    echo json_encode($tAnalisis);
}
