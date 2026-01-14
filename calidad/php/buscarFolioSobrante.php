<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

// $id = $_GET["id"];

$sql = "SELECT alms.fecha, alms.consecutivo, alms.sobrante AS clasificacion, alms.bruto, alms.tara, alms.neto, s.nombre, '1' AS tipo,
        ls.porcentaje, ls.sf, ls.st, ls.adulteracionDescripcion, ls.hmf, rs.resultado, rs.idResultadoFinal, fl.idFloracion, fl.floracion
        FROM almacensobrantes alms 
        LEFT JOIN sobrantes s ON s.idSobrante = alms.sobrante
        LEFT JOIN laboratorio_sobrantes ls ON ls.idAlmacen = alms.consecutivo AND ls.sobrante = :sobrante AND ls.tipoDeMiel = :miel
        LEFT JOIN resultadofinal rs ON rs.idresultadoFinal = ls.resultadoFinal
        LEFT JOIN floraciones fl ON fl.idFloracion = ls.idFloracion
        WHERE alms.tipoDeMiel = :miel AND alms.estado = 0 AND alms.consecutivo = :folio AND alms.sobrante = :sobrante";
$datos = $con->prepare($sql);
$datos->bindParam(':miel', $_GET['miel']);
$datos->bindParam(':folio', $_GET['folio']);
$datos->bindParam(':sobrante', $_GET['clasificacion']);
$datos->execute();
$calidad = new stdClass();

if ($datos->rowCount() >= 1) {
    while ($rs = $datos->fetch()) {
        $calidad->fecha = $rs["fecha"];
        $calidad->idAlmacen = $rs["consecutivo"];
        $calidad->proveedor = $rs["nombre"];
        $calidad->idSagarpa = '--------';
        $calidad->localidad =  '--------';
        $calidad->bruto = $rs["bruto"];
        $calidad->tara = $rs["tara"];
        $calidad->neto = $rs["neto"];
        $calidad->porcentaje = $rs["porcentaje"];
        $calidad->sf = $rs["sf"];
        $calidad->st = $rs["st"];
        $calidad->adulteracionDescripcion = $rs["adulteracionDescripcion"];
        $calidad->hmf = $rs["hmf"];
        $calidad->resultado = $rs["resultado"];
        $calidad->idResultadoFinal = $rs["idResultadoFinal"];
        $calidad->idFloracion = $rs["idFloracion"];
        $calidad->floracion = $rs["floracion"];
        // $calidad->sobrante = $rs["sobrante"];
        $calidad->tipo = $rs["tipo"];
        $calidad->clasificacion = $rs["clasificacion"];                                                                    
    }       
}else{
    $calidad = 0;
}

echo $json_response = json_encode($calidad);