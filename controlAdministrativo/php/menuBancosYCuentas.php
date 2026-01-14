<?php

function main($con, $post){

      // Asegurar que el mes tenga formato de 2 dígitos (01, 02, ..., 08, ..., 12)
      $idMes = str_pad($post->idMes, 2, '0', STR_PAD_LEFT);

      $datos = $con->prepare("SELECT b.banco, cb.idCuenta, cb.numDeCuenta, ab.idBanco
      FROM auxiliardebancos ab
      INNER JOIN bancos b ON b.idBanco = ab.idBanco
      INNER JOIN cuentasbancarias cb ON cb.idCuenta = ab.idCuenta
      INNER JOIN meses m ON m.idMes = SUBSTR(ab.fecha FROM 6 FOR 2)
      WHERE m.idMes = :idMes GROUP BY ab.idCuenta ORDER BY b.banco");

      $datos->bindParam(':idMes', $idMes, PDO::PARAM_STR);
      $datos->execute();

      $arrayMenu = array();

    foreach($datos->fetchAll(PDO::FETCH_ASSOC) as $dato){
    $seleccionaElSaldo = $con->prepare("SELECT COALESCE(SUM(cantidad),0) AS saldoIngresos,
    (SELECT COALESCE(SUM(cantidad),0) FROM auxiliardebancos WHERE ingresoEgreso = 1 AND idCuenta = :idCuenta AND
               SUBSTR(fecha FROM 6 FOR 2) = :idMes) AS saldoEgresos,
(SELECT cantidad FROM auxiliardebancos WHERE tipoDePersona = 0 AND ingresoEgreso = 0 AND idCuenta = :idCuenta2 AND
               SUBSTR(fecha FROM 6 FOR 2) = :idMes2) AS saldoInicial
    FROM auxiliardebancos WHERE tipoDePersona != 0 AND ingresoEgreso = 0 AND idCuenta = :idCuenta3 AND
               SUBSTR(fecha FROM 6 FOR 2) = :idMes3");

       $seleccionaElSaldo->bindParam(':idCuenta', $dato['idCuenta'], PDO::PARAM_INT);
       $seleccionaElSaldo->bindParam(':idMes', $idMes, PDO::PARAM_STR);
       $seleccionaElSaldo->bindParam(':idCuenta2', $dato['idCuenta'], PDO::PARAM_INT);
       $seleccionaElSaldo->bindParam(':idMes2', $idMes, PDO::PARAM_STR);
       $seleccionaElSaldo->bindParam(':idCuenta3', $dato['idCuenta'], PDO::PARAM_INT);
       $seleccionaElSaldo->bindParam(':idMes3', $idMes, PDO::PARAM_STR);
       $seleccionaElSaldo->execute();

       $saldo = $seleccionaElSaldo->fetch(PDO::FETCH_ASSOC);
       $saldoIngresos = $saldo['saldoIngresos'] ?? 0;
       $saldoEgresos = $saldo['saldoEgresos'] ?? 0;
       $saldoInicial = $saldo['saldoInicial'] ?? 0;

       $dato['saldoInicial'] = $saldoInicial;
       $dato['saldoIngresos'] = $saldoIngresos;
       $dato['saldoEgresos'] = $saldoEgresos;

       $dato['saldoActual'] = $saldoInicial + $saldoIngresos - $saldoEgresos;

       array_push($arrayMenu, $dato);
   }

   echo json_encode($arrayMenu);
}


if(isset($_GET['traerCuentasDelmes'])){
    include_once '../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();
    $post = json_decode(file_get_contents('php://input'));
    main($con, $post);
} else {
    exit();
}