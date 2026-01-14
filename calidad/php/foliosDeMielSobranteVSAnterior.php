<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$post = file_get_contents('php://input');

try {
    if (!$post) {
        throw new Exception('No se recibieron datos');
    } else {
        $parametros = json_decode($post);
    }
    $folios = array();
    $sqlSelect = $con->prepare("SELECT CONCAT(s.codigo, '-', a.consecutivo) AS folio, 
    CASE WHEN a.referencia IS NOT NULL THEN a.referencia ELSE '---' END AS referencia, 
    CASE WHEN a.lote IS NOT NULL THEN a.lote ELSE '---' END AS lote
        FROM almacensobrantes a
        LEFT JOIN sobrantes s ON s.idSobrante = a.sobrante
        WHERE a.tipoDeMiel = :miel AND a.sobrante = :sobrante 
        ORDER BY a.consecutivo ASC");
    $sqlSelect->bindParam(':miel', $parametros->miel);
    $sqlSelect->bindParam(':sobrante', $parametros->sobrante);
    $sqlSelect->execute();
    if ($sqlSelect == false) {
        throw new Exception($con->errorInfo());
    }
    $folios = $sqlSelect->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['error' => false, 'folios' => $folios]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
