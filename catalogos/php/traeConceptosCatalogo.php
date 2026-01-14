<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$idConcepto = file_get_contents('php://input');
try {
    if (!isset($_GET['actualizado'])) {
        if (!$idConcepto) {
            throw new Exception('No se recibió el ID del concepto');
        }
        $conceptos = array();
        $sqlSeleccionaConceptos = $con->prepare("SELECT scc.idSubconceptoCC, scc.subconceptoCC, scc.precioUnitario, scc.peso, udm.nombre as nombreUnidad, scc.unidad
            FROM `subconceptoscajachica` scc
            LEFT JOIN unidadesdemedida udm ON scc.unidad = udm.idUnidad
            WHERE idConceptoCC = :idConcepto");
        $sqlSeleccionaConceptos->bindParam(':idConcepto', $idConcepto);
        $sqlSeleccionaConceptos->execute();
        if ($sqlSeleccionaConceptos == false) {
            throw new Exception($con->errorInfo());
        }
        $conceptos = $sqlSeleccionaConceptos->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['error' => false, 'conceptos' => $conceptos]);
    } else {
        if (!$idConcepto) {
            throw new Exception('No se recibió el ID de la subcuenta');
        }
        $conceptos = array();
        $sqlSeleccionaConceptos = $con->prepare("SELECT ss.*, udm.nombre as nombreUnidad
        FROM subsubcuentas ss
        LEFT JOIN unidadesdemedida udm ON ss.unidad = udm.idUnidad
        WHERE precio IS NOT NULL AND precio = 1 AND idSubcuenta = :idConcepto AND ss.ocultar = 0
        ORDER BY ss.clave ASC");
        $sqlSeleccionaConceptos->bindParam(':idConcepto', $idConcepto);
        $sqlSeleccionaConceptos->execute();
        if ($sqlSeleccionaConceptos == false) {
            throw new Exception($con->errorInfo());
        }
        $conceptos = $sqlSeleccionaConceptos->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['error' => false, 'conceptos' => $conceptos]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
