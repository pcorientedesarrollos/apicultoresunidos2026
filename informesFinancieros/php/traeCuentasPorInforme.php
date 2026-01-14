<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    if (!isset($_GET['tipo'])) {
        throw new Exception('No se recibieron los parámetros esperados');
    } else {
        $tipo = $_GET['tipo'];
    }

    $sqlSeleccionaRelacion = $con->prepare("SELECT rci.idRelacion, rci.idInforme, inf.informeFinanciero, rci.orden, c.idCuentaConcepto, c.cuenta FROM cuentas c LEFT JOIN relacioncuentainformes rci ON c.idCuentaConcepto = rci.idCuentaConcepto 
    LEFT JOIN informesfinancieros inf ON inf.idInforme = rci.idInforme WHERE rci.idInforme = :informe");
    $sqlSeleccionaRelacion->bindParam(':informe', $tipo);
    $sqlSeleccionaRelacion->execute();
    $resultado = $sqlSeleccionaRelacion->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['error' => false, 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
