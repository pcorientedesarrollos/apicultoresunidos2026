<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idBanco = $_GET['idBanco'];

if(isset($_GET['idCuenta'])){
    $query = "SELECT idCuenta, numDeCuenta FROM cuentasbancarias WHERE idBanco = :idBanco AND idCuenta != :idCuenta";
    $datos = $con->prepare($query);
    $datos->bindParam(':idBanco', $idBanco);
    $datos->bindParam(':idCuenta', $_GET['idCuenta']);    
    $datos->execute();
    
    $arrayCuentas = array();
    while ($row = $datos->fetch()) {
        $cuentasC = new stdClass();
        $cuentasC->idCuenta = $row["idCuenta"];
        $cuentasC->numDeCuenta = $row["numDeCuenta"];
        $arrayCuentas[] = $cuentasC;
    }
echo $json_response = json_encode($arrayCuentas);

}else{
    $query = "SELECT idCuenta, numDeCuenta FROM cuentasbancarias WHERE idBanco = :idBanco";
    $datos = $con->prepare($query);
    $datos->bindParam(':idBanco', $idBanco);
    $datos->execute();
    
    $arrayCuentas = array();
    while ($row = $datos->fetch()) {
        $cuentasC = new stdClass();
        $cuentasC->idCuenta = $row["idCuenta"];
        $cuentasC->numDeCuenta = $row["numDeCuenta"];
        $arrayCuentas[] = $cuentasC;
    }
echo $json_response = json_encode($arrayCuentas);

}

# JSON-encode the response
?>