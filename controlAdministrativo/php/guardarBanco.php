<?php

$datos = json_decode(file_get_contents("php://input"));
$info = $datos->valor;
include_once '../../DAOConeccion/conePDO.php';
$con = new conePDO();
$cn = $con->conectar();
try {
    $cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $cn->beginTransaction();

    $sqlBanco = $cn->prepare("INSERT INTO bancos (banco) VALUES (:banco)");
    $sqlBanco->bindParam(':banco', $info[0]->banco);
    $sqlBanco->execute();
    if ($sqlBanco->rowCount() >= 1) {
        $idBanco = $cn->lastInsertId();
        foreach ($info[1] as $arregloCuentas) {
            $sqlCuentas = $cn->prepare("INSERT INTO cuentasbancarias (numDeCuenta, idBanco, moneda) VALUES (:numDeCuenta, :idBanco, :moneda)");
            $sqlCuentas->bindParam(':numDeCuenta', $arregloCuentas->numDeCuenta);
            $sqlCuentas->bindParam(':idBanco', $idBanco);
            $sqlCuentas->bindParam(':moneda', $arregloCuentas->moneda);
            $sqlCuentas->execute();
        }
    }
    $cn->commit();
    echo json_encode(['error' => false, 'message' => 'Nuevo banco disponible']);
} catch (Exception $e) {
    $cn->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}