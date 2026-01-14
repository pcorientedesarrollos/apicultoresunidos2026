<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

function main($idMes)
{
    global $con;
    
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
}

$post = file_get_contents('php://input');
if ($post) {
    main( $post );
} else {
    exit();
}
