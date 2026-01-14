<?php

if(isset($_GET['idCuenta'])){
    include_once '../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();

    $post = json_decode(file_get_contents('php://input'));

    $idCuenta = $_GET['idCuenta'];

    $seleccionaSaldo = $con->prepare("SELECT cantidad, tipoMovimiento FROM auxiliardebancos WHERE idCuenta = :idCuenta AND idMes = $post->idMes");
    $seleccionaSaldo->bindParam(':idCuenta', $idCuenta);
    $seleccionaSaldo->execute();
     $resultado = new stdClass();
     $saldoInicial = 0;    
    if ( $seleccionaSaldo->rowCount() >= 1 ) {
        foreach($seleccionaSaldo->fetchAll(PDO::FETCH_ASSOC) as $i){
            if($i['tipoMovimiento'] == 0 ){
                $saldoInicial += $i['cantidad'];
            }else{
                $saldoInicial -= $i['cantidad'];
            }
        }
        $resultado->saldoInicial = $saldoInicial;
     }else{
        $resultado->saldoInicial = 0.00;
     }







    // $seleccionaSaldo = $con->prepare("SELECT saldo FROM auxiliardebancos WHERE idCuenta = :idCuenta AND SUBSTR(fecha FROM 6 FOR 2 ) = $post->idMes ORDER BY idAuxiliar DESC LIMIT 1");
    // $seleccionaSaldo->bindParam(':idCuenta', $idCuenta);
    // $seleccionaSaldo->execute();
    // if($seleccionaSaldo->rowCount() == 1){
    //     $res = $seleccionaSaldo->fetch(PDO::FETCH_ASSOC);
    //     $resultado = new stdClass();
    //     $resultado->saldoInicial = $res['saldo'];
    // } else {
    //     $resultado->saldoInicial = 0.00;
    // }

    echo json_encode($resultado);
} else {
    exit();
}