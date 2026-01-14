<?php
date_default_timezone_set("America/Merida");
function main($con, $post)
{

    $idBanco = $post->idBanco;
    $idCuenta = $post->idCuenta;
    $saldoInicial = $post->saldoInicial;

    $_replaceSigns = ['$', ',', 'M', 'X','N', 'm', 'x', 'n', ' '];
    $saldoInicial = str_replace($_replaceSigns, '', $saldoInicial);

    $fecha = $post->fecha;
    $idMes = $post->idMes;
    $hora = date("H:i:s");
    $referencia = 'SALDO INICIAL DE LA CUENTA';
    $nombreDe = '';
    $descripcion = 'SALDO INICIAL';
    $tipoDepositoCompra = '';
    $tiposMovimiento = 0;
    $insertarSaldoInicialCuenta = $con->prepare("INSERT INTO auxiliardebancos (idBanco, idCuenta, fecha, idMes, hora, referencia, nombreDe, descripcion, tipoDepositoCompra, cantidad, tipoMovimiento) 
    VALUES (:idBanco, :idCuenta, :fecha, :idMes, :hora, :referencia, :nombreDe, :descripcion, :tipoDepositoCompra, :cantidad, :tipoMovimiento)");
    $insertarSaldoInicialCuenta->bindParam(':idBanco', $idBanco);
    $insertarSaldoInicialCuenta->bindParam(':idCuenta', $idCuenta);
    $insertarSaldoInicialCuenta->bindParam(':fecha', $fecha);
    $insertarSaldoInicialCuenta->bindParam(':idMes', $idMes);
    $insertarSaldoInicialCuenta->bindParam(':hora', $hora);
    $insertarSaldoInicialCuenta->bindParam(':referencia', $referencia);
    $insertarSaldoInicialCuenta->bindParam(':nombreDe', $nombreDe);
    $insertarSaldoInicialCuenta->bindParam(':descripcion', $descripcion);
    $insertarSaldoInicialCuenta->bindParam(':tipoDepositoCompra', $tipoDepositoCompra);
    $insertarSaldoInicialCuenta->bindParam(':cantidad', $saldoInicial);
    $insertarSaldoInicialCuenta->bindParam(':tipoMovimiento', $tiposMovimiento);
    $insertarSaldoInicialCuenta->execute();
    if ($insertarSaldoInicialCuenta->rowCount() == 1) {
        echo json_encode(['error'=>false, 'message'=>'Saldo inicial: ' .'$' . number_format($saldoInicial, 2, '.', ','), 'swal'=>'success']);
    } else {
        echo json_encode(['error'=>true, 'message'=>'Ocurrió un error', 'swal'=>'error']);
    }
};

$post = json_decode(file_get_contents('php://input'));
if ($post) {
    include_once '../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();
    main($con, $post);
} else {
    exit();
}
