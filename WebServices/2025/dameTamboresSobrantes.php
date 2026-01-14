<?php

header('Access-Control-Allow-Origin: *');
include_once '../DAOConeccion/conexionWebServices.php';
$pdo = new conePDO();
// $con = $pdo->conectar('apicultores2019');
// $con = $pdo->conectar('apicultores2020');
// $con = $pdo->conectar('mielorganica2020');
// $con = $pdo->conectar('apicultores2021');
// $con = $pdo->conectar('mielorganica2021');
// $con = $pdo->conectar('apicultores2022');
// $con = $pdo->conectar('apicultores2023');
$con = $pdo->conectar('apicultores2024');
// $con = $pdo->conectar('mielorganica2022');
// $con = $pdo->conectar('mielorganica2023');


try {
    $resultado = array();
    $sqlImpresion = "SELECT idImpresion, idAlmacen FROM impresion WHERE estado = 5";
    $dato = $con->prepare($sqlImpresion);
    $dato->execute();
    if ($dato == false) {
        throw new Exception($con->errorInfo());
    } else {
        foreach ($dato->fetchAll(PDO::FETCH_ASSOC) as $impresion) {

            $sql = "SELECT tdm.tipoDeMiel, zt.nombre AS zona, als.bruto, als.tara, als.neto, sbr.nombre, CONCAT(UPPER(sbr.codigo), '-', als.tipoDeMiel, '-', als.consecutivo) AS consecutivo,
            CONCAT(UPPER(sbr.codigo), '-', als.tipoDeMiel, '-', als.consecutivo) AS folio,
            IF(als.lote IS NULL, 'N/A', als.lote) AS lote,  als.observaciones
            FROM almacensobrantes als
            LEFT JOIN tiposdemiel tdm ON als.tipoDeMiel = tdm.idTipoDeMiel
            LEFT JOIN sobrantes sbr ON als.sobrante = sbr.idSobrante
            INNER JOIN zonastambores zt ON zt.idZonaTambor = als.zona
            WHERE idEntradaSobrante = :idEntradaSobrante";
            $datos = $con->prepare($sql);
            $datos->bindParam(':idEntradaSobrante', $impresion['idAlmacen']);
            $datos->execute();

            if ($datos == false) {
                throw new Exception($con->errorInfo());
            }
            $datosSobrante = array_merge($datos->fetch(PDO::FETCH_ASSOC), $impresion);
            array_push($resultado, $datosSobrante);
        }
    }
    echo json_encode($resultado);
} catch (Exception $e) {
    echo json_encode([]);
}
