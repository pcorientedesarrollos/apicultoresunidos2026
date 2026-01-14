<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$cn = $pdo->conectar();

$json = file_get_contents("php://input");

try {

    if (!$json) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $info = json_decode($json);
    }

    $cn->beginTransaction();

    if(isset($info->idCuentaConcepto)){
        $sql = "UPDATE cuentas SET cuenta = :cuenta, ingresoEgreso = :ingresoEgreso WHERE idCuentaConcepto = :idCuentaConcepto";
        $dato = $cn->prepare($sql);
        $dato->bindParam(':cuenta', $info->cuenta);
        $dato->bindParam(':idCuentaConcepto', $info->idCuentaConcepto);
        $dato->bindParam(':ingresoEgreso', $info->ingresoEgreso);
        $dato->execute();
        if ($dato == false) {
            throw new Exception($cn - errorInfo());
        }

        if(isset($info->idInforme)){
            if($info->edicion){
                $sqlEliminarRelacion = $cn->prepare("DELETE FROM relacioncuentainformes WHERE idCuentaConcepto = :idCuentaConcepto");
                $sqlEliminarRelacion->bindParam(':idCuentaConcepto', $info->idCuentaConcepto);
                $sqlEliminarRelacion->execute();
                if ($sqlEliminarRelacion == false) {
                    throw new Exception($cn->errorInfo());
                } 
                foreach ($info->idInforme as $informes) {
                    $sqlTam = $cn->prepare("INSERT INTO relacioncuentainformes (idCuentaConcepto, idInforme) VALUES (:idCuentaConcepto, :idInforme)");
                    $sqlTam->bindParam(':idCuentaConcepto', $info->idCuentaConcepto);
                    $sqlTam->bindParam(':idInforme', $informes);  
                    $sqlTam->execute();
                    if ($sqlTam == false) {
                        throw new Exception($cn->errorInfo());
                    }          
                }
            }
        }

    }else{

        $sql = "INSERT INTO cuentas (cuenta, ingresoEgreso) VALUES (:cuenta, :ingresoEgreso)";
        $dato = $cn->prepare($sql);
        $dato->bindParam(':cuenta', $info->cuenta);
        $dato->bindParam(':ingresoEgreso', $info->ingresoEgreso);
        $dato->execute();

        if ($dato == false) {
            throw new Exception($cn - errorInfo());
        }
        $idCuenta = $cn->lastInsertId();

        if(isset($info->idInforme)){
            foreach ($info->idInforme as $informes) {
                $sqlTam = $cn->prepare("INSERT INTO relacioncuentainformes (idCuentaConcepto, idInforme) VALUES (:idCuentaConcepto, :idInforme)");
                $sqlTam->bindParam(':idCuentaConcepto', $idCuenta);
                $sqlTam->bindParam(':idInforme', $informes);  
                $sqlTam->execute();
                if ($sqlTam == false) {
                    throw new Exception($cn->errorInfo());
                }          
            }
        }
    
    }

    $cn->commit();
    echo json_encode(['error' => false, 'message' => 'Consulta realizada']);
} catch (Exception $e) {
    $cn->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
}
