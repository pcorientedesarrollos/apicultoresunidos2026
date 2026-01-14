<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
try {
    $externos = array();
    $sqlSelect = $con->prepare("SELECT aee.idAnalisisEncabezado, aee.fecha, t.tipoDeMiel, f.floracion, aee.numeroTambos, emx.nombre
    FROM analisisexternosencabezado aee
    LEFT JOIN empresasexternas emx ON emx.idExterno = aee.idExterno
    LEFT JOIN tiposdemiel t ON t.idTipoDeMiel = aee.tipoMiel
    LEFT JOIN floraciones f ON f.idFloracion = aee.idFloracion
    ORDER BY aee.fecha DESC, aee.idAnalisisEncabezado DESC");
    $sqlSelect->execute();
    if ($sqlSelect == false) {
        throw new Exception($con->errorInfo());
    }
    $externos = $sqlSelect->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['error' => false, 'externos' => $externos]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
