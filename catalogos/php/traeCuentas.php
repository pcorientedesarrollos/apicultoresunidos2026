<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$postdata = file_get_contents('php://input');
try {

    if (!$postdata && $postdata != '0') {
        throw new Exception('No se recibieron los parámetros esperados');
    }

    $tipo = $postdata;
    
        $datos = $con->prepare('SELECT * FROM cuentas WHERE ingresoEgreso = :ingresoEgreso');
        $datos->bindParam(':ingresoEgreso', $tipo);
        $datos->execute();
        $resultado = array();
        if ($datos == false) {
            throw new Exception($con->errorInfo());
        }

        if(isset($_GET['informes'])){
            if ($datos->rowCount() >= 1) {
                foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $dato) {
                    $idCuenta = $dato['idCuentaConcepto'];
                    $sqlSeleccionaRelacion = $con->prepare("SELECT rci.idRelacion, rci.idInforme, inf.informeFinanciero, c.idCuentaConcepto, c.cuenta FROM cuentas c LEFT JOIN relacioncuentainformes rci ON c.idCuentaConcepto = rci.idCuentaConcepto 
                    LEFT JOIN informesfinancieros inf ON inf.idInforme = rci.idInforme WHERE c.idCuentaConcepto = :idCuentaConcepto");
                    $sqlSeleccionaRelacion->bindParam(':idCuentaConcepto', $idCuenta);
                    $sqlSeleccionaRelacion->execute();
                    $dato['idInforme'] = [];
                        foreach ($sqlSeleccionaRelacion->fetchAll(PDO::FETCH_ASSOC) as $data) {
                          array_push($dato['idInforme'], $data);
                        }
                    array_push($resultado, $dato);
                }
            }
        }else{
            $resultado = $datos->fetchAll(PDO::FETCH_ASSOC);
        }

    echo json_encode(['error' => false, 'data' => $resultado]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}