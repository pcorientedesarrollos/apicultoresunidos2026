<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

function main($idMes)
{
    global $con;

    try {
        $seleccionarMovimientosMensual = $con->prepare("SELECT tipo, total FROM cajachica WHERE idMEs = :idMes");
        $seleccionarMovimientosMensual->bindParam(':idMes', $idMes);
        $seleccionarMovimientosMensual->execute();

        $saldoDelMes = 0;
        $saldoMes = new stdClass();

        foreach ($seleccionarMovimientosMensual->fetchAll(PDO::FETCH_ASSOC) as $movimientoDelMes) {
            $saldoDelMes = $movimientoDelMes['tipo'] == 0
            ?$saldoDelMes += $movimientoDelMes['total']
            :$saldoDelMes -= $movimientoDelMes['total'];
        }

        $saldoMes->elUltimoSaldo = $saldoDelMes;
        echo json_encode($saldoMes);
    } catch (PDOException $e) {
        error_log('ultimoSaldoCajaChica.php - idMes=' . $idMes . ' database=' . ($_SESSION['database'] ?? '(sin sesion)') . ' - ' . $e->getMessage());
        http_response_code(200);
        echo json_encode(['error' => true, 'message' => 'No se pudo obtener el saldo de caja chica']);
    }
}

$post = file_get_contents('php://input');
if ($post) {
    main( $post );
} else {
    exit();
}
