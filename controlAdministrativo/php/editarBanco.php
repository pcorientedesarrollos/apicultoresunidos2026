<?php
$datos = json_decode(file_get_contents("php://input"));
$info = $datos->valor;
include_once '../../DAOConeccion/conePDO.php';
$con = new conePDO();
$cn = $con->conectar();

try {
    $cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $cn->beginTransaction();

    // Actualizar banco

    $sqlActualizarBanco = $cn->prepare("UPDATE bancos SET banco = :banco WHERE idBanco = :idBanco");
    $sqlActualizarBanco->bindParam(':banco', $info[0]->banco);
    $sqlActualizarBanco->bindParam(':idBanco', $info[0]->idBanco);
    $sqlActualizarBanco->execute();
    if ($sqlActualizarBanco == false) {
        throw new Exception($cn->errorInfo());
    }

    // Actualizar cuentas o crear las nuevas

    foreach ($info[1] as $cuentaDelBanco) {
        if ($cuentaDelBanco->idCuenta > 0) {
            $sqlUpdateCuenta = $cn->prepare("UPDATE cuentasbancarias SET numDeCuenta = :numDeCuenta, moneda = :moneda WHERE idCuenta = :idCuenta");
            $sqlUpdateCuenta->bindParam(':numDeCuenta', $cuentaDelBanco->numDeCuenta);
            $sqlUpdateCuenta->bindParam(':moneda', $cuentaDelBanco->moneda);
            $sqlUpdateCuenta->bindParam(':idCuenta', $cuentaDelBanco->idCuenta);
            $sqlUpdateCuenta->execute();

            if ($sqlUpdateCuenta == false) {
                throw new Exception($cn->errorInfo());
            }

        } else {

            $sqlCuentas = $cn->prepare("INSERT INTO cuentasbancarias (numDeCuenta, idBanco, moneda) VALUES (:numDeCuenta, :idBanco, :moneda)");
            $sqlCuentas->bindParam(':numDeCuenta', $cuentaDelBanco->numDeCuenta);
            $sqlCuentas->bindParam(':idBanco', $info[0]->idBanco);
            $sqlCuentas->bindParam(':moneda', $cuentaDelBanco->moneda);
            $sqlCuentas->execute();

            if ($sqlCuentas == false) {
                throw new Exception($cn->errorInfo());
            }
        }
    }

    $cn->commit();
    echo json_encode(['error' => false, 'message' => 'Se han actualizado los datos']);
} catch (Exception $e) {
    $cn->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
