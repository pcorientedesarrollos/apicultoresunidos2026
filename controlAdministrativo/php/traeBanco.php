<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    if (!isset($_GET['idBanco'])) {
        throw new Exception('No se recibió el código del banco');
    } else {
        $paramBanco = $_GET['idBanco'];
    }

    $datos = $con->prepare("SELECT idBanco, banco FROM bancos WHERE idBanco = :idBanco");
    $datos->bindParam(':idBanco', $paramBanco);
    $datos->execute();
    if ($datos == false) {
        throw new Exception($con->errorInfo());
    }

    $resultadoBanco = $datos->fetch(PDO::FETCH_ASSOC);

    // Obtener las cuentas:
    $datosCuentas = $con->prepare("SELECT * FROM cuentasbancarias WHERE idBanco = :idBanco");
    $datosCuentas->bindParam(':idBanco', $resultadoBanco['idBanco']);
    $datosCuentas->execute();
    if ($datosCuentas == false) {
        throw new Exception($con->errorInfo());
    }
    $resultadoBanco['arregloCuentas'] = $datosCuentas->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($resultadoBanco);

} catch (Exception $e) {
    echo $e->getMessage();
}
